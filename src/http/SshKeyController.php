<?php


namespace ItVision\ServerBackup\http;


use Illuminate\Support\Facades\Auth;
use ItVision\ServerBackup\models\ServerSshKeys;
use Prologue\Alerts\AlertsMessageBag;
use Pterodactyl\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SshKeyController extends Controller
{

    public function __construct(
        AlertsMessageBag $alert
    ) {
        $this->alert = $alert;
    }

    public function keys()
    {
        return view('backup::ssh.index', ['keys' => ServerSshKeys::get()]);
    }


    public function newKey(Request $request)
    {
        $model = new ServerSshKeys();
        $model->name = $request['name'];
        $model->key  = $request['key'];
        $model->user_id = Auth::user()->id;
        $model->save();

        $this->alert->success('You have created new ssh key!')->flash();
        return redirect()->back();
    }


    public function updateKey($key)
    {
        return view('backup::ssh.update', ['key' => ServerSshKeys::find($key)]);
    }

    public function update(Request $request, $keyId)
    {
        $model = ServerSshKeys::find($keyId);
        $model->name = $request['name'];
        $model->key = $request['key'];

        if($request['inUse'] != ""){
            $keys = ServerSshKeys::get();
            foreach ($keys as $key){
                $key->inUse = 0;
                $key->save();
            }
            $model->inUse = 1;
        }

        $model->save();

        $this->alert->success('You updated ssh key!')->flash();
        return redirect('admin/sshKeys');
    }


    public function delete(Request $request, $id)
    {
        $model = ServerSshKeys::find($id);
        $model->delete();

        $this->alert->success('You deleted ssh key!')->flash();
        return redirect()->back();
    }


    public function inUse(Request $request, $id)
    {
        $keys = ServerSshKeys::get();
        foreach ($keys as $key){
            $key->inUse = 0;
            $key->save();
        }

        $key_ = ServerSshKeys::find($id);
        $key_->inUse = 1;
        $key_->save();

        $this->alert->success('Key '.$key_->name.' is now in use')->flash();

        return redirect()->back();
    }
}
