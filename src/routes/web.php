<?php
/**
 * Created by PhpStorm.
 * User: default
 * Date: 6/5/19
 * Time: 10:48 AM
 */


Route::get('/backup', 'milivoje\backup\http\BackupController@index')->name('server.backup.index');
Route::post('/backup','milivoje\backup\http\BackupController@backup')->name('server.backup.save');
Route::get('/backup/download/{backup}', 'milivoje\backup\http\BackupController@download')->name('server.backup.download');
Route::get('/backup/delete/{backup}', 'milivoje\backup\http\BackupController@delete')->name('server.backup.delete');
