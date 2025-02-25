<div class="payment-setting-content w-100 d-flex justify-content-center mt-4">
  <div class="payment-setting-content-wrapper p-3">
    <div class="top-peayment-setting-card w-100 d-flex justify-content-between border-bottom-ddd">
      <div class="d-flex justify-content-center w-100 border-bottom-primary pb-2 cursor-pointer tab"
        data-tab="gozaresh-mali">
        <span class="font-size-13">گزارش مالی</span>
      </div>
    </div>
    <div class="gozaresh-mali-content mt-3">
      <div class="gozaresh-mali-card-bg w-100 d-flex mt-3 p-3 justify-content-around">
        <div class="d-flex flex-column justify-content-center">
          <span class="text-center text-white font-weight-bold font-size-13">کل در آمد</span>
          <span
            class="text-center text-white font-weight-bold mt-2 font-size-13"><?php echo e(number_format($totalIncome)); ?></span>
          <span class="text-center text-white font-weight-bold mt-2 font-size-13">تومان</span>
        </div>
        <div class="d-flex flex-column justify-content-center">
          <span class="text-center text-white font-weight-bold font-size-13">پرداخت شده</span>
          <span class="text-center text-white font-weight-bold mt-2 font-size-13"><?php echo e(number_format($paid)); ?></span>
          <span class="text-center text-white font-weight-bold mt-2 font-size-13">تومان</span>
        </div>
        <div class="d-flex flex-column justify-content-center">
          <span class="text-center text-white font-weight-bold font-size-13">موجودی</span>
          <span class="text-center text-white font-weight-bold mt-2 font-size-13"><?php echo e(number_format($available)); ?></span>
          <span class="text-center text-white font-weight-bold mt-2 font-size-13">تومان</span>
        </div>
      </div>
      <form wire:submit.prevent="requestSettlement">
        <div>
          <div class="w-100 position-relative mt-4">
            <label for="visit_fee" class="label-top-input-special-takhasos">مبلغ ویزیت (تومان)</label>
            <input type="text" id="visit_fee" wire:model.defer="visit_fee"
              class="form-control h-50 border-radius-4 w-100 text-center" value="<?php echo e($formatted_visit_fee); ?>">
            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['visit_fee'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <span class="text-danger"><?php echo e($message); ?></span>
      <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
          </div>
          <div class="w-100 position-relative mt-4">
            <label for="card_number" class="label-top-input-special-takhasos">شماره کارت</label>
            <input type="text" id="card_number" wire:model.defer="card_number"
              class="form-control h-50 border-radius-4 w-100 text-right" placeholder="1234-1234-1234-1234">
            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['card_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <span class="text-danger"><?php echo e($message); ?></span>
      <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
          </div>
        </div>
        <div class="w-100 mt-3">
          <button type="submit" class="btn btn-primary h-50 w-100">درخواست تسویه حساب</button>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <script>
    document.addEventListener('livewire:init', () => {
      toastr.options = {
        positionClass: 'toast-top-right',
        timeOut: 3000,
        closeButton: true,
      };

      Livewire.on('toast', (event) => {
        toastr.success(event.message);
      });

      // فقط برای آپدیت مقدار بدون کاما
      const visitFeeInput = document.getElementById('visit_fee');
      visitFeeInput.addEventListener('input', function (e) {
        let value = e.target.value.replace(/[^0-9]/g, ''); // فقط اعداد
        window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('visit_fee', value); // مقدار بدون کاما به Livewire
      });

      // فرمت شماره کارت
      const cardNumberInput = document.getElementById('card_number');
      if (cardNumberInput.value) {
        let value = cardNumberInput.value.replace(/[^0-9]/g, '');
        cardNumberInput.value = formatCardNumber(value);
      }
      cardNumberInput.addEventListener('input', function (e) {
        let value = e.target.value.replace(/[^0-9]/g, '');
        if (value.length > 16) value = value.slice(0, 16);
        e.target.value = formatCardNumber(value);
        window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('card_number', e.target.value);
      });

      cardNumberInput.addEventListener('keypress', function (e) {
        if (e.target.value.length >= 19) e.preventDefault();
      });

      function formatCardNumber(value) {
        if (!value) return '';
        let formatted = '';
        for (let i = 0; i < value.length; i++) {
          if (i > 0 && i % 4 === 0) formatted += '-';
          formatted += value[i];
        }
        return formatted;
      }
    });
  </script>
</div><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/livewire/dr/payment-setting-component.blade.php ENDPATH**/ ?>