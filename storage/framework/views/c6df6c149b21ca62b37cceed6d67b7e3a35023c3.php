<?php $__env->startSection('content'); ?>
    <nav class="navbar" data-spy="affix" data-offset-top="176">
        <div class="container-fluid">
            <form method="get" class="navbar-form navbar-left">
                <div class="form-group">
                    <strong>Группа:</strong>
                </div>
                <div class="form-group">
                    <select name="role" class="form-control input-sm">
                        <option value="">Все пользователи</option>
                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($role->id); ?>" <?php echo e($filter['role'] == $role->id ? 'selected' : ''); ?> ><?php echo e($role->display_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="form-group" style="margin-left: 10px;">
                    <strong>Параллель:</strong>
                </div>
                <div class="form-group">
                    <select class="form-control input-sm" name="grade">
                        <option value="">Все</option>
                    </select>
                </div>
            </form>
            <div class=" navbar-right">
                <a href="edit.php" class="btn btn-sm btn-outline btn-info"><i class="fa fa-plus"></i> Добавить нового</a>
                <a href="fired.php" class="btn btn-sm btn-default"><i class="fa fa-eye" aria-hidden="true"></i> Неактивные пользователи</a>
            </div>
        </div>
    </nav>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-condensed table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Имя</th>
                        <th>Телефон</th>
                        <th>Email (Логин)</th>
                        <th>Доп. контакты</th>
                        <th>Должность</th>
                        <th>Другие контактные лица</th>
                        <th>Последний визит</th>
                        <th>Уроков</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="user-row" data-id="<?php echo e($user->id); ?>">
                            <td><?php echo e($user->id); ?></td>
                            <td><strong><?php echo e($user->name); ?></strong></td>
                            <td style="white-space: nowrap;"><?php echo e($user->phone); ?></td>
                            <td><a href="mailto:<?php echo e($user->email); ?>"><?php echo e($user->email); ?></a></td>
                            <td style="font-size: 0.8em;"><?php echo e($user->contacts); ?></td>
                            <td><?php echo e($user->position); ?></td>
                            <td style="font-size: 0.8em;"><?php echo e($user->contacts2); ?></td>
                            <td><?php echo e($user->lastAuthorization ? date('d.m.Y H:i:s',strtotime($user->lastAuthorization)) : ''); ?></td>
                            <td></td>
                            <td>
                                <a href="edit.php?id=<?php echo e($user->id); ?>" class="btn btn-xs btn-outline btn-warning"><i class="fa fa-pencil"></i></a>
                                <a href="edit.php?id=<?php echo e($user->id); ?>&delete=1" class="btn btn-xs btn-outline btn-danger delete-user" data-name="<?php echo e($user->name); ?>"><i class="fa fa-times"></i></a>
                                <a href="/controllers/user.php?action=send-invite&id=<?php echo e($user->id); ?>" class="btn btn-xs btn-outline btn-info js-send-invite" data-name="<?php echo e($user->name); ?>" title="Отправить приглашение"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <a href="/controllers/user.php?action=send-invite" class="btn btn-sm btn-outline btn-info js-send-massive">
                <i class="fa fa-paper-plane-o" aria-hidden="true"></i> Отправить приглашение всем пользователям на странице
            </a>
            <? //if (in_array(User::getInstance()->getUserId(), [1,84,86,85])):?>
            <a href="" class="btn btn-sm btn-outline btn-warning">
                <i class="fa fa-file-excel-o"></i> Выгрузить доступы
            </a>
            <br/><br/><br/>
            <? //endif;?>
        </div>
    </div>

    <script>
        $('.delete-user').click(function(event) {
            event.preventDefault();
            if (confirm('Вы точно хотите удалить пользователя '+$(this).attr('data-name')+'?')) {
                window.location=$(this).attr('href');
            }
        });

        $('.js-send-invite').click(function(event) {
            event.preventDefault();
            if (confirm('Вы точно хотите отправить приглашение пользователю '+$(this).attr('data-name')+'?')) {
                $.getJSON($(this).attr('href'), {}, function(response) {
                    toastr.success("Приглашение отправлено");
                });
            }
        });

        $('.js-send-massive').click(function(event) {
            event.preventDefault();
            var users = [];
            $('.user-row').each(function () {
                users.push(parseInt($(this).data('id')));
            });

            if (confirm('Вы точно хотите отправить приглашение пользователям: '+users.length+'?')) {
                $('.js-send-invite').each(function () {
                    //console.log($(this).attr('href'));
                    $.getJSON($(this).attr('href'), {}, function (response) {
                        toastr.success("Приглашение отправлено");
                    });
                });
            }
        });

        $('.navbar select').change(function() {
            $('.navbar form').submit();
        });

    </script>
<?php $__env->stopSection(); ?>






<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php /* /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/user/index.blade.php */ ?>