@if (isset($server->name) && isset($node->name))
<li class="header">BACKUP MANAGEMENT</li>
<li class="{{ ! starts_with(Route::currentRouteName(), 'server.backups.client') ?: 'active' }}">
    <a href="{{ route('server.backups.client', $server->uuidShort) }}">
        <i class="fa fa-hdd-o"></i> <span>Backups</span>
    </a>
</li>
@endif
