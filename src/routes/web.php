<?php
/**
 * Created by PhpStorm.
 * User: default
 * Date: 6/5/19
 * Time: 10:48 AM
 */


Route::get('/backup', 'ItVision\ServerBackup\http\BackupController@index')->name('server.backup.index');
Route::post('/backup','ItVision\ServerBackup\http\BackupController@backup')->name('server.backup.save');
Route::get('/backup/download/{backup}', 'ItVision\ServerBackup\http\BackupController@download')->name('server.backup.download');
Route::get('/backup/delete/{backup}', 'ItVision\ServerBackup\http\BackupController@delete')->name('server.backup.delete');
