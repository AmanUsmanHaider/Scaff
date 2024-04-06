<div class="sidebar sidebar-dark sidebar-fixed" id="sidebar">
    <div class="sidebar-brand d-none d-md-flex">
        <h5 class="mb-0">{{!empty(session('currentModule.0')) ? ucwords(session('currentModule.0')) : ''}}</h5>
    </div>
    <ul class="sidebar-nav" data-coreui="navigation" data-simplebar="">
        <li class="nav-item">
            <a class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}" href="{{route('academic.dashboard')}}">
                <i class="nav-icon cil-speedometer"></i> Dashboard
            </a>
        </li>
            <li class="nav-title">User Management</li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/module*') ? 'active' : '' }}" href="{{route('academic.module.index')}}">
                        <i class="nav-icon cil-cursor"></i> Subjects
                    </a>
                </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('admin/module2*') ? 'active' : '' }}" href="{{route('academic.module2.index')}}">
                <i class="nav-icon cil-cursor"></i> Courses
            </a>
        </li>

    </ul>
    <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
</div>
