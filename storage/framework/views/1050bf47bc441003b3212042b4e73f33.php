<tr class="service-row <?php if($level > 0): ?> subservice <?php endif; ?>" data-id="<?php echo e($service->id); ?>"
    <?php if($level > 0): ?> data-parent="<?php echo e($service->parent_id); ?>" style="display: none;" <?php endif; ?> data-level="<?php echo e($level); ?>">
    <td><?php echo e($service->id); ?></td>
    <td>
        <?php echo str_repeat('&mdash; ', $level); ?> <?php echo e($service->name); ?>

    </td>
    <td><?php echo e($service->duration); ?> دقیقه</td>
    <td><?php echo e(number_format($service->price, 0)); ?> تومان</td>
    <td><?php echo e($service->discount ? number_format($service->discount, 0) . ' تومان' : 'ندارد'); ?></td>
    <td>
        <span wire:click="toggleStatus(<?php echo e($service->id); ?>)"
              class="text-<?php echo e($service->status == 1 ? 'success' : 'danger'); ?> cursor-pointer">
            <?php echo e($service->status == 1 ? 'فعال' : 'غیرفعال'); ?>

        </span>
    </td>
    <td>
        <a href="<?php echo e(route('dr-services.edit', $service->id)); ?>" class="btn btn-sm btn-light rounded-circle">
            <img src="<?php echo e(asset('dr-assets/icons/edit.svg')); ?>" alt="ویرایش">
        </a>
        <button type="button" class="btn btn-sm btn-light rounded-circle delete-service"
                data-url="<?php echo e(route('dr-services.destroy', $service->id)); ?>">
            <img src="<?php echo e(asset('dr-assets/icons/trash.svg')); ?>" alt="حذف">
        </button>
    </td>
    <td>
        <!--[if BLOCK]><![endif]--><?php if($service->children->count()): ?>
            <button class="btn btn-sm btn-primary toggle-subservices" data-id="<?php echo e($service->id); ?>">مشاهده</button>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </td>
</tr>

<!--[if BLOCK]><![endif]--><?php $__currentLoopData = $service->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php echo $__env->make('dr.panel.dr-services.partials.service-row', ['service' => $child, 'level' => $level + 1], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
<?php /**PATH D:\MyProjects\Benobe\panel\resources\views/dr/panel/dr-services/partials/service-row.blade.php ENDPATH**/ ?>