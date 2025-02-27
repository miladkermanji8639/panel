

<?php $__env->startSection('content'); ?>
 <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.agent.edit-agent', ['agentId' => $agentId]);

$__html = app('livewire')->mount($__name, $__params, 'lw-1492641630-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.content.layouts/layoutMaster', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/admin/content/agent/edit.blade.php ENDPATH**/ ?>