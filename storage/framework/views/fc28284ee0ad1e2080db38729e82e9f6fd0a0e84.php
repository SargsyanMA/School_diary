<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Золотое сечение.LIFE! - электронный портал частной школы "Золотое сечение"</title>
        <link rel="icon" href="/favicon.png" type="image/x-icon" />
        <link href="/css/bootstrap.min.css" rel="stylesheet">
        <link href="/font-awesome/css/font-awesome.css" rel="stylesheet">
        <link href="/css/animate.css" rel="stylesheet">
        <link href="/css/style.css" rel="stylesheet">
        <link href="/css/custom.css" rel="stylesheet">
    </head>
    <body class="gray-bg">
        <div class="loginColumns animated fadeInDown">
            <div class="row">
                <div class="col-md-6">
                    <div class="ibox-content">
                        <?php if($errors->has('email')): ?>
                            <span class="invalid-feedback" role="alert">
                                <strong><?php echo e($errors->first('email')); ?></strong>
                            </span>
                        <?php endif; ?>
                        <?php if($errors->has('password')): ?>
                            <span class="invalid-feedback" role="alert">
                                <strong><?php echo e($errors->first('password')); ?></strong>
                            </span>
                        <?php endif; ?>
                        <form class="m-t" role="form" method="post" action="<?php echo e(route('login')); ?>">
                            <?php echo csrf_field(); ?>
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Имя пользователя" name="email" id="login" required autofocus autocomplete="on" value="<?php echo e(old('email')); ?>">
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" placeholder="Пароль" name="password" id="password" required autocomplete="on">
                            </div>
                            <button type="submit" class="btn btn-primary block full-width m-b">Войти</button>
                            <div class="checkbox">
                                <label for="remember-me" >
                                    <input type="checkbox" class="checkbox" name="remember" value="1" id="remember" <?php echo e(old('remember') ? 'checked' : ''); ?> /> Запомнить меня на этом устройстве
                                </label>
                            </div>
                            <?php if(Route::has('password.request')): ?>
                                <a class="btn btn-link" href="<?php echo e(route('password.request')); ?>">
                                    Забыли пароль? (изменить пароль)
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
                <div class="col-md-6">
                    <p><strong>Уважаемые родители!</strong></p>
                    <p>Приветствуем Вас на интернет-портале <span class="font-bold" style=" color: #de1d3e;">Золотое сечение.LIFE</span>. Здесь представлена самая свежая и актуальная информация о жизни <a href="http://www.theschool.ru" target="_blank">школы «Золотое сечение»</a>: все новости и события, расписание занятий с домашними заданиями и комментариями учителей.</p>
                    <p>Мы создали данную площадку, так как заботимся о достижении нашими учениками наивысших учебных результатов и стремимся максимально раскрыть потенциал каждого ребенка. Этого можно достичь только совместными усилиями: осведомленность родителей о ведении образовательного процесса и готовность к сотрудничеству со специалистами школы являются важными условиями успешного обучения и социализации детей. Помните, что ребенку необходима Ваша поддержка!</p>

                </div>
            </div>
            <hr/>
            <div class="row">
                <div class="col-md-12">
                    <strong>Copyright</strong> Частная школа  "Золотое сечение" &copy; <?php echo e(date('Y')); ?>

                </div>
            </div>
        </div>
    </body>
</html><?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/auth/login.blade.php ENDPATH**/ ?>