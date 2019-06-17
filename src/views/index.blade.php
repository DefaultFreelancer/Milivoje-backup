@extends('layouts.master')

@section('title')
    Backup Manager
@endsection

@section('content-header')
    <h1>Backup Manager<small>Take a full backup of your server files</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('index') }}">Home</a></li>
        <li><a href="{{ route('server.index', $server->uuidShort) }}">{{ $server->name }}</a></li>
        <li>Backup Manager</li>
        <li class="active">List Backups</li>
    </ol>
@endsection

@section('content')
    <div class="content">
        <div class="row">
            <div class="col">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Backups - {{$backupcount}}</h3>
                        <div class="box-tools">
                            <form action="{{ route('server.backup.save', [ $server->uuidShort ]) }}" method="POST">
                                @csrf
                                @method('POST')
                            <button class="btn btn-primary btn-sm" type="submit">Create Backup</button>
                            </form>
                        </div>
                    </div>
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover">
                            <tbody>
                            <tr>
                                <th class="middle text-center">Status</th>
                                <th class="middle text-center">Date</th>
                                <th class="middle text-center">Download</th>
                                <th class="middle text-center">Delete</th>
                            </tr>
                            @foreach($backups as $backup)
                                <tr>
                                    <td class="middle text-center">
                                        @if($backup->complete === 0)
                                            <i class='fa fa-refresh fa-spin fa-fw' aria-hidden='true'></i>
                                        @else
                                            <i class='fa fa-download fa-fw' aria-hidden='true'></i>
                                        @endif
                                    </td>
                                    <td class="middle text-center"><code>{{$backup->created_at}}</code></td>
                                    <td class="middle text-center">
                                            <a href="{{ $backup->complete === 0 ? "#" :  route('server.backup.download', [ $server->uuidShort, $backup->id ])  }}" type="button" class="btn btn-success btn-sm {{ $backup->complete === 0 ? "disabled" : "" }}">
                                                Download
                                            </a>
                                    </td>
                                    <td class="middle text-center">
                                        <a href="{{ $backup->complete === 0 ? "#" :  route('server.backup.delete', [ $server->uuidShort, $backup->id ])  }}" type="button" class="btn btn-danger btn-sm {{ $backup->complete === 0 ? "disabled" : "" }}">
                                            Delete
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
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
