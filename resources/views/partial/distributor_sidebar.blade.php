<nav id="sidebar">
    <div class="navbar-nav theme-brand flex-row  text-center">
        <div class="nav-logo">
            <div class="nav-item theme-logo">
                <a href="{{route('distributor.dashboard')}}">
                    <img src="{{ asset('storage/' . $site_settings['favicon']) }}" class="navbar-logo" width="50">
                </a>
            </div>
            <div class="nav-item theme-text">
                <a href="{{route('distributor.dashboard')}}" class="nav-link"> {{ $site_settings['application_name'] }}
                </a>
            </div>
        </div>
        <div class="nav-item sidebar-toggle">
            <div class="btn-toggle sidebarCollapse">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-chevrons-left">
                    <polyline points="11 17 6 12 11 7"></polyline>
                    <polyline points="18 17 13 12 18 7"></polyline>
                </svg>
            </div>
        </div>
    </div>
    <div class="shadow-bottom"></div>
    <ul class="list-unstyled menu-categories" id="accordionExample">
        @if (Auth::guard('distributor')->check())
        <li class="menu">
            <div class="dropdown-toggle user-details">
                <div class="avatar avatar-sm avatar-online">
                    <img alt="avatar" src="{{ asset('storage/' . Auth::guard('distributor')->user()->image) }}"
                        class="rounded-2 profile-img">
                </div>
                <div class="user_info">
                    <h6 class="mb-0">{{ Auth::guard('distributor')->user()->name }}</h6>
                    <span class="text-secondary fs--2 d-flex">
                        <i class="fa-duotone fa-phone me-0"></i>
                        {{ Auth::guard('distributor')->user()->mobile }}
                    </span>
                </div>
            </div>
        </li>
        @endif
        <li class="menu @routeis('distributor.dashboard') active @endrouteis">
            <a href="{{route('distributor.dashboard')}}" aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <i class="fa-duotone fa-house"></i>
                    <span>Dashboard</span>
                </div>
            </a>
        </li>

        <li class="menu @routeis('distributor.profile') active @endrouteis">
            <a href="{{ route('distributor.profile') }}" aria-expanded="false" class="dropdown-toggle">
                <div>
                    <i class="fa-duotone fa-user"></i>
                    <span>Profile </span>
                </div>
            </a>
        </li>
        <li class="menu @routeis('distributor.wallet') active @endrouteis">
            <a href="{{ route('distributor.wallet') }}" aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <i class="fa-duotone fa-wallet"></i>
                    <span>My Wallet</span>
                </div>
            </a>
        </li>
        <li class="menu">
            <a role="banner" href="" aria-expanded="false" class="dropdown-toggle" type="button" data-bs-toggle="modal"
                data-bs-target="#loadMoney">
                <div>
                    <i class="fa-duotone fa-sack-dollar"></i>
                    <span>Load Money</span>
                </div>
            </a>
        </li>

        <li class="menu @routeis('distributor.request-money') active @endrouteis">
            <a href="{{ route('distributor.request-money') }}" aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <i class="fa fa-inr"></i>
                    <span>Request Money</span>
                </div>
            </a>
        </li>

        <li class="menu @routeis('distributor.retailers') active @endrouteis">
            <a href="{{ route('distributor.retailers') }}" aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <i class="fa-solid fa-sparkles"></i>
                    <span>Retailers</span>
                </div>
            </a>
        </li>

        <li class="menu @routeis('distributor.lock') active @endrouteis">
            <a href="{{ route('distributor.lock') }}" aria-expanded="false" class="dropdown-toggle">
                <div>
                    <i class="fa-duotone fa-lock"></i>
                    <span>Lock Screen</span>
                </div>
            </a>
        </li>
        <li class="menu @routeis('distributor.logout') active @endrouteis">
            <a href="{{ route('distributor.logout') }}"
                onclickDisabled="event.preventDefault(); document.getElementById('logout-form').submit();"
                aria-expanded="false" class="dropdown-toggle">
                <div>
                    <i class="fa-regular fa-arrow-right-from-bracket"></i>
                    <span>Log Out</span>
                    <form id="logout-form" action="{{ route('distributor.logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </a>
        </li>


    </ul>
</nav>