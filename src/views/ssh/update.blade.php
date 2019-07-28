@extends('layouts.admin')

@section('title')
    Backup Manager
@endsection

@section('content-header')
    <h1>SSH Keys Manager</h1>
    <ol class="breadcrumb">
        <li>SSH Keys Manager</li>
        <li class="active">List of Keys</li>
    </ol>
@endsection

@section('content')
    <div class="content">
        <div class="row">

            <div class="col-sm-5">

                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Update key</h3>
                    </div>

                    <form action="{{ route('ssh.update.key', $key->id) }}" method="post">
                        @csrf
                        {{ method_field('PUT') }}
                        <div class="box-body">

                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" placeholder="Name" name="name" value="{{ $key->name }}" class="form-control"/>
                            </div>

                            <div class="form-group">
                                <label>Key</label>
                                <textarea name="key" rows="6" placeholder="Key" class="form-control">{{ $key->key }}</textarea>
                            </div>

                            <a href="{{ url('admin/sshKeys') }}" class="btn btn-default">Back</a>
                            <button type="submit" class="btn btn-success">Update</button>

                        </div>
                    </form>

                </div>

            </div>

        </div>
    </div>
@endsection

@section('footer-scripts')
    @parent
    {!! Theme::js('js/frontend/server.socket.js') !!}
    {!! Theme::js('vendor/jquery/date-format.min.js') !!}
    {!! Theme::js('vendor/chartjs/chart.min.js') !!}
    {!! Theme::js('vendor/jquery/jquery.min.js') !!}
    {!! Theme::js('vendor/sweetalert/sweetalert.min.js') !!}
@endsection
