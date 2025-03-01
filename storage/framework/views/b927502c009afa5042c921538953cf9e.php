<div class="container-fluid py-5">
 <!-- هدر -->
 <div class="bg-white-only lter b-b wrapper-md clrfix mb-5">
  <h1 class="m-n h3 font-thin">مدیریت ریدایرکت‌ها</h1>
 </div>

 <!-- فرم افزودن -->
 <div class="wrapper-md mb-5">
  <div class="panel panel-default shadow-sm">
   <div class="panel-heading">افزودن ریدایرکت جدید</div>
   <div class="panel-body">
    <div class="row g-4">
     <div class="col-md-5">
      <div class="form-group">
       <label class="control-label fw-bold mb-2">آدرس مسیر ریدایرکت</label>
       <input type="url" class="form-control input-shiny" wire:model="newSourceUrl" placeholder="https://" dir="ltr">
       <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['newSourceUrl'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <span class="text-danger d-block mt-1"><?php echo e($message); ?></span>
       <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
      </div>
     </div>
     <div class="col-md-5">
      <div class="form-group">
       <label class="control-label fw-bold mb-2">آدرس هدف ریدایرکت</label>
       <input type="url" class="form-control input-shiny" wire:model="newDestinationUrl"
        placeholder="https://" dir="ltr">
       <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['newDestinationUrl'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <span class="text-danger d-block mt-1"><?php echo e($message); ?></span>
       <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
      </div>
     </div>
     <div class="col-md-2 d-flex align-items-end">
      <button wire:click="addRedirect" class="btn btn-gradient-success w-100 py-2">ثبت و
       ذخیره</button>
     </div>
    </div>
   </div>
  </div>
 </div>

 <!-- ابزارها و جستجو -->
 <div class="container px-0 mb-5">
  <div class="bg-light p-4 rounded-3 shadow-sm">
   <div class="row g-4">
    <div class="col-md-6">
     <div class="input-group">
      <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
      <input type="text" class="form-control border-0 shadow-none" wire:model.live="search"
       placeholder="جستجو در ریدایرکت‌ها...">
     </div>
    </div>
    <div class="col-md-6">
     <div class="d-flex gap-2 justify-content-end">
      <button wire:click="export" class="btn btn-gradient-secondary rounded-pill px-4">
       <i class="fas fa-download"></i> خروجی CSV
      </button>
      <button wire:click="deleteSelected" class="btn btn-gradient-danger rounded-pill px-4"
       <?php if(empty($selectedRedirects)): ?> disabled <?php endif; ?>>
       <i class="fas fa-trash"></i> حذف انتخاب‌شده‌ها
      </button>
     </div>
    </div>
   </div>
  </div>
 </div>

 <!-- لیست ریدایرکت‌ها -->
 <div class="container px-0">
  <div class="card shadow-sm">
   <div class="card-body">
    <div class="table-responsive text-nowrap">
     <table class="table table-bordered">
      <thead>
       <tr>
        <th><input type="checkbox" wire:model.live="selectAll" class="form-check-input"></th>
        <th>ردیف</th>
        <th>آدرس مبدا</th>
        <th>آدرس مقصد</th>
        <th>وضعیت</th>
        <th>عملیات</th>
       </tr>
      </thead>
      <tbody>
       <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $redirects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $redirect): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
         <td><input type="checkbox" wire:model.live="selectedRedirects" value="<?php echo e($redirect->id); ?>"
           class="form-check-input"></td>
         <td><?php echo e($redirects->firstItem() + $index); ?></td>
         <td>
          <!--[if BLOCK]><![endif]--><?php if($editId === $redirect->id): ?>
           <input type="url" class="form-control input-shiny" wire:model.live="editSourceUrl">
           <!--[if BLOCK]><![endif]--><?php if($errors->has('newSourceUrl')): ?>
            <span class="text-danger d-block mt-1"><?php echo e($errors->first('newSourceUrl')); ?></span>
           <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
          <?php else: ?>
           <?php echo e($redirect->source_url); ?>

          <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
         </td>
         <td>
          <!--[if BLOCK]><![endif]--><?php if($editId === $redirect->id): ?>
           <input type="url" class="form-control input-shiny" wire:model.live="editDestinationUrl">
           <!--[if BLOCK]><![endif]--><?php if($errors->has('newSourceUrl')): ?>
            <span class="text-danger d-block mt-1"><?php echo e($errors->first('newSourceUrl')); ?></span>
           <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
          <?php else: ?>
           <?php echo e($redirect->destination_url); ?>

          <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
         </td>
         <td>
          <button wire:click="toggleStatus(<?php echo e($redirect->id); ?>)"
           class="badge <?php echo e($redirect->is_active ? 'bg-label-success' : 'bg-label-danger'); ?> border-0 cursor-pointer">
           <?php echo e($redirect->is_active ? 'فعال' : 'غیرفعال'); ?>

          </button>
         </td>
         <td>
          <!--[if BLOCK]><![endif]--><?php if($editId === $redirect->id): ?>
           <div class="d-flex gap-2">
            <button wire:click="updateRedirect" class="btn btn-gradient-success rounded-pill px-3">
             <i class="fas fa-check"></i>
            </button>
            <button wire:click="cancelEdit" class="btn btn-gradient-danger rounded-pill px-3">
             <i class="fas fa-times"></i>
            </button>
           </div>
          <?php else: ?>
           <div class="dropdown">
            <button class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown" type="button">
             <i class="ti ti-dots-vertical"></i>
            </button>
            <div class="dropdown-menu">
             <a class="dropdown-item" wire:click="startEdit(<?php echo e($redirect->id); ?>)" href="javascript:void(0);">
              <i class="ti ti-edit me-1"></i> ویرایش
             </a>
             <a class="dropdown-item" onclick="confirmDelete(<?php echo e($redirect->id); ?>)" href="javascript:void(0);">
              <i class="ti ti-trash me-1"></i> حذف
             </a>
            </div>
           </div>
          <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
         </td>
        </tr>
       <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr>
         <td colspan="6" class="text-center py-5">
          <i class="fas fa-link fs-1 text-muted mb-3"></i>
          <p class="text-muted fw-medium">هیچ ریدایرکتی یافت نشد.</p>
         </td>
        </tr>
       <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
      </tbody>
     </table>
    </div>
    <div class="d-flex justify-content-between mt-4">
     <div class="text-muted">نمایش <?php echo e($redirects->firstItem()); ?> تا <?php echo e($redirects->lastItem()); ?> از
      <?php echo e($redirects->total()); ?> ردیف</div>
     <?php echo e($redirects->links()); ?>

    </div>
   </div>
  </div>
 </div>

 <style>
  .panel-default {
   border: 1px solid #e5e7eb;
   border-radius: 8px;
  }

  .panel-heading {
   background: linear-gradient(135deg, #f9fafb, #e5e7eb);
   padding: 15px;
   font-weight: bold;
   border-bottom: 1px solid #e5e7eb;
   border-radius: 8px 8px 0 0;
   color: #4b5563;
  }

  .panel-body {
   padding: 20px;
  }

  .input-shiny {
   border: 1px solid #d1d5db;
   border-radius: 8px;
   padding: 10px;
   font-size: 14px;
   transition: all 0.3s ease;
   background: #fff;
   box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
  }

  .input-shiny:focus {
   border-color: #4f46e5;
   box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.25), inset 0 1px 3px rgba(0, 0, 0, 0.05);
  }

  .form-control {
   border-radius: 8px;
   border: 1px solid #e5e7eb;
   transition: all 0.3s ease;
  }

  .form-control:focus {
   border-color: #4f46e5;
   box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.25);
  }

  .btn-gradient-success {
   background: linear-gradient(90deg, #10b981, #34d399);
   border: none;
   color: white;
   box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
   transition: all 0.3s ease;
  }

  .btn-gradient-success:hover {
   background: linear-gradient(90deg, #059669, #10b981);
   transform: translateY(-2px);
   box-shadow: 0 5px 12px rgba(0, 0, 0, 0.15);
  }

  .bg-light {
   background: #f9fafb;
   border: 1px solid #e5e7eb;
  }

  .btn-gradient-danger {
   background: linear-gradient(90deg, #f87171, #fca5a5);
   border: none;
   color: white;
   box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
   transition: all 0.3s ease;
  }

  .btn-gradient-danger:hover:not(:disabled) {
   background: linear-gradient(90deg, #ef4444, #f87171);
   transform: translateY(-2px);
   box-shadow: 0 5px 12px rgba(0, 0, 0, 0.15);
  }

  .btn-gradient-danger:disabled {
   background: #d1d5db;
   cursor: not-allowed;
  }

  .btn-gradient-secondary {
   background: linear-gradient(90deg, #6b7280, #9ca3af);
   border: none;
   color: white;
   box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
   transition: all 0.3s ease;
  }

  .btn-gradient-secondary:hover {
   background: linear-gradient(90deg, #4b5563, #6b7280);
   transform: translateY(-2px);
   box-shadow: 0 5px 12px rgba(0, 0, 0, 0.15);
  }

  .table-bordered {
   border: 1px solid #e5e7eb;
  }

  .table-bordered th,
  .table-bordered td {
   border: 1px solid #e5e7eb;
  }

  .rounded-pill {
   border-radius: 50rem;
  }

  .w-8 {
   width: 2rem;
  }

  .h-8 {
   height: 2rem;
  }

  .cursor-pointer {
   cursor: pointer;
  }
 </style>

 <script>
  document.addEventListener('livewire:init', () => {
   Livewire.on('toast', (message, options = {}) => {
    if (typeof toastr === 'undefined') {
     console.error('Toastr is not loaded!');
     return;
    }
    const type = options.type || 'info';
    const toastOptions = {
     positionClass: options.position || 'toast-top-right',
     timeOut: options.timeOut || 3000,
     progressBar: options.progressBar || false,
    };
    if (type === 'success') toastr.success(message, '', toastOptions);
    else if (type === 'error') toastr.error(message, '', toastOptions);
    else if (type === 'warning') toastr.warning(message, '', toastOptions);
    else toastr.info(message, '', toastOptions);
   });

   Livewire.on('confirmDeleteSelected', () => {
    Swal.fire({
     title: 'آیا مطمئن هستید؟',
     text: 'ریدایرکت‌های انتخاب‌شده حذف خواهند شد و قابل بازگشت نیستند!',
     icon: 'warning',
     showCancelButton: true,
     confirmButtonColor: '#ef4444',
     cancelButtonColor: '#d1d5db',
     confirmButtonText: 'بله، حذف کن',
     cancelButtonText: 'خیر',
    }).then((result) => {
     if (result.isConfirmed) {
      window.Livewire.find('<?php echo e($_instance->getId()); ?>').confirmDeleteSelected();
     }
    });
   });
  });

  function confirmDelete(id) {
   Swal.fire({
    title: 'آیا مطمئن هستید؟',
    text: 'این ریدایرکت حذف خواهد شد و قابل بازگشت نیست!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#d1d5db',
    confirmButtonText: 'بله، حذف کن',
    cancelButtonText: 'خیر',
   }).then((result) => {
    if (result.isConfirmed) {
     window.Livewire.find('<?php echo e($_instance->getId()); ?>').deleteRedirect(id);
    }
   });
  }
 </script>
</div>
<?php /**PATH D:\MyProjects\Benobe\panel\resources\views/livewire/admin/tools/redirects.blade.php ENDPATH**/ ?>