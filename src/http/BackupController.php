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
    /**
     * ConsoleController constructor.
     *
     * @param \Illuminate\Contracts\Config\Repository $config
     */
    public function __construct(ConfigRepository $config, AlertsMessageBag $alert)
    {
        $this->config = $config;
        $this->alert = $alert;
    }

    /**
     * Render server index page with the console and power options.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request, $server): View
    {
        $serverReq = $request->attributes->get('server');
//        $serverReq = Server::where(['uuidShort' => $server])->first();

//        if($serverReq)
            $this->setRequest($request)->injectJavascript([
                'server' => [
                    'cpu' => $serverReq->cpu,
                ],
                'meta' => [
                    'saveFile' => route('server.files.save', $serverReq->uuidShort),
                    'csrfToken' => csrf_token(),
                ],
                'config' => [
                    'console_count' => $this->config->get('pterodactyl.console.count'),
                    'console_freq' => $this->config->get('pterodactyl.console.frequency'),
                ],
            ]);

        try{
            $server = Server::where(['uuidShort' => $server])->first();
            $backups = Backup::where(['server_id' => $server->id])->get();
        } catch (\Exception $e){
            return redirect()->back()->with('Error', 'There is no server!');
        }

        return view('backup::index', [
            'backups' => $backups,
            'backupcount' => count($backups),
            'server'    => $server
        ]);
    }


    public function backup(Request $request, $server)
    {
        $server = Server::find($server);
        $backups = Backup::where(['server_id' => $server->id])->get();
        $backupLimit = BackupLimit::where(['server_id' => $server->id])->first();

        if(count($backups) >= $backupLimit->backups){
            $this->alert->fail('You are not allowed to create more backups!')->flash();
            return redirect()->back();
        };

        error_reporting(E_ALL);
        ini_set('max_execution_time', 1560);


        // SSH connection

        $key = new RSA();
        $sshKey = ServerSshKeys::where(['inUse' => 1])->first();
        if(!$sshKey){
            $sshKey = ServerSshKeys::first();
        }
        $key->loadKey('PuTTY-User-Key-File-2: ssh-rsa
Encryption: none
Comment: root
Public-Lines: 6
AAAAB3NzaC1yc2EAAAABJQAAAQEArHNdkaZK/TmXb+6m3V2Np7VYp+38s/DfdX7m
TnttfgbkygznWZnN1YQaHfoTq8o1bTCqiQaH2BmtgNha24MIU5iRUaIUNUaHhSr6
UBZt/QK11ySVg9Uz20l384gc0pPQeIVurqnGTyXD+1PriZiJirt48Be4a/iqSjkD
c8DyIA/FZe9PlPQ50HgX1U4v8EmROYkrSrFK7czhAQdAu0WFilaXajPYpgARevxp
fQUvuLE1ILC0ea6Ad4DPQGW+XIW+H9pZ1e9XPyEZuyjKXp6Co+E8qMZaV42faIHq
ieqv0RMoZ7/vLbBPnhKv0sB1XcwDaL50WFTX2Qd9SBhrS37/nw==
Private-Lines: 14
AAABACVJYFbX2NzOLpS9+pC6SCRC4ryGe9PhDbiKI/VLHpfKI6FO5encENQ4PP+Q
BEDRxlzMQIxUcGYTY8jU1V/k+uGkcnKD6LGStYSTq75KCfGEC6QjlfL9qljtSl4r
RICOqZ2Eok9HTXjlkiiA2Psn5mMvdBg8eubpqEdmRe+DqfkXzoZ0B+aRzW+JpIRV
g6VsUcuH/8V5GtMoksA7oU8UqJOUVzOR/4FytEUGpFDcvTPWsqR6qPS1BgYxA4Uw
bUhHQl0x2yDDGWs2LfYqILspjfYhuL5FXgDMQEG6ra09bh0d95vU1tepvQiTQHvM
O4bhqyCJsBuhGkqcgjQ6fQC7oa0AAACBAPi5Es4g7o4UbexhQUcVGR+fyLnc7okq
9kVhWd6aOgmjcWa38Tc9vyBoY0vP1QlTKZunJgeu9U7YJlbfoRxks2aJ2EisjxVm
dZLv5STX9yYm5pFvC2+WlQE0rmaov47Hx99EhFIVuo9qJZs2yLVkx+2I7RBbsvbW
UB8UycoGif2/AAAAgQCxfwPc6aNVmC2SkAFn/ht3TGtyfHKlsjr05pmYE0vwPFcd
Y79jsY5vU1pltzM7VcafQNNfXvP1wrfIGMSoiYjEpvt7sdnsSNDyOMDOJQcr6p60
yboX/Uf1A9Qgn+8DwqCv8oM7coS17EQKX2csqTBeaVSVfNjHaaOEAcAfIZE2IQAA
AIB8KBlFzL5eVctAzCiHvroZwFtn2bWh5KVgQbpAbEya85tVuVWrVUy15w7pY6tv
SzzDJFj3lr3zmj/bBPkE166cTqNmUdg7Pv7aVtxok2CHM1P3E6FRzpccbyX+DIKi
5kXEZAaXvlTWNH5H4T4zP31yv31lkmTUmOQCh6YRZ5JV4g==
Private-MAC: 470500fba51e3d08bbc32ed0d2a2ba140231b3cd
');

        // Domain can be an IP too
        $ssh = new SSH2($server->node->fqdn, 697);

        echo "<pre>";
        print_r($server->node->fqdn);
        print_r(!$ssh->login('root', $key));
        die;
        if (!$ssh->login('root', $key)) {
            exit('Connection Failed');
        }

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
        $backup->serverid = $server->id;
        $backup->complete = 1;

        $ssh->exec('mkdir -p '.$backupslocation);
        $ssh->exec('cd '.$backupslocation.' && nohup tar -czvf '.$backup->name.' '.$gamelocation);

        $backup->save();
        $this->alert->success('Your server is being backed up. Please check back later.')->flash();
        return redirect()->route('server.backup.index', $server->uuidShort);
    }


    public function download(Request $request, $server, $backupid)
    {
        $server = Server::where(['uuidShort' => $server])->first();
        $backup = Backup::find($backupid);

        return redirect('server/'.$server->uuidShort.'/files/download/backups/'.$backup->name);
    }


    public function delete(Request $request, $server, $backupid)
    {
//        $server = $request->attributes->get('server');
//        $backup = Backup::where('id', '=', $backupid)->get();
//        Backup::where('id', '=', $backupid)->delete();
//
//        error_reporting(E_ALL);
//        ini_set('max_execution_time', 360);
//        $key = new RSA();
//        $key->loadKey('PuTTY-User-Key-File-2: ssh-rsa
//Encryption: none
//Comment: root@vps591074
//Public-Lines: 6
//AAAAB3NzaC1yc2EAAAADAQABAAABAQC7mYDW5XjngjUfc0OFU2gCJzxpLEkDxOVi
//doh/ktoDiYIwRe04V4sH5vd9k7ZWzbnH8RU4c1Gd3oDGfcvjP+2yy0uO2nmiTja+
//3BMArZcJh3dqV8oNRCVj1/cISmWDYBXYeAp68lks0hQNUYdCaQjLtKctYhR84JJB
//IbE+OqkzJuxPiyeDmXezcb6EbR1EypHOjMWoEyG2qwNUt83wmJXYk2rUi7Gm7r5D
//mr5zYemNRZzIc1bp8jaWTTgrzrFRwWeIC7Hpe/EpVnWp/osGGPHVCQLx6PR3Sv82
//7F7AKQ75kIZWUWAf9lKGf5QMloFWlSGm2Ydski0RNVRGhQ8NoKR9
//Private-Lines: 14
//AAABAQC6Y4do+9GmYu4Y81KQsw/Ro94XuNJKlmQ58f8okWVewk7BW5iXtBMEpOwc
//rpgponkFOHiW/6yFp75WeEIM5UVrsSS/KD5VjlRCSTHwKIi6BQgWdmbfy2dCzh44
//9IHrh5ns20e+Y+9J4ufdW7WBvA3rJvA63QnyEMX/RFRvuaTGlh6oisOzh4PuoHW3
//FtqIoGUkmH5iIcomG+GXzOJk4tz24nU9uNIRomtpxgnbngpcNhrep0Im0vx5zNXR
//PL4Sz9BG8Xzq4lpTbP4x5Zf76Y86459wVY4L2kTo0V7blfcxFMu+VN1X6nHv777H
//xGmpaujO/poHganQVIa+bmsbuESBAAAAgQDtpr3A/KirGJI86QmHxb0LIZPBr5BP
//AGr2WMy0biTnXkTSm+fr6n/5wJTq+FJhTGtipeKsc4D83BhMUj7D3q5rcB2F+OF4
//yOxgWtz3fikE5CkilSNgqA1EMX7CK1bs2p8TbHei10AV0GOjeanswt9Ce45xiCSz
//GMYi0eqS0sHrTQAAAIEAyhV5HVgCN9uHsoR3OkEqlM1EnZ0oDs9rguynddDXpWmd
//qG0sTwRGtW33qoHc2LlAOzOQj3vjEBTE5ZEh+OJej5ZOu0B428J2Y/nrUjbCkQbp
//WabAiR41ltayTCE+ynVj9tw6rWfBn6DJG0/c1zO1C5SZQ73mRcZmX9gdXG5QJfEA
//AACBAMt9DRbbOwvtKl57vQKiQAmWz0qd8m8yFvnWOdC7RenVz59bwQu3ApxO/gdB
///9f9/dOvAv3GO0E2qDdLPKhPFrYohYiT5azGwxfpBdl/n8uS7uFmA3OrnyJ7lgFy
//jiO6j+An9FzTqjEuvKztQz4ZGCJ+b1u9MGvNFc4sapiAj5Xc
//Private-MAC: 6c736ecef67cbdb0d6ff01dcfff1b602f77b9fbb
//');
//
//        // Domain can be an IP too
//        $ssh = new SSH2($server->node->fqdn, 697);
//        if (!$ssh->login('root', $key)) {
//            exit('Connection Failed');
//        }
//        $backuplocation = "/srv/daemon-data/" . $server->uuid . "/backups/".$backup[0]['name'];
//
//        $ssh->exec('rm -rf '.$backuplocation);
//        Backup::where('id', '=', $backupid)->delete();
//
//        return redirect()->route('server.backup.index', $server->uuidShort);
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
