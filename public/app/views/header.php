<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="cache-control" content="no-cache" />
    <meta name="expires" content="0" />
    <meta name="pragma" content="no-cache" />
    <title>Золотое сечение.LIFE! - электронный портал частной школы "Золотое сечение"</title>

    <link rel="icon" href="/favicon.png" type="image/x-icon" />
    <meta name="viewport" content="width=device-width, user-scalable=no" />


    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/font-awesome/css/font-awesome.css" rel="stylesheet">

    <!-- Toastr style -->
    <link href="/css/plugins/toastr/toastr.min.css" rel="stylesheet">

    <!-- Gritter -->
    <link href="/js/plugins/gritter/jquery.gritter.css" rel="stylesheet">
    <link href="/css/plugins/selectize/selectize.bootstrap3.css" rel="stylesheet">
    <link href="/css/animate.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
    <link href="/css/custom.css?6" rel="stylesheet">
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
                                <span class="block m-t-xs"><span class="fa fa-user"></span> <strong class="font-bold"><?=$currentUser['name'];?></strong></span>
                                <span class="text-muted text-xs block"><?=!empty($currentUser['role']) ? $currentUser['role'] : '';?> <!--b class="caret"></b--></span>
                            </span>
                        </a>
                        <!--ul class="dropdown-menu animated fadeInRight m-t-xs">
                            <li><a href="/user/edit.php?id=<?=$currentUser['id'];?>">Профиль</a></li>
                            <li class="divider"></li>
                            <li><a href="/?logout=1">Выйти</a></li>
                        </ul-->
                    </div>
                    <!--div class="logo-element">
                        <img src="/img/logo.png" />
                    </div-->
                </li>

                <? include_once 'menu.php';?>


            </ul>
        </div>
    </nav>

    <div id="page-wrapper" class="gray-bg dashbard-1">
        <div class="row border-bottom">
            <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#" title="Меню"><i class="fa fa-bars"></i> </a>
                    <div class="year-title hidden-mobile">
                        <?if($userCanChangeYear):?>
                            <form method="get" class="form-inline">
                                <select class="form-control" name="changeYear">
                                    <?foreach($yearList as $year):?>
                                        <option value="<?=$year;?>" <?if($currentYear==$year):?>selected<?endif;?>><?=$year;?> - <?=$year+1;?> учебный год</option>
                                    <?endforeach;?>
                                </select>
                                <button class="btn btn-sm">Выбрать</button>
                            </form>
                        <?else:?>
                            <?=$currentYear;?> - <?=$currentYear +1;?> учебный год
                        <?endif;?>
                    </div>
                </div>

                <ul class="nav navbar-top-links navbar-right">
                    <li>
                        <span class="m-r-sm text-muted welcome-message">Добро пожаловать на портал <span style="color: #de1d3e; font-weight: bold;">Золотое сечение.LIFE</span>
                    </li>
                    <li class="dropdown messages"></li>
                    <li>
                        <a href="/?logout=1"><i class="fa fa-sign-out"></i> Выйти</a>
                    </li>
                </ul>
            </nav>
        </div>

            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-11">
                    <h2><?=$title;?></h2>
                    <ol class="breadcrumb  hidden-mobile">
                        <li>
                            <a href="/">Главная</a>
                        </li>
                        <li class="active">
                            <strong><?=$title;?></strong>
                        </li>
                    </ol>
                </div>
                <div class="col-lg-1 view-links hidden-mobile">
                    <?if ($_SERVER['SCRIPT_NAME']=="/user/index.php" || ($_SERVER['SCRIPT_NAME']=="/schedule.php")):?>
                        <?if($currentType=='class' || $currentType=='teacher'):?>
                            <div class="dropdown inline">
                                <button id="excel" class="btn btn-default btn-outline" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <i class="fa fa-file-excel-o"></i>
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="excel">
                                    <li><a href="<?=View::getInstance()->addToUrl(['view'=>'excel', 'lastname'=>1]);?>" class="excel" target="_blank" title="Выгрузить в Excel">Выгрузить в Excel c фамилиями</a></li>
                                    <li><a href="<?=View::getInstance()->addToUrl(['view'=>'excel', 'lastname'=>0]);?>" class="excel" target="_blank" title="Выгрузить в Excel">Выгрузить в Excel без фамилий</a></li>
                                </ul>
                            </div>
                        <?else:?>
                            <a href="<?=View::getInstance()->addToUrl(['view'=>'excel', 'lastname'=>0]);?>" class="btn btn-default btn-outline excel" target="_blank" title="Выгрузить в Excel"> <i class="fa fa-file-excel-o"></i></a>
                        <?endif;?>

                        <?if($currentType=='class' || $currentType=='teacher'):?>
                            <div class="dropdown  inline">
                                <button id="print" class="btn btn-default btn-outline" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <i class="fa fa-print"></i>
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdown">
                                    <li><a href="<?=View::getInstance()->addToUrl(['view'=>'print', 'lastname'=>1]);?>" class="print" target="_blank" title="Распечатать">Распечатать c фамилиями</a></li>
                                    <li><a href="<?=View::getInstance()->addToUrl(['view'=>'print', 'lastname'=>0]);?>" class="print" target="_blank" title="Распечатать">Распечатать без фамилий</a></li>
                                </ul>
                            </div>
                        <?else:?>
                            <a href="<?=View::getInstance()->addToUrl(['view'=>'print', 'lastname'=>0]);?>" class="btn btn-default btn-outline print" target="_blank" title="Распечатать"><i class="fa fa-print"></i></a>
                        <?endif;?>
                    <?endif;?>
                    <?if ($_SERVER['SCRIPT_NAME']=="/calendar/index.php"):?>
                        <a href="<?=View::getInstance()->addToUrl(['view'=>'print']);?>" class="btn btn-default btn-outline print" target="_blank" title="Распечатать"><i class="fa fa-print"></i></a>
                    <?endif;?>
                </div>
            </div>
        <div class="row  border-bottom white-bg dashboard-header">