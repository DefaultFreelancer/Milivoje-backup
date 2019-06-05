<?php
/**
 * Created by PhpStorm.
 * User: default
 * Date: 6/5/19
 * Time: 10:48 AM
 */


Route::get('/backup', 'milivoje\backups\http\BackupController@index')->name('server.backup.index');
Route::post('/backup','milivoje\backups\http\BackupController@backup')->name('server.backup.save');
Route::get('/backup/download/{backup}', 'milivoje\backups\http\BackupController@download')->name('server.backup.download');
Route::get('/backup/delete/{backup}', 'milivoje\backups\http\BackupController@delete')->name('server.backup.delete');
