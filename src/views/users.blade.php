@extends('layouts.admin')
@section('title')
    Administration
@endsection

@section('content-header')
    <h1>User Servers Review</h1>
@endsection

@section('content')

    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">User List</h3>
                </div>
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Client Name</th>
                            <th>Username</th>
                            <th class="text-center"><span data-toggle="tooltip" data-placement="top" title="" data-original-title="Servers that this user is marked as the owner of.">Servers Owned</span></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                        <tr class="align-middle">
                            <td><code>{{ $user->id }}</code></td>
                            <td><a href="{{ (count($user->servers) ? url('admin/backupsLimit/user/'.$user->id) : "#") }}">{{ $user->email }}</a></td>
                            <td>{{ $user->name_first }}, {{ $user->name_last }}</td>
                            <td>{{ $user->username }}</td>
                            <td class="text-center">
                                <a href="{{ (count($user->servers) ? url('admin/backupsLimit/user/'.$user->id) : "#") }}">{{ count($user->servers) }}</a>
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
