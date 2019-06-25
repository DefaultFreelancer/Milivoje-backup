<?php
/**
 * Created by PhpStorm.
 * User: miliv
 * Date: 6/19/2019
 * Time: 11:37 AM
 */

namespace ItVision\ServerBackup\models;


class ServerSshKeys extends BaseModel
{
    protected $table = 'server_ssh_keys';




    public function custom_echo($x, $length)
    {
        if(strlen($x)<=$length)
        {
            echo $x;
        }
        else
        {
            $y=substr($x,0,$length) . '...';
            echo $y;
        }
    }
}
