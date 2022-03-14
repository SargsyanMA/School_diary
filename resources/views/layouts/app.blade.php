<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="cache-control" content="no-cache" />
        <meta name="expires" content="0" />
        <meta name="pragma" content="no-cache" />

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Золотое сечение.LIFE! - электронный портал частной школы "Золотое сечение"</title>

        <link rel="icon" href="/favicon.png" type="image/x-icon" />
        <meta name="viewport" content="width=device-width, user-scalable=no" />


        <link href="/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css" integrity="sha384-HzLeBuhoNPvSl5KYnjx0BT+WB0QEEqLprO+NBkkk5gbc67FTaL7XIGa2w1L0Xbgc" crossorigin="anonymous">



        <!-- Toastr style -->
        <link href="/css/plugins/toastr/toastr.min.css" rel="stylesheet">

        <!-- Gritter -->
        <link href="/js/plugins/gritter/jquery.gritter.css" rel="stylesheet">
        <link href="/css/plugins/selectize/selectize.bootstrap3.css" rel="stylesheet">
        <link href="/css/animate.css" rel="stylesheet">
        <link href="/css/style.css" rel="stylesheet">
        <link href="/css/custom.css?7" rel="stylesheet">
        <link href="/css/plugins/summernote/summernote.css" rel="stylesheet">
        <link href="/css/plugins/summernote/summernote-bs3.css" rel="stylesheet">
        <link href="/css/plugins/datetimepicker/bootstrap-datetimepicker.css" rel="stylesheet">
        <link href="/css/plugins/fullcalendar/fullcalendar.css" rel="stylesheet">
        <link href="/css/plugins/qtip/jquery.qtip.min.css" rel="stylesheet">
        <link href="/css/plugins/sweetalert/sweetalert.css" rel="stylesheet">
        <link href="/elfinder/css/theme-bootstrap-libreicons-svg.css" rel="stylesheet">

        <!-- Mainly scripts -->
        <script src="/js/jquery-2.1.1.js"></script>
        <!-- jQuery UI -->
        <script src="/js/plugins/jquery-ui/jquery-ui.min.js"></script>
        <script src="/js/bootstrap.min.js"></script>
        <script src="/js/plugins/metisMenu/jquery.metisMenu.js"></script>
        <script src="/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
        <script src="/js/plugins/selectize/standalone/selectize.min.js"></script>
        <script src="/js/nunjucks.js"></script>
        <script src="/js/plugins/summernote/summernote.min.js"></script>
        <script src="/js/plugins/summernote/lang/summernote-ru-RU.js"></script>
        <script src="/js/plugins/summernote/plugin/specialchars/summernote-ext-specialchars.js"></script>
        <script src="/js/plugins/summernote/plugin/ckfinder/summernote-ext-ckfinder.js?10"></script>
        <script src="/js/plugins/moment/moment-with-locales.js"></script>
        <script src="/js/plugins/moment-range/moment-range.js"></script>
        <script src="/js/plugins/datetimepicker/bootstrap-datetimepicker.min.js"></script>
        <script src="/js/plugins/fullcalendar/fullcalendar.js"></script>

        <script src="/js/plugins/qtip/jquery.qtip.min.js"></script>
        <script src="/js/plugins/filestyle/bootstrap-filestyle.min.js"></script>
        <script src="/js/plugins/jasny/jasny-bootstrap.min.js"></script>
        <script src="/js/plugins/sweetalert/sweetalert.min.js"></script>
        <script src="/js/plugins/scrollTo/jquery.scrollTo.min.js"></script>
        <script src="/js/plugins/toastr/toastr.min.js"></script>
        <script src="/js/global.js?2"></script>
    </head>

    <body>
        <div id="wrapper">
            <nav class="navbar-default navbar-static-side" role="navigation">
                <div class="sidebar-collapse">
                    <ul class="nav" id="side-menu">
                        <li class="nav-header">
                            <div class="dropdown profile-element">
                                <!--span><img alt="image" class="img-circle" src="/img/profile_small.jpg" /></span-->
                                <a data-toggle="dropdown" class="dropdown-toggle" href="#">

                                    <span class="clear">
                                        <span class="block m-t-xs">
                                            <span class="fa fa-user"></span> <strong class="font-bold">{{ Auth::user()->name }}</strong>
                                        </span>
                                        <span class="text-muted text-xs block">{{ Auth::user()->position != 0 ? Auth::user()->position : '' }}</span>
                                        @if(Auth::user()->role_id == 2 )
                                            <a href="/students/{{Auth::user()->id}}">Мой профиль</a>
                                        @endif
                                    </span>
                                </a>
                            </div>
                        </li>
                        @include('menu')
                    </ul>
                </div>
            </nav>

            <div id="page-wrapper" class="gray-bg dashbard-1">
                <div class="row border-bottom">
                    <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                        <div class="navbar-header">
                            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#" title="Меню"><i class="fa fa-bars"></i> </a>
                        </div>

                        <ul class="nav navbar-top-links navbar-right">
                            <li>
                                <span class="m-r-sm text-muted welcome-message">Добро пожаловать на портал <span style="color: #de1d3e; font-weight: bold;">Золотое сечение.LIFE</span></span>
                            </li>
                            <li class="dropdown messages"></li>
                            <li>
                                <a href="/logout"><i class="fa fa-sign-out"></i> Выйти</a>
                            </li>
                        </ul>
                    </nav>
                </div>
                @if(isset($title))
                    <div class="row wrapper border-bottom white-bg page-heading">
                        <div class="col-lg-11">
                            @if(isset($breadcrumbs))
                                <ol class="breadcrumb hidden-mobile" style="margin-top: 10px;">
                                    @foreach($breadcrumbs as $url=>$name)
                                        <li>
                                            <a href="{{$url}}">{{$name}}</a>
                                        </li>
                                    @endforeach
                                </ol>
                            @endif

                            <h2><?=$title;?></h2>
                        </div>
                    </div>
                @endif
                @yield('content-no-wrapper')

                <div class="row  border-bottom white-bg dashboard-header">
                    @yield('content')
                </div>



                <div class="footer">
                    <div class="copyright">
                        &copy; Частная школа "Золотое сечение"  <?=date('Y');?>
                    </div>
                </div>
            </div>
        </div>
        @include('includes.poll')



        <!-- Custom and plugin javascript -->
        <script src="/js/inspinia.js?12"></script>
        <script src="/ckfinder/ckfinder.js?3"></script>
    </body>
</html>
