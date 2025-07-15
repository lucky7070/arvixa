<nav id="sidebar">
    <div class="navbar-nav theme-brand flex-row text-center">
        <div class="nav-logo">
            <div class="nav-item theme-logo">
                <a href="{{route('dashboard')}}">
                    <img src="{{ asset('storage/' . $site_settings['favicon']) }}" class="navbar-logo" alt=""
                        width="50">
                </a>
            </div>
            <div class="nav-item theme-text">
                <a href="{{route('dashboard')}}" class="nav-link"> {{ $site_settings['application_name'] }} </a>
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
        <li class="menu @routeis('dashboard') active @endrouteis">
            <a href="{{route('dashboard')}}" aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <i class="fa-duotone fa-house"></i>
                    <span>Dashboard</span>
                </div>
            </a>
        </li>
        @if(userCan([102,103,104,105,106,112,116,124]))
        <li
            class="menu @routeis('roles,users,main_distributors,distributors,retailers,customers,services,employees,payment-modes') active @endrouteis">
            <a href="#master" data-bs-toggle="collapse"
                aria-expanded="{{ routeis('roles,users,main_distributors,distributors,retailers,customers,services,employees,payment-modes') }}"
                class="dropdown-toggle">
                <div class="">
                    <i class="fa-solid fa-sparkles"
                        aria-hidden="{{ routeis('roles,users,main_distributors,distributors,retailers,customers,services,employees,payment-modes') }}"></i>
                    <span>Master</span>
                </div>
                <div> <i class="fa-solid fa-chevron-right"></i> </div>
            </a>
            <ul class="collapse submenu list-unstyled @routeis('roles,users,main_distributors,distributors,retailers,customers,services,employees,payment-modes') show @endrouteis"
                id="master" data-bs-parent="#accordionExample">
                @if(userCan(102))
                <li class="@routeis('roles') active @endrouteis">
                    <a class="nav-link" href="{{ route('roles') }}" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Roles</span></div>
                    </a>
                </li>
                @endif
                @if(userCan(103))
                <li class="@routeis('users') active @endrouteis">
                    <a class="nav-link" href="{{ route('users') }}" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Sub Admins</span></div>
                    </a>
                </li>
                @endif
                @if(userCan(116))
                <li class="@routeis('employees') active @endrouteis">
                    <a class="nav-link" href="{{ route('employees') }}" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Employees</span>
                        </div>
                    </a>
                </li>
                @endif
                @if(userCan(104))
                <li class="@routeis('main_distributors') active @endrouteis">
                    <a class="nav-link" href="{{ route('main_distributors') }}" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Main Distributors</span>
                        </div>
                    </a>
                </li>
                @endif
                @if(userCan(105))
                <li class="@routeis('distributors') active @endrouteis">
                    <a class="nav-link" href="{{ route('distributors') }}" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Distributors</span>
                        </div>
                    </a>
                </li>
                @endif
                @if(userCan(106))
                <li class="@routeis('retailers') active @endrouteis">
                    <a class="nav-link" href="{{ route('retailers') }}" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Retailers</span></div>
                    </a>
                </li>
                @endif
                @if(userCan(112))
                <li class="@routeis('customers') active @endrouteis">
                    <a class="nav-link" href="{{ route('customers') }}" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Customers</span></div>
                    </a>
                </li>
                @endif
                @if(userCan(107))
                <li class="@routeis('services') active @endrouteis">
                    <a class="nav-link" href="{{ route('services') }}" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Services</span></div>
                    </a>
                </li>
                @endif

                @if(userCan(124))
                <li class="@routeis('payment-modes') active @endrouteis">
                    <a class="nav-link" href="{{ route('payment-modes') }}" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Payment Modes</span></div>
                    </a>
                </li>
                @endif

            </ul>
        </li>
        @endif
        @if(userCan([103,104,105,106]))
        <li class="menu @routeis('user-chart') active @endrouteis">
            <a href="{{route('user-chart')}}" aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <i class="fa-light fa-list-tree"></i>
                    <span>Users Tree</span>
                </div>
            </a>
        </li>
        @endif

        {{-- @if(userCan(109))
        <li class="menu @routeis('payment-request') active @endrouteis">
            <a href="{{route('payment-request')}}" aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <i class="fa fa-inr"></i>
                    <span>Payment Request</span>
                </div>
            </a>
        </li>
        @endif --}}


        @if(userCan([109]) || userCan([123]))
        <li class="menu @routeis('payment-request') active @endrouteis">
            <a href="#payment_request" data-bs-toggle="collapse" aria-expanded="{{ routeis('payment-request') }}"
                class="dropdown-toggle">
                <div class="">
                    <i class="fa fa-inr"></i>
                    <span>Payments</span>
                </div>
                <div> <i class="fa-solid fa-chevron-right"></i> </div>
            </a>
            <ul class="collapse submenu list-unstyled @routeis('payment-request') show @endrouteis" id="payment_request"
                data-bs-parent="#accordionExample">
                @if(userCan(109))
                <li class="@routeis('payment-request') active @endrouteis">
                    <a class="nav-link" href="{{ route('payment-request') }}" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Payment Request</span>
                        </div>
                    </a>
                </li>
                @endif
            </ul>
            <ul class="collapse submenu list-unstyled @routeis('upi-payment') show @endrouteis" id="payment_request"
                data-bs-parent="#accordionExample">
                @if(userCan(123))
                    <li class="@routeis('upi-payment') active @endrouteis">
                        <a class="nav-link" href="{{ route('upi-payment') }}" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1">UPI Payments</span>
                            </div>
                        </a>
                    </li>
                @endif
            </ul>
        </li>
        @endif

        @if(userCan([114,122,130]))
        <li class="menu @routeis('reports.pan-cards,reports.pan-cards,reports.electricity-bill,reports.water-bill,reports.gas-bill,reports.lic-bill,reports.itr-files') active @endrouteis">
            <a href="#reports" data-bs-toggle="collapse" aria-expanded="{{ routeis('reports.pan-cards,reports.pan-cards,reports.electricity-bill,reports.water-bill,reports.gas-bill,reports.lic-bill,reports.itr-files') }}"
                class="dropdown-toggle">
                <div class="">
                    <i class="fa-duotone fa-file-chart-column"></i>
                    <span>Reports</span>
                </div>
                <div> <i class="fa-solid fa-chevron-right"></i> </div>
            </a>
            <ul class="collapse submenu list-unstyled @routeis('reports.pan-cards,reports.pan-cards,reports.electricity-bill,reports.water-bill,reports.gas-bill,reports.lic-bill,reports.itr-files') show @endrouteis" id="reports"
                data-bs-parent="#accordionExample">
                @if(userCan(114))
                <li class="@routeis('reports.pan-cards') active @endrouteis">
                    <a class="nav-link" href="{{ route('reports.pan-cards') }}" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">PanCard Report</span>
                        </div>
                    </a>
                </li>
                @endif
                
                 @if(userCan(110))
                <li class="@routeis('reports.electricity-bill') active @endrouteis">
                    <a class="nav-link" href="{{ route('reports.electricity-bill') }}" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Electricity Bill Report</span>
                        </div>
                    </a>
                </li>
                @endif
                
                 @if(userCan(110))
                <li class="@routeis('reports.water-bill') active @endrouteis">
                    <a class="nav-link" href="{{ route('reports.water-bill') }}" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Water Bill Report</span>
                        </div>
                    </a>
                </li>
                @endif
                
                @if(userCan(110))
                <li class="@routeis('reports.gas-bill') active @endrouteis">
                    <a class="nav-link" href="{{ route('reports.gas-bill') }}" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Gas Bill Report</span>
                        </div>
                    </a>
                </li>
                @endif
                
                @if(userCan(110))
                <li class="@routeis('reports.lic-bill') active @endrouteis">
                    <a class="nav-link" href="{{ route('reports.lic-bill') }}" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">LIC Bill Report</span>
                        </div>
                    </a>
                </li>
                @endif

                @if(userCan(110))
                <li class="@routeis('reports.itr-files') active @endrouteis">
                    <a class="nav-link" href="{{ route('reports.itr-files') }}" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">ITR Files</span>
                        </div>
                    </a>
                </li>
                @endif
                
            </ul>
        </li>
        @endif

        @if(userCan(115))
        <li class="menu @routeis('emails') active @endrouteis">
            <a href="{{route('emails')}}" aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <i class="fa-duotone fa-envelopes"></i>
                    <span>Send Emails</span>
                </div>
            </a>
        </li>
        @endif

        @if(userCan(117))
        <li class="menu @routeis('notification') active @endrouteis">
            <a href="{{route('notification')}}" aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <i class="fa-duotone fa-paper-plane"></i>
                    <span>Send Notification</span>
                </div>
            </a>
        </li>
        @endif

        @if(userCan([118,113,119,108,120,121]))
        <li
            class="menu @routeis('sliders,testimonials,cms,faq,enquiries,admin-banners,join-requests') active @endrouteis">
            <a href="#static_content" data-bs-toggle="collapse"
                aria-expanded="{{ routeis('sliders,testimonials,cms,faq,enquiries,admin-banners,join-requests') }}"
                class="dropdown-toggle">
                <div class="">
                    <i class="fa-sharp fa-solid fa-photo-film"
                        aria-hidden="{{ routeis('sliders,testimonials,cms,faq,enquiries,admin-banners,join-requests') }}"></i>
                    <span>Content</span>
                </div>
                <div> <i class="fa-solid fa-chevron-right"></i> </div>
            </a>
            <ul class="collapse submenu list-unstyled @routeis('sliders,testimonials,cms,faq,enquiries,admin-banners,join-requests') show @endrouteis"
                id="static_content" data-bs-parent="#accordionExample">
                @if(userCan(118))
                <li class="@routeis('sliders') active @endrouteis">
                    <a class="nav-link" href="{{ route('sliders') }}" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Sliders</span></div>
                    </a>
                </li>
                @endif

                @if(userCan(113))
                <li class="@routeis('admin-banners') active @endrouteis">
                    <a class="nav-link" href="{{ route('admin-banners') }}" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Admin Banners</span>
                        </div>
                    </a>
                </li>
                @endif

                @if(userCan(119))
                <li class="@routeis('testimonials') active @endrouteis">
                    <a class="nav-link" href="{{ route('testimonials') }}" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Testimonials</span>
                        </div>
                    </a>
                </li>
                @endif

                @if(userCan(108))
                <li class="@routeis('cms') active @endrouteis">
                    <a class="nav-link" href="{{ route('cms') }}" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">CMS</span></div>
                    </a>
                </li>
                @endif

                @if(userCan(120))
                <li class="@routeis('faq') active @endrouteis">
                    <a class="nav-link" href="{{ route('faq') }}" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Faq</span></div>
                    </a>
                </li>
                @endif

                @if(userCan(121))
                <li class="@routeis('enquiries') active @endrouteis">
                    <a class="nav-link" href="{{ route('enquiries') }}" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Enquiries</span></div>
                    </a>
                </li>
                <li class="@routeis('join-requests') active @endrouteis">
                    <a class="nav-link" href="{{ route('join-requests') }}" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Join Requests</span>
                        </div>
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        @if(userCan([109,110]))
        <li class="menu @routeis('states,cities') active @endrouteis">
            <a href="#location_content" data-bs-toggle="collapse" aria-expanded="{{ routeis('states,cities') }}"
                class="dropdown-toggle">
                <div class="">
                    <i class="fa-duotone fa-location-dot"></i>
                    <span>Location</span>
                </div>
                <div> <i class="fa-solid fa-chevron-right"></i> </div>
            </a>
            <ul class="collapse submenu list-unstyled @routeis('states,cities') show @endrouteis" id="location_content"
                data-bs-parent="#accordionExample">
                @if(userCan(109))
                <li class="@routeis('states') active @endrouteis">
                    <a class="nav-link" href="{{ route('states') }}" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">States</span></div>
                    </a>
                </li>
                @endif

                @if(userCan(110))
                <li class="@routeis('cities') active @endrouteis">
                    <a class="nav-link" href="{{ route('cities') }}" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Cities</span>
                        </div>
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        @if(userCan(101))
        <li class="menu @routeis('setting') active @endrouteis">
            <a href="#setting" data-bs-toggle="collapse" aria-expanded="{{ routeis('setting') }}"
                class="dropdown-toggle">
                <div class="">
                    <i class="fa fa-cog my-auto" aria-hidden="{{ routeis('setting') }}"></i>
                    <span>App Setting</span>
                </div>
                <div><i class="fa-solid fa-chevron-right"></i></div>
            </a>
            <ul class="collapse submenu list-unstyled @routeis('setting') show @endrouteis" id="setting"
                data-bs-parent="#accordionExample">
                @foreach(config('constant.setting_array', []) as $key => $setting)
                <li class="@if(request()->path() == 'setting/'.$key) active @endif">
                    <a class="nav-link" href="{{ route('setting', ['id' => $key]) }}" data-bs-toggle=""
                        aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <span class="nav-link-text ps-1">{{ $setting }}</span>
                        </div>
                    </a>
                </li>
                @endforeach
            </ul>
        </li>

        <li class="menu">
            <a href="{{route('database_backup')}}" aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <i class="fa-duotone fa-database"></i>
                    <span>Database Backup</span>
                </div>
            </a>
        </li>
        @endif
    </ul>
</nav>