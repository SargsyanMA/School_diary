<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Золотое сечение.LIFE! - электронный портал частной школы "Золотое сечение"</title>
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="/css/animate.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">

    <script src="/js/jquery-2.1.1.js"></script>
</head>

<body class="gray-bg">

<div class="passwordBox animated fadeInDown">
    <div class="row">

        <div class="col-md-12">
            <div class="ibox-content">
                <h2 class="font-bold">Изменение пароля</h2>

                <?if($step==1):?>
                    <?if ($result):?>
                        <p><?=$result;?></p>
                    <?else:?>
                        <p>Введите адрес электронной почты, и Вам будет отправлена ссылка для изменения пароля</p>
                        <div class="row">
                            <div class="col-lg-12">
                                <form class="m-t" role="form" action="/forgotpassword.php" method="post">
                                    <div class="form-group">
                                        <input type="email" name="email" class="form-control" placeholder="Email" value="<?=$inputEmail;?>" required="">
                                        <span class="text-danger"><?=$error;?></span>
                                    </div>
                                    <button type="submit" class="btn btn-primary block full-width m-b">Изменить пароль</button>
                                </form>

                            </div>
                        </div>
                    <?endif;?>
                <?elseif($step==2):?>
                    <p class="text-danger"><?=$error;?></p>
                    <?if($result):?>
                        <h3 class="text-success"><?=$result;?></h3>
                    <?endif;?>
                    <?if ($showPasswordChange && !$result):?>
                        <p>Введите новый пароль</p>
                        <div class="row">
                            <div class="col-lg-12">
                                <form class="m-t" role="form" action="/forgotpassword.php" method="post">
                                    <div class="form-group">
                                        <label>Новый пароль</label>
                                        <input type="password" name="password" class="form-control" placeholder="Новый пароль" value="" required="">
                                    </div>
                                    <div class="form-group">
                                        <label>Подтверждение пароля</label>
                                        <input type="password" name="passwordConfirm" class="form-control" placeholder="Подтверждение пароля" value="" required="">
                                    </div>

                                    <input type="hidden" name="secret" value="<?=$secret;?>">
                                    <input type="hidden" name="userid" value="<?=$userId;?>">
                                    <button type="submit" class="btn btn-primary block full-width m-b">Сохранить пароль</button>
                                </form>

                            </div>
                        </div>
                    <?endif;?>
                <?endif;?>
            </div>
        </div>
    </div>
    <hr/>
    <div class="row">
        <div class="col-md-12">
            <strong>Copyright</strong> Частная школа "Золотое сечение" &copy; <?=date('Y');?>
        </div>
    </div>
</div>
<script>
    $(function() {
        var password = $("input[name='password']") ,
            confirmPassword = $("input[name='passwordConfirm']");

        function validatePassword(){
            if(password.val() != confirmPassword.val()) {
                confirmPassword.get(0).setCustomValidity("Пароли не совпадают");
            } else {
                confirmPassword.get(0).setCustomValidity('');
            }
        }

        password.change(function() {
            validatePassword();
        });
        confirmPassword.keyup(function() {
            validatePassword();
        });
    });
</script>
</body>

</html>
