<!-- todo access check -->
<?php
    $teacherParent = \Illuminate\Support\Facades\Auth::user()->role_id == App\User::TEACHER && \Illuminate\Support\Facades\Auth::user()->children()->exists()
    ?'parent'
    :'';
?>


<?php $__currentLoopData = config('menu.menu'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if(
        in_array(\Illuminate\Support\Facades\Auth::user()->role->name, $item['roles'])
        || in_array($teacherParent, $item['roles'])
        || (\Illuminate\Support\Facades\Auth::user()->curator && in_array('curator', $item['roles']))
        ||  \Illuminate\Support\Facades\Auth::user()->role->name == 'admin'
        ||  \Illuminate\Support\Facades\Auth::user()->admin
        ): ?>
        <li>
            <a href="<?php echo e($item['link']); ?>" title="<?php echo e($item['title']); ?>">
                <i class="fa <?php echo e($item['icon']); ?>"></i>
                <span class="nav-label"><?php echo e($item['title']); ?></span>
            </a>
        </li>
    <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/menu.blade.php ENDPATH**/ ?>