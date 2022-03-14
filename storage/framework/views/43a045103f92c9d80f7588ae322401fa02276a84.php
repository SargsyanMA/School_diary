<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="cache-control" content="no-cache" />
        <meta name="expires" content="0" />
        <meta name="pragma" content="no-cache" />

        <!-- CSRF Token -->
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

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

    <body onload="window.print()">
        <?php echo $__env->yieldContent('content-no-wrapper'); ?>
        <div class="row  border-bottom white-bg dashboard-header">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
        <!-- Custom and plugin javascript -->
        <script src="/js/inspinia.js?12"></script>
        <script src="/ckfinder/ckfinder.js?3"></script>
    </body>
</html>
<?php /* /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/layouts/app-print.blade.php */ ?>