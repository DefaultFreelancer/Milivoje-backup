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

            <div class="col-sm-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Keys - {{ count($keys) }}</h3>
                        <div class="box-tools">
{{--                            <form action="{{ route('server.backup.save', [ $server->id ]) }}" method="POST">--}}
{{--                                @csrf--}}
{{--                                @method('POST')--}}
{{--                                <button class="btn btn-primary btn-sm" type="submit">Create Backup</button>--}}
{{--                            </form>--}}
                        </div>
                    </div>
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover">
                            <tbody>
                            <tr>
                                <th class="middle text-center">ID</th>
                                <th class="middle text-center">Name</th>
                                <th class="middle text-center">Key</th>
                                <th class="middle text-center">Action</th>
                            </tr>
                            @foreach($keys as $key)
                                <tr>
                                    <td class="middle text-center">
                                        {{ $key->id }}
                                    </td>
                                    <td class="middle text-center">
                                        {{ $key->name }}
                                    </td>
                                    <td class="middle text-center">
                                        {{ $key->custom_echo($key->key, 60)}}
                                    </td>
                                    <td style="display: flex;">
                                        <div style="margin: 3px;"><a href="{{ url('admin/updateKey/'.$key->id) }}" class="btn btn-facebook"><span class="fa fa-edit"></span></a></div>

                                        <form id="inUse" method="POST" style="margin: 3px;" action="{{ route('ssh.inuse.key', $key->id) }}">
                                            {{ csrf_field() }}
                                            <div>
                                                <button type="submit" class="btn btn-{{ $key->inUse ? "default" : "success" }}">
                                                @if($key->inUse)
                                                    <span class="fa fa-minus-circle"></span>
                                                @else
                                                    <span class='fa fa-plus-circle'></span>
                                                @endif
                                                </button>
                                            </div>
                                        </form>

                                        <form id="delete-form" style="margin: 3px;" method="POST" action="{{ route('ssh.delete.key', $key->id) }}">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}

                                            <div>
                                                <button class="btn btn-danger" type="submit">
                                                    <span class="fa fa-trash"></span>
                                                </button>
                                            </div>
                                        </form>

                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">

                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Create new key</h3>
                    </div>

                    <form action="{{ route('ssh.new.key') }}" method="post">
                        @csrf
                        <div class="box-body">

                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" placeholder="Name" name="name" class="form-control"/>
                            </div>

                            <div class="form-group">
                                <label>SSH Key</label>
                                <textarea name="key" rows="6" placeholder="Key" class="form-control"></textarea>
                            </div>

                            <button type="submit" class="btn btn-success">Submit</button>

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
