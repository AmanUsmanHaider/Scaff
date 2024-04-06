<header class="header header-sticky mb-4">
    <div class="container-fluid">
        <button class="header-toggler px-md-0 me-md-3" type="button" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()">
            <i class="icon icon-lg cil-menu"></i>
        </button>
        <a class="header-brand d-md-none" href="#">
            <h5 class="mb-0">{{!empty(session('currentModule.0')) ? ucwords(session('currentModule.0')) : ''}}</h5>
        </a>
        <ul class="header-nav d-none d-md-flex">
            <li class="nav-item"><a class="nav-link" href="javascript:void(0)">{{(isset(auth()->user()->name) ? ucwords(auth()->user()->name) : '')}}</a></li>
        </ul>
        <ul class="header-nav ms-auto">
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="icon icon-lg cil-bell"></i>
                </a>
            </li>
        </ul>
        <ul class="header-nav ms-3">
            <li class="nav-item dropdown">
                <a class="nav-link py-0" data-coreui-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                    <div class="avatar avatar-md">
                        @if(!empty(auth()->user()->image))
                            <img class="avatar-img" src="{{route('admin.profile.get.image',auth()->user()->id)}}" alt="user@email.com">
                        @else
                            <img class="avatar-img" src="{{asset('admin/coreui')}}/assets/img/avatars/1.jpg" alt="user@email.com">
                        @endif
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end pt-0">
                    <div class="dropdown-header bg-light py-2">
                        <div class="fw-semibold">Account</div>
                    </div>
                    <a href="javascript:void(0)" class="dropdown-item" data-coreui-toggle="modal" data-coreui-target="#switch-modules" id="switch">
                        <i class="icon me-2 cil-reload"></i> Switch
                    </a>
                    <a class="dropdown-item" href="{{route('admin.profile',auth()->user()->id)}}">
                        <i class="icon me-2 cil-user"></i> Profile
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="javascript:void(0);" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="icon me-2 cil-account-logout"></i> Logout
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </a>
                </div>
            </li>
        </ul>
    </div>
    <div class="header-divider"></div>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb my-0 ms-2">
                <li class="breadcrumb-item">
                    <!-- if breadcrumb is single--><span>Home</span>
                </li>
                <li class="breadcrumb-item active"><span> {{(!empty($p_title) && isset($p_title)) ? $p_title : ''}}</span></li>
            </ol>
        </nav>
    </div>
</header>

<!-- Modal -->
<div class="modal fade" id="switch-modules" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Switch Module</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive" id="switch-table"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
