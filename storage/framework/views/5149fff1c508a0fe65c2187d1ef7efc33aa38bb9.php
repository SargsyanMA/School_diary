<?php $__env->startSection('content'); ?>
    <h1>График контрольных работ</h1>

    <p>
        В один день может быть проведено не более 2 контрольных работ.
        Все работы должны быть проведены в тот день, в который они указаны.
        В случае изменения даты контрольной работы, сообщите об этом заместителю директору по учебной работе своего подразделения.
        Если Вы не сообщили об изменениях, контрольная работа проводиться не может!
    </p>
    <form method="get">
        <div class="row">
            <div class="form-group col-md-3">
                <label>Параллель</label>
                <select class="form-control input-sm js-grade" name="grade_id">
                    <?php $__currentLoopData = $grades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($g->id); ?>" <?php echo e($g->id == $currentGrade ? 'selected' :''); ?> ><?php echo e($g->number); ?> <?php echo e($g->letter); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="form-group col-md-3">
                <label>Месяц</label>
                <select class="form-control input-sm js-grade" name="month">
                    <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($i); ?>" <?php echo e($i == $currentMonth ? 'selected' :''); ?> ><?php echo e($m); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="form-group col-md-3">
                <button class="btn btn-success btn-sm" style="margin-top: 22px;" type="submit">Выбрать</button>
            </div>
            <div class="form-group col-md-6 text-right">
                <a href="/forms/kr-plan?print=1" class="btn btn-success btn-sm" style="margin-top: 22px;" type="submit">Распечатать</a>
                <a href="/forms/kr-plan-export" class="btn btn-success btn-sm" style="margin-top: 22px;" type="submit">Excel</a>
            </div>
        </div>
    </form>

    <div style="overflow: scroll;">
    <?php $__currentLoopData = $dates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year_num=>$year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $__currentLoopData = $year; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month_num=>$month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <h2><?php echo e(\Carbon\Carbon::parse("{$year_num}-{$month_num}-01")->locale('ru')->isoFormat('MMMM G')); ?></h2>
            <table class="table table-bordered">
                <tr>
                    <th class="text-right">Пн</th>
                    <th class="text-right">Вт</th>
                    <th class="text-right">Ср</th>
                    <th class="text-right">Чт</th>
                    <th class="text-right">Пт</th>
                    <th class="text-danger text-right">Сб</th>
                    <th class="text-danger text-right">Вс</th>
                </tr>
                <?php $__currentLoopData = $month; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <?php for($i=1; $i<= 7; $i++): ?>
                            <td class="<?php echo e($i>=6 ? 'text-danger': ''); ?>">
                                <?php if(isset($week[$i])): ?>
                                    <div class="row" style="margin-bottom: 10px;">
                                        <div class="col-xs-6">
                                            <?php if($i<6): ?>
                                                <button class="btn btn-info btn-outline btn-sm js-kr-add" style="padding: 2px 10px" data-date="<?php echo e($week[$i]->format('d.m.Y')); ?>">
                                                    <i class="fa fa-plus"></i> добавить
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-xs-6 text-right"><?php echo e($week[$i]->format('d')); ?></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 js-data">
                                            <?php if(isset($krs[$week[$i]->format('Y-m-d')][$grade->id])): ?>
                                                <?php $__currentLoopData = $krs[$week[$i]->format('Y-m-d')][$grade->id]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div style="margin-bottom: 15px;">
                                                        <?php if(!empty($kr->lesson)): ?>
                                                            <b><?php echo e($kr->lesson->name); ?></b><br/>
                                                        <?php endif; ?>
                                                        <?php echo e($kr->text); ?><br/>
                                                        <button class="btn btn-warning btn-outline btn-sm js-kr-edit" style="padding: 2px 10px" data-route="<?php echo e(route('kr-plan.edit', [$kr->id])); ?>">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </button>
                                                        <form style="display: inline-block;" action="<?php echo e(route('kr-plan.destroy', [$kr->id])); ?>" method="POST">
                                                            <?php echo e(csrf_field()); ?>

                                                            <?php echo method_field('DELETE'); ?>
                                                            <button type="submit" style="padding: 2px 10px" class="btn btn-outline btn-sm btn-danger">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </td>
                        <?php endfor; ?>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </table>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


    <!-- Modal -->
    <div class="modal fade" id="krModal" tabindex="-1" role="dialog" aria-labelledby="krModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content"></div>
        </div>
    </div>

    <script>
        $(function() {
            $('.js-kr-add').click(function () {
                $.get('<?php echo e(route('kr-plan.create')); ?>', {
                    'date': $(this).data('date'),
                    'grade_id': $('.js-grade').val()
                }, function (html) {
                    $('#krModal .modal-content').html(html);
                    $('#krModal').modal('show');

                    $('.datetimepicker').datetimepicker({
                        sideBySide: false,
                        locale: 'ru',
                        format: 'DD.MM.YYYY',
                        useCurrent: false
                    });
                })
            });

            $('.js-kr-edit').click(function () {
                $.get( $(this).data('route'), function (html) {
                    $('#krModal .modal-content').html(html);
                    $('#krModal').modal('show');

                    $('.datetimepicker').datetimepicker({
                        sideBySide: false,
                        locale: 'ru',
                        format: 'DD.MM.YYYY',
                        useCurrent: false
                    });
                })
            });
        });
    </script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/forms/kr-plan/index.blade.php ENDPATH**/ ?>