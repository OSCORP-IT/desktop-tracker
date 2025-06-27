<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $page_title ?? '' }} {{ config('app.name', 'Laravel') }}</title>

        <link rel="shortcut icon" href="{{ asset('hyper/images/favicon.ico') }}">

        <link href="{{ asset('hyper/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('hyper/css/app.min.css') }}" rel="stylesheet" type="text/css" id="light-style" />
        <link href="{{ asset('hyper/css/app-dark.min.css') }}" rel="stylesheet" type="text/css" id="dark-style" />

        {{ $style ?? '' }}
    </head>
    <body class="loading" data-layout-config='{"leftSideBarTheme":"dark","layoutBoxed":false, "leftSidebarCondensed":false, "leftSidebarScrollable":false,"darkMode":{{ (Auth::user()->theme_color == "Dark") ? "true" : "false" }}, "showRightSidebarOnStart": false}'>
        <div class="wrapper">
            <div class="leftside-menu">
                <a href="{{ url('admin-panel/dashboard') }}" class="logo text-center logo-light">
                    <span class="logo-lg">
                        <img src="{{ asset('hyper/images/logo.png') }}" alt="" height="16">
                    </span>
                    <span class="logo-sm">
                        <img src="{{ asset('hyper/images/logo_sm.png') }}" alt="" height="16">
                    </span>
                </a>
            
                <a href="{{ url('admin-panel/dashboard') }}" class="logo text-center logo-dark">
                    <span class="logo-lg">
                        <img src="{{ asset('hyper/images/logo-dark.png') }}" alt="" height="16">
                    </span>
                    <span class="logo-sm">
                        <img src="{{ asset('hyper/images/logo_sm_dark.png') }}" alt="" height="16">
                    </span>
                </a>
            
                <div class="h-100" id="leftside-menu-container" data-simplebar>
                    <ul class="side-nav">
                        <li class="side-nav-title side-nav-item">Navigation</li>
            
                        <li class="side-nav-item">
                            <a data-bs-toggle="collapse" href="#sidebarDashboards" aria-expanded="false" aria-controls="sidebarDashboards" class="side-nav-link">
                                <i class="uil-home-alt"></i>
                                <span class="badge bg-success float-end">1</span>
                                <span> Dashboards </span>
                            </a>
            
                            <div class="collapse" id="sidebarDashboards">
                                <ul class="side-nav-second-level">
                                    <li>
                                        <a href="{{ url('admin-panel/dashboard') }}"> Analytics </a>
                                    </li>
            
                                    <li>
                                        <a href="{{ url('admin-panel/dashboard/crm') }}"> CRM </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="side-nav-title side-nav-item"> App </li>

                        <li class="side-nav-item">
                            <a href="{{ url('admin-panel/projects') }}" class="side-nav-link">
                                <i class="uil-briefcase"></i>
                                <span> Projects </span>
                            </a>
                        </li>

                        <li class="side-nav-item">
                            <a href="{{ url('admin-panel/tasks') }}" class="side-nav-link">
                                <i class="uil-briefcase"></i>
                                <span> Tasks </span>
                            </a>
                        </li>
                        
                        <li class="side-nav-item">
                            <a href="{{ url('admin-panel/my-tasks') }}" class="side-nav-link">
                                <i class="uil-briefcase"></i>
                                <span> My Tasks </span>
                            </a>
                        </li>
            
                        <li class="side-nav-title side-nav-item"> User Management </li>
            
                        <li class="side-nav-item">
                            <a href="{{ url('admin-panel/users') }}" class="side-nav-link">
                                <i class="uil-users-alt"></i>
                                <span> Users </span>
                            </a>
                        </li>
            
                        <li class="side-nav-item">
                            <a href="{{ url('admin-panel/roles') }}" class="side-nav-link">
                                <i class="uil-users-alt"></i>
                                <span> Role & Permissions </span>
                            </a>
                        </li>

                        <li class="side-nav-title side-nav-item mt-1"> System </li>

                        <li class="side-nav-item">
                            <a href="{{ url('admin-panel/system-settings') }}" class="side-nav-link">
                                <i class="uil-cog"></i>
                                <span> System Settings </span>
                            </a>
                        </li>
                    </ul>

                    <div class="clearfix"></div>
                </div>
            </div>            

            <div class="content-page">
                <div class="content">
                    <div class="navbar-custom">
                        <ul class="list-unstyled topbar-menu float-end mb-0">
                            <li class="notification-list">
                                @if (Auth::user()->theme_color == "Light")
                                    <a class="nav-link end-bar-toggle" href="javascript: void(0);" onclick="changeThemeColor('dark')">
                                        <i class="uil-moon noti-icon"></i>
                                    </a>
                                @elseif(Auth::user()->theme_color == "Dark")
                                    <a class="nav-link end-bar-toggle" href="javascript: void(0);" onclick="changeThemeColor('light')">
                                        <i class="uil-sun noti-icon"></i>
                                    </a>
                                @endif
                            </li>

                            <li class="dropdown notification-list d-lg-none">
                                <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                    <i class="dripicons-search noti-icon"></i>
                                </a>

                                <div class="dropdown-menu dropdown-menu-animated dropdown-lg p-0">
                                    <form class="p-3">
                                        <input type="text" class="form-control" placeholder="Search ..." aria-label="Recipient's username">
                                    </form>
                                </div>
                            </li>

                            <li class="dropdown notification-list">
                                <a class="nav-link dropdown-toggle nav-user arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                    <span class="account-user-avatar"> 
                                        @if (Auth::user()->profile_image)
                                            <img src="{{ url('images/clients', Auth::user()->profile_image) }}" alt="{{ Auth::user()->full_name ?? "" }}" class="rounded-circle">
                                        @else
                                            <img src="{{ asset('hyper/images/avator.png') }}" alt="user-image" class="rounded-circle">
                                        @endif
                                    </span>
                                    <span>
                                        <span class="account-user-name">{{ Auth::user()->name ?? "" }}</span>
                                        <span class="account-position">{{ Auth::user()->email ?? "" }}</span>
                                    </span>
                                </a>
                                
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated topbar-dropdown-menu profile-dropdown">
                                    <div class=" dropdown-header noti-title">
                                        <h6 class="text-overflow m-0">Welcome !</h6>
                                    </div>

                                    <a href="{{ url('admin-panel/my-account') }}" class="dropdown-item notify-item">
                                        <i class="mdi mdi-account-circle me-1"></i>
                                        <span>My Account</span>
                                    </a>

                                    <a href="{{ url('/') }}" class="dropdown-item notify-item">
                                        <i class="mdi mdi-account-edit me-1"></i>
                                        <span>Home</span>
                                    </a>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
    
                                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="dropdown-item notify-item">
                                            <i class="mdi mdi-logout me-1"></i>
                                            <span>Logout</span>
                                        </a>
                                    </form>
                                </div>
                            </li>

                        </ul>
                        <button class="button-menu-mobile open-left">
                            <i class="mdi mdi-menu"></i>
                        </button>
                        <div class="app-search dropdown d-none d-lg-block">
                            <form>
                                <div class="input-group">
                                    <input type="text" class="form-control dropdown-toggle"  placeholder="Search..." id="top-search">
                                    <span class="mdi mdi-magnify search-icon"></span>
                                    <button class="input-group-text btn-primary" type="submit">Search</button>
                                </div>
                            </form>

                            <div class="dropdown-menu dropdown-menu-animated dropdown-lg" id="search-dropdown">
                                <div class="dropdown-header noti-title">
                                    <h5 class="text-overflow mb-2">Found <span class="text-danger">17</span> results</h5>
                                </div>

                                <a href="javascript:void(0);" class="dropdown-item notify-item">
                                    <i class="uil-notes font-16 me-1"></i>
                                    <span>Analytics Report</span>
                                </a>

                                <a href="javascript:void(0);" class="dropdown-item notify-item">
                                    <i class="uil-life-ring font-16 me-1"></i>
                                    <span>How can I help you?</span>
                                </a>

                                <a href="javascript:void(0);" class="dropdown-item notify-item">
                                    <i class="uil-cog font-16 me-1"></i>
                                    <span>User profile settings</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- =============================================================== -->
                    <main>
                        {{ $slot }}
                    </main>
                    <!-- =============================================================== -->
                </div>

                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                                <script>document.write(new Date().getFullYear())</script> © Fintech - fintech.com
                            </div>
                            <div class="col-md-6">
                                <div class="text-md-end footer-links d-none d-md-block">
                                    <a href="javascript: void(0);">About</a>
                                    <a href="javascript: void(0);">Support</a>
                                    <a href="javascript: void(0);">Contact Us</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        
        <script src="{{ asset('hyper/js/vendor.min.js') }}"></script>

        {{ $script ?? '' }}

        <script src="{{ asset('hyper/js/app.min.js') }}"></script>

        <script type="text/javascript">
            function changeThemeColor(color) {
                var change_theme_color_url = "{{ url('admin-panel/change-theme-color') }}";

                window.location.href = change_theme_color_url + "?theme_color=" + color;
            }
        </script>
    </body>
</html>
