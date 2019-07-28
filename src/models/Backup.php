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
    protected $table = 'backups';

    public static function getBackupNum($server)
    {
        return self::where(['server_id' => $server])->get();
    }




}
