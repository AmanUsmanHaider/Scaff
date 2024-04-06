<div class="sidebar sidebar-dark sidebar-fixed" id="sidebar">
    <div class="sidebar-brand d-none d-md-flex">
        <h5 class="mb-0">{{!empty(session('currentModule.0')) ? ucwords(session('currentModule.0')) : ''}}</h5>
    </div>
    <ul class="sidebar-nav" data-coreui="navigation" data-simplebar="">
        <li class="nav-item">
            <a class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}" href="{{route('admin.dashboard')}}">
                <i class="nav-icon cil-speedometer"></i> Dashboard
            </a>
        </li>
        @canany(['admin_user-management_permission-group-list','admin_user-management_permission-create','admin_user-management_role-list','admin_user-management_user-list'])
            <li class="nav-title">User Management</li>
            @can('admin_user-management_module-list')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/module*') ? 'active' : '' }}" href="{{route('admin.module.index')}}">
                        <i class="nav-icon cil-cursor"></i> Module
                    </a>
                </li>
            @endcan
            @can('admin_user-management_permission-group-list')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/permission-group*') ? 'active' : '' }}" href="{{route('admin.permission-group.index')}}">
                        <i class="nav-icon cil-cursor"></i> Permission Group
                    </a>
                </li>
            @endcan
            @can('admin_user-management_permission-create')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/permissions*') ? 'active' : '' }}" href="{{route('admin.permissions.index')}}">
                        <i class="nav-icon cil-cursor"></i> Permission
                    </a>
                </li>
            @endcan
            @can('admin_user-management_role-list')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/role*') ? 'active' : '' }}" href="{{route('admin.role.index')}}">
                        <i class="nav-icon cil-cursor"></i> Role
                    </a>
                </li>
            @endcan
            @can('admin_user-management_user-list')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/user*') ? 'active' : '' }}" href="{{route('admin.user.index')}}">
                        <i class="nav-icon cil-cursor"></i> User
                    </a>
                </li>
            @endcan
        @endcanany
        @canany(['admin_user-management_backup-list','admin_user-management_log-dashboard','admin_user-management_log-list'])
            <li class="nav-title">Backup Management</li>
            @can('admin_user-management_backup-list')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/backup*') ? 'active' : '' }}" href="{{route('admin.backup.index')}}">
                        <i class="nav-icon cil-cursor"></i> Backup
                    </a>
                </li>
            @endcan
            @canany(['admin_user-management_log-dashboard','admin_user-management_log-list'])
                <li class="nav-group">
                    <a class="nav-link nav-group-toggle" href="#">
                        <i class="nav-icon cil-cursor"></i> Logs
                    </a>
                    <ul class="nav-group-items">
                        @can('admin_user-management_backup-list')
                            <li class="nav-item"><a class="nav-link {{ request()->is('admin/log-viewer') ? 'active' : '' }}" href="{{route('log-viewer::dashboard')}}"><span class="nav-icon"></span> Logs Dashboard</a></li>
                        @endcan
                        @can('admin_user-management_backup-list')
                            <li class="nav-item"><a class="nav-link {{ request()->is('admin/log-viewer/logs') ? 'active' : '' }}" href="{{ route('log-viewer::logs.list') }}"><span class="nav-icon"></span> Logs By Day</a></li>
                        @endcan
                    </ul>
                </li>
            @endcanany
        @endcanany
    </ul>
    <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
</div>
