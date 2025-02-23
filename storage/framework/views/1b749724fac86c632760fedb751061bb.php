<div class="card">
 <div class="card-header d-flex justify-content-between align-items-center">
  <a href="<?php echo e(route('admin.Dashboard.menu.create')); ?>" class="btn btn-primary">
   <i class="ti ti-plus"></i> افزودن منو
  </a>
 </div>
 <div class="card-body">
  <div class="d-flex justify-content-between mb-3">
   <div>
    <input type="search" class="form-control  w-100 me-2" placeholder="جستجو شهر" wire:model="search"
     wire:keyup="searchUpdated">
   </div>
   <div>
    <button class="btn btn-danger" id="deleteButton" <?php if(empty($selectedRows)): echo 'disabled'; endif; ?> onclick="confirmDelete()">
     <i class="ti ti-trash"></i> حذف انتخاب‌شده‌ها
    </button>
   </div>
  </div>
  <!--[if BLOCK]><![endif]--><?php if(session()->has('success')): ?>
   <div class="alert alert-success"><?php echo e(session('success')); ?></div>
  <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
  <div class="table-responsive">
   <table class="table table-striped">
    <thead>
     <tr>
      <th>
       <input type="checkbox" class="form-check-input" wire:model="selectAll"
        x-on:change="$wire.dispatch('updateDeleteButton')">
      </th>
      <th>ردیف</th>
      <th>نام</th>
      <th>لینک</th>
      <th>آیکون</th>
      <th>جایگاه</th>
      <th>زیرمجموعه</th>
      <th>ترتیب</th>
      <th>وضعیت</th>
      <th>عملیات</th>
     </tr>
    </thead>
    <tbody>
     <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <tr>
       <td><input type="checkbox" class="form-check-input" wire:model="selectedRows" value="<?php echo e($menu->id); ?>"
       x-on:change="$wire.dispatch('updateDeleteButton')"></td>
       <td><?php echo e($index + 1); ?></td>
       <td><?php echo e($menu->name); ?></td>
       <td><?php echo e($menu->url); ?></td>
      <td>
      <img src="<?php echo e($menu->icon ? asset('storage/' . $menu->icon) : asset('default-icon.png')); ?>" alt="آیکون منو"
        class="img-thumbnail" style="width: 40px; height: 40px; border-radius: 8px;">
      </td>

       <td><?php echo e($menu->position); ?></td>
       <td><?php echo e($menu->parent ? $menu->parent->name : 'دسته اصلی'); ?></td>
       <td><?php echo e($menu->order); ?></td>
       <td>
      <span wire:click="toggleStatus(<?php echo e($menu->id); ?>)"
       class="badge bg-label-<?php echo e($menu->status == 1 ? 'success' : 'danger'); ?> cursor-pointer">
       <?php echo e($menu->status == 1 ? 'فعال' : 'غیر فعال'); ?>

      </span>
       </td>
       <td>
      <div class="dropdown">
       <button class="btn p-0" data-bs-toggle="dropdown">
        <i class="ti ti-dots-vertical"></i>
       </button>
       <div class="dropdown-menu">
        <a class="dropdown-item" href="<?php echo e(route('admin.Dashboard.menu.edit', ['id' => $menu->id])); ?>">
         <i class="ti ti-pencil"></i> ویرایش
        </a>
       </div>
      </div>
       </td>
      </tr>
   <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
    </tbody>
  </table>
  </div>
  <div class="d-flex justify-content-between mt-3">
   <span>نمایش <?php echo e($menus->count()); ?> از <?php echo e($menus->total()); ?> منو</span>
   <?php echo e($menus->links()); ?>

  </div>
 </div>
</div>
<script>
  function confirmDelete() {
    Swal.fire({
      title: "آیا مطمئن هستید؟",
      text: "این عملیات غیرقابل بازگشت است!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
      confirmButtonText: "بله، حذف شود!",
      cancelButtonText: "لغو",
    }).then((result) => {
      if (result.isConfirmed) {
        window.dispatchEvent(new CustomEvent('deleteSelectedMenus'));
      }
    });
  }
</script><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/livewire/admin/dashboard/menu/menu-list.blade.php ENDPATH**/ ?>