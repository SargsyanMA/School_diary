<?php $__env->startSection('content-no-wrapper'); ?>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row m-b-lg m-t-lg">
            <div class="col-md-6">

                <div class="profile-image" style="width: 120px; float: left;">
                    <img style="width: 84px; height: 84px; " src="<?php echo e(url('storage/'.$student->avatar)); ?>" class="rounded-circle circle-border m-b-md" alt="profile">
                </div>
                <div class="profile-info">
                    <div class="">
                        <div>
                            <h2 class="no-margins">
                                <?php echo e($student->name); ?>

                            </h2>
                            <h4><?php echo e($student->grade->number ?? ''); ?><?php echo e($student->class_letter); ?> класс</h4>
                            <small>
                                There are many variations of passages of Lorem Ipsum available, but the majority
                                have suffered alteration in some form Ipsum available.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <table class="table small m-b-xs">
                    <tbody>
                    <tr>
                        <td>
                            <strong>142</strong> Projects
                        </td>
                        <td>
                            <strong>22</strong> Followers
                        </td>

                    </tr>
                    <tr>
                        <td>
                            <strong>61</strong> Comments
                        </td>
                        <td>
                            <strong>54</strong> Articles
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>154</strong> Tags
                        </td>
                        <td>
                            <strong>32</strong> Friends
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <small>Sales in last 24h</small>
                <h2 class="no-margins">206 480</h2>
                <div id="sparkline1"><canvas style="display: inline-block; width: 266.75px; height: 50px; vertical-align: top;" width="266" height="50"></canvas></div>
            </div>


        </div>
        <div class="row">

            <div class="col-lg-3">

                <div class="ibox">
                    <div class="ibox-content">
                        <h3>Контакты</h3>
                        <div><a href="mailto:<?php echo e($student->email); ?>"><?php echo e($student->email); ?></a></div>
                        <div><?php echo e($student->phone); ?></div>
                        <?php if($student->birthdate): ?>
                            <div>Дата рождения: <?php echo e($student->birthdate); ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="ibox">
                    <div class="ibox-content">
                        <h3>Родители</h3>
                        <div><?php echo e($student->parent->name ?? ''); ?> (<?php echo e($student->parent->relation ?? ''); ?>)</div>
                        <div><a href="mailto:<?php echo e($student->parent->email ?? ''); ?>"><?php echo e($student->parent->email ?? ''); ?></a></div>
                        <div><?php echo e($student->parent->phone ?? ''); ?></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <?php $__empty_1 = true; $__currentLoopData = $student->comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="social-feed-box" style="border: 1px solid #e7eaec; background: #fff; margin-bottom: 15px;">
                        <div class="social-avatar" style="    padding: 15px 15px 0 15px;">
                            <a href="" class="float-left" style="float: left!important;">
                                <img alt="image" src="<?php echo e(url('storage/users/default.png')); ?>" style="height: 40px;width: 40px; margin-right: 10px;">
                            </a>
                            <div class="media-body">
                                <a href="#"><?php echo e($comment->author->name); ?></a>
                                <small class="text-muted"><?php echo e($comment->created_at); ?></small>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="social-body" style="padding: 15px;">
                            <p><?php echo e($comment->text); ?></p>
                            <div class="btn-group">
                                <button class="btn btn-white btn-xs"><i class="fa fa-pencil"></i> редактировать</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    - нет комментариев -
                <?php endif; ?>
                <div class="social-footer">
                    <form method="post" action="/students/<?php echo e($student->id); ?>/add-comment">
                        <?php echo e(csrf_field()); ?>

                        <div class="social-comment">
                            <div class="media-body">
                                <div class="form-group">
                                    <textarea class="form-control" name="text" id="student-comment" rows="3"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> добавить комментарий</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-4 m-b-lg">
                <?php $__currentLoopData = $achievement_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="ibox">
                        <div class="ibox-content">
                            <h3><?php echo e($type->name); ?></h3>
                            <?php $__currentLoopData = $achievements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $achievement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($achievement->type_id ==  $type->id): ?>
                                    <p>
                                        <?php echo e($achievement->text); ?>

                                    </p>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal">
                                <i class="fa fa-plus"></i> добавить
                            </button>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="/students/<?php echo e($student->id); ?>/add-achievement">
                    <?php echo e(csrf_field()); ?>

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Добавить достижение</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <select name="type_id" class="form-control">
                                <?php $__currentLoopData = $achievement_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($type->id); ?>"><?php echo e($type-> name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" name="text" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php /* /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/student/detail.blade.php */ ?>