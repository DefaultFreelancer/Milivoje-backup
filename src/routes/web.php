<?php
/**
 * Created by PhpStorm.
 * User: default
 * Date: 6/5/19
 * Time: 10:48 AM
 */

Route::group(['middleware' => ['web', 'csrf', 'auth', 'server', 'subuser.auth', 'node.maintenance']], function (){
    Route::group(['namespace' => 'ItVision\ServerBackup\http', 'prefix' => "server/{server}"], function (){
        Route::get('/backup', 'BackupController@index')->name('server.backups.client');
        Route::post('/backup','BackupController@backup')->name('server.backup.save');
        Route::get('/backup/download/{backup}', 'BackupController@download')->name('server.backup.download');
        Route::get('/backup/delete/{backupId}', 'BackupController@delete')->name('server.backup.delete');
    });
});

Route::group(['namespace' => 'ItVision\ServerBackup\http', 'prefix' => 'admin/', 'middleware' => ['web','auth']], function (){
    Route::get('backupsLimit/users', 'BackupController@usersView')->name('backupLimit.servers.users');
    Route::get('backupsLimit/user/{user}', 'BackupController@backupLimit');
    Route::post('backupLimit/change/server/{server}', 'BackupController@backupLimitChange')->name('backupLimit.change');

    Route::get('sshKeys', 'SshKeyController@keys')->name('ssh.index.keys');
    Route::post('new_sshKeys', 'SshKeyController@newKey')->name('ssh.new.key');
    Route::get('updateKey/{id}', 'SshKeyController@updateKey');
    Route::put('update_ssh/{keyId}', 'SshKeyController@update')->name('ssh.update.key');
    Route::delete('delete/{id}', 'SshKeyController@delete')->name('ssh.delete.key');
    Route::post('inUse/{id}', 'SshKeyController@inUse')->name('ssh.inuse.key');
});

