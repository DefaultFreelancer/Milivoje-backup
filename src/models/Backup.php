<?php
/**
 * Created by PhpStorm.
 * User: default
 * Date: 6/5/19
 * Time: 10:44 AM
 */

namespace ItVision\ServerBackup\Models;


use Illuminate\Support\Facades\DB;

class Backup extends BaseModel
{
    protected $table = ['backups'];
//    protected $fillable  = ['server_id','name','complete'];
//    public $server_id;
//    public $name;
//    public $complete;


    public static function getBackupNum($server)
    {
        return DB::table('backups')->where(['server_id' => $server])->get();
    }




}
