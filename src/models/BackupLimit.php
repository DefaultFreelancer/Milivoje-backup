<?php
/**
 * Created by PhpStorm.
 * User: miliv
 * Date: 6/18/2019
 * Time: 3:37 PM
 */


namespace ItVision\ServerBackup\Models;

use Illuminate\Support\Facades\DB;

class BackupLimit extends BaseModel
{
    protected $table = 'backup_limit';

    public $server_id;
    public $user_id;
    public $backup;

    public static function getBackupLimitNumber($server)
    {
        return self::where(['server_id' => $server])->first();
    }

    public static function create($server){
        $model = new self();
        $model->user_id = 0;
        $model->server_id = $server;
        $model->backup = 3;
        $model->save();
    }
}
