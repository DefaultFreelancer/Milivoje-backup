<?php
/**
 * Created by PhpStorm.
 * User: miliv
 * Date: 6/19/2019
 * Time: 10:51 AM
 */

namespace Terminal;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Pterodactyl\Models\Server;

class InitBackupLimit extends Command
{

    protected $signature = "InitBackupLimit";
    protected $description = "This command will fulfil all limits for every server, and this has to be done only once. First time after installation of this package.";


    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        try{
            foreach (Server::get() as $server){
                DB::table('backup_limit')->insert(['server_id' => $server->id, 'backups' => 3]);
            }
        } catch (\Exception $e){
            print_r($e);
            return false;
        }
        print_r('Success!');
    }


}
