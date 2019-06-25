{{-- Pterodactyl - Panel --}}
{{-- Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com> --}}

{{-- This software is licensed under the terms of the MIT license. --}}
{{-- https://opensource.org/licenses/MIT --}}
@extends('layouts.admin')

@section('title')
    Manager User: {{ $user->username }}
@endsection

@section('content-header')
    <h1>{{ $user->name_first }} {{ $user->name_last}}<small>{{ $user->username }}</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li><a href="{{ route('admin.users') }}">Users</a></li>
        <li class="active">{{ $user->username }}</li>
    </ol>
@endsection

@section('content')
    <div class="row">

        <div class="col-md-6">

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">User Servers</h3>
                </div>

                <div class="box-body">

                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Number of backups</th>
                            <th>Number of backups limits</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($servers as $server)
                        <tr class="align-middle">
                            <td><code>{{ $server->id }}</code></td>
                            <td>{{ $server->name }}</td>
                            <td>{{ $server->description }}</td>
                            <td>{{ count($server->backups) }}</td>

                            <td>
                                <form method="post" action="{{ route('backupLimit.change', $server) }}">
                                    {!! csrf_field() !!}
                                    <input value="{{ $server->backupLimit }}" name="limit" class="form-control" style="width: 80px; float: left; margin-right: 3px;">
                                    <button class="btn btn-sm btn-primary">
                                        Update
                                    </button>
                                </form>
                            </td>

                        </tr>
                        @endforeach
                        </tbody>
                    </table>

                </div>

            </div>

        </div>

    </div>
@endsection

