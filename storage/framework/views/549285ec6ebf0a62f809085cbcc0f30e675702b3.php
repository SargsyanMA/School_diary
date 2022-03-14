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
<div class="container loginColumns animated fadeInDown">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card ibox-content">
                <div class="card-header">Восстановить доступ к аккаунту</div>
                <div class="card-body">
                    <?php if(session('status')): ?>
                        <div class="alert alert-success" role="alert">
                            Мы отправили вам ссылку для сброса пароля по электронной почте!
                        </div>
                    <?php endif; ?>
                    <form method="POST" action="<?php echo e(route('password.email')); ?>">
                        <?php echo csrf_field(); ?>

                        <div class="form-group">
                            <label for="email" class="col-form-label text-md-right">Адрес электронной почты</label>

                            <input id="email" type="email" class="form-control<?php echo e($errors->has('email') ? ' is-invalid' : ''); ?>" name="email" value="<?php echo e(old('email')); ?>" required>

                            <?php if($errors->has('email')): ?>
                                <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e($errors->first('email')); ?></strong>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                Отправить ссылку для сброса пароля
                            </button>
                        </div>
                    </form>
                </div>
            </div>
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
</html><?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/auth/passwords/email.blade.php ENDPATH**/ ?>