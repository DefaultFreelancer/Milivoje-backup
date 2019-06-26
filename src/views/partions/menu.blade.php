<li class="header">BACKUP MANAGEMENT</li>
<li class="{{ ! starts_with(Route::currentRouteName(), 'backupLimit.servers.users') ?: 'active' }}">
    <a href="{{ route('backupLimit.servers.users') }}">
        <i class="fa fa-users"></i> <span>User and Servers</span>
    </a>
</li>
<li class="{{ ! starts_with(Route::currentRouteName(), 'ssh.index.keys') ? : 'active' }}">
    <a href="{{ route('ssh.index.keys') }}">
        <i class="fa fa-server"></i> <span>SSH keys</span>
    </a>
</li>
