<?php
/**
 * Created by PhpStorm.
 * User: miliv
 * Date: 4/16/2019
 * Time: 4:40 PM
 */

namespace ItVision\ServerBackup\http;

use Exeption;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\Request;
use ItVision\ServerBackup\Models\Backup;
use ItVision\ServerBackup\Models\BackupLimit;
use ItVision\ServerBackup\models\ServerSshKeys;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;
use Pterodactyl\Http\Controllers\Controller;
use Pterodactyl\Models\Server;
use Pterodactyl\Models\User;
use Pterodactyl\Traits\Controllers\JavascriptInjection;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Prologue\Alerts\AlertsMessageBag;

class BackupController extends Controller
{
    use JavascriptInjection;

    /**
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;
    /**
     * @var \Prologue\Alerts\AlertsMessageBag
     */
    protected $alert;
    protected $sshKey;
    /**
     * ConsoleController constructor.
     *
     * @param \Illuminate\Contracts\Config\Repository $config
     */
    public function __construct(ConfigRepository $config, AlertsMessageBag $alert)
    {
        $this->config = $config;
        $this->alert = $alert;

        $sshKey = ServerSshKeys::where(['inUse' => 1])->first();
        if(!$sshKey)
            $sshKey = ServerSshKeys::first();

        $this->sshKey = $sshKey->key;
    }

    /**
     * Render server index page with the console and power options.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $server = $request->attributes->get('server');
        $this->setRequest($request)->injectJavascript([
            'server' => [
                'cpu' => $server->cpu,
            ],
            'meta' => [
                'saveFile' => route('server.files.save', $server->uuidShort),
                'csrfToken' => csrf_token(),
            ],
            'config' => [
                'console_count' => $this->config->get('pterodactyl.console.count'),
                'console_freq' => $this->config->get('pterodactyl.console.frequency'),
            ]
        ]);

        try{
            $backups = Backup::where(['server_id' => $server->id])->get();
        } catch (\Exception $e){
            return redirect()->back()->with('Error', 'There is no server!');
        }

        return view('backup::index', [
            'backups' => $backups,
            'backupsCount' => count($backups),
            'server'    => $server,
            'node'      => $server->node
        ]);
    }


    public function backup(Request $request)
    {
        $server = $request->attributes->get('server');
        $backups = Backup::where(['server_id' => $server->id])->get();
        $backupLimit = BackupLimit::where(['server_id' => $server->id])->first();

        if(count($backups) >= $backupLimit->backups){
            $this->alert->error('You are not allowed to create more backups!')->flash();
            return redirect()->back();
        };

        error_reporting(E_ALL);
        ini_set('max_execution_time', 1560);

        // SSH connection
        $key = new RSA();

        $key->loadKey($this->sshKey);
        $ssh = new SSH2($server->node->fqdn, 697);

        if(!$ssh->login('root', $key)) {
            print_r($ssh->getErrors());
            exit('Connection Failed');
        }

        sleep(5);
        $ssh->setTimeout(3);

        $gamelocation = "/srv/daemon-data/" . $server->uuid;
        if($server->egg_id == 40)
            $gamelocation = "/srv/daemon-data/" . $server->uuid ."/garrysmod/addons /srv/daemon-data/" .
                $server->uuid ."/garrysmod/data /srv/daemon-data/" . $server->uuid ."/garrysmod/gamemodes /srv/daemon-data/" .
                $server->uuid ."/garrysmod/lua /srv/daemon-data/" . $server->uuid ."/garrysmod/maps /srv/daemon-data/" .
                $server->uuid ."/garrysmod/materials /srv/daemon-data/" . $server->uuid ."/garrysmod/models /srv/daemon-data/" .
                $server->uuid ."/garrysmod/scripts /srv/daemon-data/" . $server->uuid ."/garrysmod/sounds /srv/daemon-data/" .
                $server->uuid ."/garrysmod/sv.db";

        $backupslocation = "/srv/daemon-data/" . $server->uuid . "/backups";

        $random = rand();
        $backup = new Backup;
        $backup->name = $random . '.tar.gz';
        $backup->server_id = $server->id;
        $backup->complete = 1;

        $ssh->exec('mkdir -p '.$backupslocation);
        $ssh->exec('cd '.$backupslocation.' && nohup tar -czvf '.$backup->name.' '.$gamelocation);

        $backup->save();
        $this->alert->success('Your server is being backed up. Please check back later.')->flash();
        return redirect()->back();
    }


    public function download(Request $request, $server, $backupid)
    {
        $backup = Backup::find($backupid);
        return redirect('server/'.$server.'/files/download/backups/'.$backup->name);
    }


    public function delete(Request $request,$server, $backupId)
    {
        $server = $request->attributes->get('server');
        $backup = Backup::find($backupId);

        error_reporting(E_ALL);
        ini_set('max_execution_time', 1560);

        $key = new RSA();
        $key->loadKey($this->sshKey);

        // Domain can be an IP too
        $ssh = new SSH2($server->node->fqdn, 697);
//        if (!$ssh->login('root', $key)) {
//            exit('Connection Failed');
//        }
        $ssh->login('root', $key);
        $backuplocation = "/srv/daemon-data/" . $server->uuid . "/backups/".$backup->name;

        $ssh->exec('rm -rf '.$backuplocation);
        $backup->delete();

        return redirect()->back();
    }


    public function usersView(): View
    {
        $users = User::get();
        return view('backup::users', ['users' => $users]);
    }


    public function backupLimit($user): View
    {
        $servers = Server::where(['owner_id' => $user])->get();
        foreach($servers as $server){
            $num = BackupLimit::getBackupLimitNumber($server->id);
            $server->backups = Backup::getBackupNum($server->id);
            $server->backupLimit = $num ? $num->backups : 0;
        }

        return view('backup::singleUserServer', ['user' => User::find($user), 'servers' => count($servers) ? $servers : [] ]);
    }


    public function backupLimitChange(Request $request, $server)
    {
        $this->validate($request,['limit' => 'numeric|between:0,20']);
        DB::table('backup_limit')->where(['server_id' => $server])->update(['backups' => $request['limit']]);
        return redirect()->back()->with('success','Backup Limit updated!');
    }




}
