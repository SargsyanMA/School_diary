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

                <?//if($error):?>
                <div class="alert alert-danger">
                    <?//=$error;?>
                </div>
                <?//endif;?>
                <form class="m-t" role="form" method="post">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Имя пользователя" name="login" id="login" required="" autocomplete="on" value="<?//=$login;?>">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" placeholder="Пароль" name="password" id="password" required="" autocomplete="on">
                </div>
                <button type="submit" class="btn btn-primary block full-width m-b">Войти</button>
                <div class="checkbox">
                    <label for="remember-me" >
                        <input type="checkbox" class="checkbox" name="remember" value="1" id="remember-me" /> Запомнить меня на этом устройстве
                    </label>
                </div>
                <a href="/forgotpassword.php">
                    <small>Забыли пароль? (изменить пароль)</small>
                </a>
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
            <strong>Copyright</strong> Частная школа  "Золотое сечение" &copy; {{ date('Y') }}
        </div>
    </div>
</div>

</body>

</html>
