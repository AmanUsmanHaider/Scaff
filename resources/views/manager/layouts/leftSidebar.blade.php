<div class="sidebar sidebar-dark sidebar-fixed" id="sidebar">
    <div class="sidebar-brand d-none d-md-flex">
        <h5 class="mb-0">{{!empty(session('currentModule.0')) ? ucwords(session('currentModule.0')) : ''}}</h5>
    </div>
    <ul class="sidebar-nav" data-coreui="navigation" data-simplebar="">
        <li class="nav-item">
            <a class="nav-link {{ request()->is('manager/dashboard') ? 'active' : '' }}" href="{{route('manager.dashboard')}}">
                <i class="nav-icon cil-speedometer"></i> Dashboard
            </a>
        </li>
    </ul>
    <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
</div>
