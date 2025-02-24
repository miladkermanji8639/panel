<div class="sidebar__nav border-top border-left">
 <span class="bars d-none padding-0-18"></span>
 <a class="header__logo d-none" href="https://netcopy.ir"></a>
 <div class="profile__info border cursor-pointer text-center">
  <div class="avatar__img">
   <img src="<?php echo e(asset('dr-assets/panel/img/pro.jpg')); ?>" class="avatar___img">
   <input type="file" accept="image/*" class="hidden avatar-img__input">
   <div class="v-dialog__container" style="display: block;"></div>
   <div class="box__camera default__avatar"></div>
  </div>
  <span class="profile__name sidebar-full-name">
   <?php
$user = Auth::guard('doctor')->check() ? Auth::guard('doctor')->user() : Auth::guard('secretary')->user();
   ?>
   <?php echo e(optional($user)->first_name); ?> <?php echo e(optional($user)->last_name); ?>

  </span>
  <span class="fs-11 fw-bold" id="takhasos-txt"> <?php echo e($specialtyName ?? 'نامشخص'); ?></span>
 </div>
 <ul class="mt-65" id="mt-65">
  <?php if(Auth::guard('doctor')->check()): ?>
     <li class="item-li i-dashboard <?php echo e(Request::routeIs('dr-panel') ? 'is-active' : ''); ?>">
     <a href="<?php echo e(route('dr-panel')); ?>">داشبورد</a>
     </li>
     <li class="item-li i-courses">
     <a href="#" class="d-flex justify-content-between w-100 align-items-center">
     نوبت اینترنتی
     <div class="d-flex justify-content-end w-100 align-items-center">
     <svg width="6" height="9" class="svg-caret-left" viewBox="0 0 7 11" fill="none"
     xmlns="http://www.w3.org/2000/svg" style="transition: transform 0.3s; transform: rotate(180deg);">
     <path fill-rule="evenodd" clip-rule="evenodd"
     d="M0.658146 0.39655C0.95104 0.103657 1.42591 0.103657 1.71881 0.39655L6.21881 4.89655C6.5117 5.18944 6.5117 5.66432 6.21881 5.95721L1.71881 10.4572C1.42591 10.7501 0.95104 10.7501 0.658146 10.4572C0.365253 10.1643 0.365253 9.68944 0.658146 9.39655L4.62782 5.42688L0.658146 1.45721C0.365253 1.16432 0.365253 0.689443 0.658146 0.39655Z"
     fill="currentColor"></path>
     </svg>
     </div>
     </a>
     <ul class="drop-toggle d-none">
     <li class="item-li <?php echo e(Request::routeIs('dr-appointments') ? 'is-active' : ''); ?>">
     <a href="<?php echo e(route('dr-appointments')); ?>"> مراجعین من</a>
     </li>
     <li class="item-li <?php echo e(Request::routeIs('dr-workhours') ? 'is-active' : ''); ?>">
     <a href="<?php echo e(route('dr-workhours')); ?>"> ساعت کاری</a>
     </li>
     <li class="item-li <?php echo e(Request::routeIs('dr-mySpecialDays') ? 'is-active' : ''); ?>">
     <a href="<?php echo e(route('dr-mySpecialDays')); ?>">روزهای خاص </a>
     </li>
     <li class="item-li <?php echo e(Request::routeIs('dr-manual_nobat_setting') ? 'is-active' : ''); ?>">
     <a href="<?php echo e(route('dr-manual_nobat_setting')); ?>">تنظیمات نوبت دستی</a>
     </li>
     <li class="item-li <?php echo e(Request::routeIs('dr-manual_nobat') ? 'is-active' : ''); ?>">
     <a href="<?php echo e(route('dr-manual_nobat')); ?>">ثبت نوبت دستی</a>
     </li>
     <li class="item-li <?php echo e(Request::routeIs('dr-scheduleSetting') ? 'is-active' : ''); ?>">
     <a href="<?php echo e(route('dr-scheduleSetting')); ?>">تنظیمات نوبت</a>
     </li>
     </ul>
     </li>
     <li
     class="item-li i-moshavere <?php echo e(Request::routeIs('dr-moshavere_setting') || Request::routeIs('dr-moshavere_waiting') || Request::routeIs('consult-term.index') ? 'is-active' : ''); ?>">
     <a href="#" class="d-flex justify-content-between w-100 align-items-center">
     مشاوره
     <div class="d-flex justify-content-end w-100 align-items-center">
     <svg width="6" height="9" class="svg-caret-left" viewBox="0 0 7 11" fill="none"
     xmlns="http://www.w3.org/2000/svg" style="transition: transform 0.3s; transform: rotate(180deg);">
     <path fill-rule="evenodd" clip-rule="evenodd"
     d="M0.658146 0.39655C0.95104 0.103657 1.42591 0.103657 1.71881 0.39655L6.21881 4.89655C6.5117 5.18944 6.5117 5.66432 6.21881 5.95721L1.71881 10.4572C1.42591 10.7501 0.95104 10.7501 0.658146 10.4572C0.365253 10.1643 0.365253 9.68944 0.658146 9.39655L4.62782 5.42688L0.658146 1.45721C0.365253 1.16432 0.365253 0.689443 0.658146 0.39655Z"
     fill="currentColor"></path>
     </svg>
     </div>
     </a>
     <ul class="drop-toggle d-none">
     <li class="item-li i-courses <?php echo e(Request::routeIs('dr-moshavere_setting') ? 'is-active' : ''); ?>">
     <a href="<?php echo e(route('dr-moshavere_setting')); ?>">برنامه ریزی مشاوره</a>
     </li>
     <li class="item-li i-user__inforamtion <?php echo e(Request::routeIs('dr-moshavere_waiting') ? 'is-active' : ''); ?>">
     <a href="<?php echo e(route('dr-moshavere_waiting')); ?>">گزارش مشاوره</a>
     </li>
     <li class="item-li i-user__inforamtion <?php echo e(Request::routeIs('dr-mySpecialDays-counseling') ? 'is-active' : ''); ?>">
     <a href="<?php echo e(route('dr-mySpecialDays-counseling')); ?>">روز های خاص</a>
     </li>
     <li class="item-li i-user__inforamtion <?php echo e(Request::routeIs('consult-term.index') ? 'is-active' : ''); ?>">
     <a href="<?php echo e(route('consult-term.index')); ?>">قوانین مشاوره</a>
     </li>
     </ul>
     </li>
      <li class="item-li i-checkout__request <?php echo e(Request::routeIs('dr-services.index') ? 'is-active' : ''); ?>">
       <a href="<?php echo e(route('dr-services.index')); ?>"> خدمات</a>
      </li>
     <li
     class="item-li i-banners <?php echo e(Request::routeIs('prescription.index') || Request::routeIs('providers.index') || Request::routeIs('favorite.templates.index') || Request::routeIs('templates.favorite.service.index') ? 'is-active' : ''); ?>">
     <a href="#">
     نسخه الکترونیک
     <div class="d-flex justify-content-end w-100 align-items-center">
     <svg width="6" height="9" class="svg-caret-left" viewBox="0 0 7 11" fill="none"
     xmlns="http://www.w3.org/2000/svg" style="transition: transform 0.3s; transform: rotate(180deg);">
     <path fill-rule="evenodd" clip-rule="evenodd"
     d="M0.658146 0.39655C0.95104 0.103657 1.42591 0.103657 1.71881 0.39655L6.21881 4.89655C6.5117 5.18944 6.5117 5.66432 6.21881 5.95721L1.71881 10.4572C1.42591 10.7501 0.95104 10.7501 0.658146 10.4572C0.365253 10.1643 0.365253 9.68944 0.658146 9.39655L4.62782 5.42688L0.658146 1.45721C0.365253 1.16432 0.365253 0.689443 0.658146 0.39655Z"
     fill="currentColor"></path>
     </svg>
     </div>
     </a>
     <ul class="drop-toggle d-none">
     <li class="item-li i-courses <?php echo e(Request::routeIs('prescription.index') ? 'is-active' : ''); ?>">
     <a href="<?php echo e(route('prescription.index')); ?>"> نسخه های ثبت شده</a>
     </li>
     <li class="item-li i-courses <?php echo e(Request::routeIs('providers.index') ? 'is-active' : ''); ?>">
     <a href="<?php echo e(route('providers.index')); ?>"> بیمه های من</a>
     </li>
     <li class="item-li i-courses <?php echo e(Request::routeIs('favorite.templates.index') ? 'is-active' : ''); ?>">
     <a href="<?php echo e(route('favorite.templates.index')); ?>">نسخه پر استفاده </a>
     </li>
     <li class="item-li i-courses <?php echo e(Request::routeIs('templates.favorite.service.index') ? 'is-active' : ''); ?>">
     <a href="<?php echo e(route('templates.favorite.service.index')); ?>">اقلام پر استفاده </a>
     </li>
     </ul>
     </li>
     <li
     class="item-li i-my__peyments <?php echo e(Request::routeIs('dr-wallet') || Request::routeIs('dr-payment-setting') ? 'is-active' : ''); ?> d-flex flex-column justify-content-center"
     id="gozaresh-mali">
     <a href="#" class="d-flex justify-content-between w-100 align-items-center">
     گزارش مالی
     <div class="d-flex justify-content-end w-100 align-items-center">
     <svg width="6" height="9" class="svg-caret-left" viewBox="0 0 7 11" fill="none"
     xmlns="http://www.w3.org/2000/svg" style="transition: transform 0.3s; transform: rotate(180deg);">
     <path fill-rule="evenodd" clip-rule="evenodd"
     d="M0.658146 0.39655C0.95104 0.103657 1.42591 0.103657 1.71881 0.39655L6.21881 4.89655C6.5117 5.18944 6.5117 5.66432 6.21881 5.95721L1.71881 10.4572C1.42591 10.7501 0.95104 10.7501 0.658146 10.4572C0.365253 10.1643 0.365253 9.68944 0.658146 9.39655L4.62782 5.42688L0.658146 1.45721C0.365253 1.16432 0.365253 0.689443 0.658146 0.39655Z"
     fill="currentColor"></path>
     </svg>
     </div>
     </a>
     <ul class="drop-toggle d-none">
     <li class="item-li i-user__inforamtion <?php echo e(Request::routeIs('dr-wallet') ? 'is-active' : ''); ?>">
     <a href="<?php echo e(route('dr-wallet')); ?>"> کیف پول</a>
     </li>
     <li class="item-li i-user__inforamtion <?php echo e(Request::routeIs('dr-payment-setting') ? 'is-active' : ''); ?>">
     <a href="<?php echo e(route('dr-payment-setting')); ?>"> پرداخت</a>
     </li>
     </ul>
     </li>
     <li
     class="item-li i-checkout__request <?php echo e(Request::routeIs('dr-patient-records') ? 'is-active' : ''); ?> d-flex flex-column justify-content-center"
     id="gozaresh-mali">
     <a href="#" class="d-flex justify-content-between w-100 align-items-center">
     پرونده الکترونیک
     <div class="d-flex justify-content-end w-100 align-items-center">
     <svg width="6" height="9" class="svg-caret-left" viewBox="0 0 7 11" fill="none"
     xmlns="http://www.w3.org/2000/svg" style="transition: transform 0.3s; transform: rotate(180deg);">
     <path fill-rule="evenodd" clip-rule="evenodd"
     d="M0.658146 0.39655C0.95104 0.103657 1.42591 0.103657 1.71881 0.39655L6.21881 4.89655C6.5117 5.18944 6.5117 5.66432 6.21881 5.95721L1.71881 10.4572C1.42591 10.7501 0.95104 10.7501 0.658146 10.4572C0.365253 10.1643 0.365253 9.68944 0.658146 9.39655L4.62782 5.42688L0.658146 1.45721C0.365253 1.16432 0.365253 0.689443 0.658146 0.39655Z"
     fill="currentColor"></path>
     </svg>
     </div>
     </a>
     <ul class="drop-toggle d-none">
     <li class="item-li i-user__inforamtion <?php echo e(Request::routeIs('dr-patient-records') ? 'is-active' : ''); ?>">
     <a href="<?php echo e(route('dr-patient-records')); ?>">پرونده الکترونیک</a>
     </li>
     </ul>
     </li>
     <li
     class="item-li i-user__secratary <?php echo e(Request::routeIs('dr-secretary-management') ? 'is-active' : ''); ?> d-flex flex-column justify-content-center"
     id="gozaresh-mali">
     <a href="#" class="d-flex justify-content-between w-100 align-items-center">
     منشی
     <div class="d-flex justify-content-end w-100 align-items-center">
     <svg width="6" height="9" class="svg-caret-left" viewBox="0 0 7 11" fill="none"
     xmlns="http://www.w3.org/2000/svg" style="transition: transform 0.3s; transform: rotate(180deg);">
     <path fill-rule="evenodd" clip-rule="evenodd"
     d="M0.658146 0.39655C0.95104 0.103657 1.42591 0.103657 1.71881 0.39655L6.21881 4.89655C6.5117 5.18944 6.5117 5.66432 6.21881 5.95721L1.71881 10.4572C1.42591 10.7501 0.95104 10.7501 0.658146 10.4572C0.365253 10.1643 0.365253 9.68944 0.658146 9.39655L4.62782 5.42688L0.658146 1.45721C0.365253 1.16432 0.365253 0.689443 0.658146 0.39655Z"
     fill="currentColor"></path>
     </svg>
     </div>
     </a>
     <ul class="drop-toggle d-none">
     <li class="item-li i-user__inforamtion <?php echo e(Request::routeIs('dr-secretary-management') ? 'is-active' : ''); ?>">
     <a href="<?php echo e(route('dr-secretary-management')); ?>"> مدیریت منشی ها</a>
     </li>
     </ul>
     </li>
     <li
     class="item-li i-clinic <?php echo e(Request::routeIs('dr-clinic-management') ? 'is-active' : ''); ?> d-flex flex-column justify-content-center"
     id="gozaresh-mali">
     <a href="#" class="d-flex justify-content-between w-100 align-items-center">
     مطب
     <div class="d-flex justify-content-end w-100 align-items-center">
     <svg width="6" height="9" class="svg-caret-left" viewBox="0 0 7 11" fill="none"
     xmlns="http://www.w3.org/2000/svg" style="transition: transform 0.3s; transform: rotate(180deg);">
     <path fill-rule="evenodd" clip-rule="evenodd"
     d="M0.658146 0.39655C0.95104 0.103657 1.42591 0.103657 1.71881 0.39655L6.21881 4.89655C6.5117 5.18944 6.5117 5.66432 6.21881 5.95721L1.71881 10.4572C1.42591 10.7501 0.95104 10.7501 0.658146 10.4572C0.365253 10.1643 0.365253 9.68944 0.658146 9.39655L4.62782 5.42688L0.658146 1.45721C0.365253 1.16432 0.365253 0.689443 0.658146 0.39655Z"
     fill="currentColor"></path>
     </svg>
     </div>
     </a>
     <ul class="drop-toggle d-none">
     <li
     class="item-li i-user__inforamtion <?php echo e(Request::routeIs('dr-clinic-management') || Request::routeIs('dr-office-gallery') || Request::routeIs('dr-office-medicalDoc') ? 'is-active' : ''); ?>">
     <a href="<?php echo e(route('dr-clinic-management')); ?>"> مدیریت مطب</a>
     </li>
     <li class="item-li i-user__inforamtion <?php echo e(Request::routeIs('dr-office-gallery') ? 'is-active' : ''); ?>">
     <a href="<?php echo e(route('dr-office-gallery')); ?>"> گالری تصاویر </a>
     </li>
     <li class="item-li i-user__inforamtion <?php echo e(Request::routeIs('dr-office-medicalDoc') ? 'is-active' : ''); ?>">
     <a href="<?php echo e(route('dr-office-medicalDoc')); ?>"> مدارک من </a>
     </li>
     </ul>
     </li>
     <li class="item-li i-checkout__request <?php echo e(Request::routeIs('dr-bime') ? 'is-active' : ''); ?>">
     <a href="<?php echo e(route('dr-bime')); ?>">بیمه ها</a>
     </li>
     <li class="item-li i-checkout__request <?php echo e(Request::routeIs('dr-secretary-permissions') ? 'is-active' : ''); ?>">
     <a href="<?php echo e(route('dr-secretary-permissions')); ?>">دسترسی ها</a>
     </li>
     <li
     class="item-li i-users <?php echo e(Request::routeIs('dr-edit-profile') || Request::routeIs('dr-edit-profile-security') || Request::routeIs('dr-edit-profile-upgrade') || Request::routeIs('dr-my-performance') || Request::routeIs('dr-subuser') || Request::routeIs('my-dr-appointments') ? 'is-active' : ''); ?> d-flex flex-column justify-content-center"
     id="hesab-karbari">
     <a href="#" class="d-flex justify-content-between w-100 align-items-center">
     حساب کاربری
     <div class="d-flex justify-content-end w-100 align-items-center">
     <svg width="6" height="9" class="svg-caret-left" viewBox="0 0 7 11" fill="none"
     xmlns="http://www.w3.org/2000/svg" style="transition: transform 0.3s; transform: rotate(180deg);">
     <path fill-rule="evenodd" clip-rule="evenodd"
     d="M0.658146 0.39655C0.95104 0.103657 1.42591 0.103657 1.71881 0.39655L6.21881 4.89655C6.5117 5.18944 6.5117 5.66432 6.21881 5.95721L1.71881 10.4572C1.42591 10.7501 0.95104 10.7501 0.658146 10.4572C0.365253 10.1643 0.365253 9.68944 0.658146 9.39655L4.62782 5.42688L0.658146 1.45721C0.365253 1.16432 0.365253 0.689443 0.658146 0.39655Z"
     fill="currentColor"></path>
     </svg>
     </div>
     </a>
     <ul class="drop-toggle d-none">
     <li class="item-li <?php echo e(Request::routeIs('dr-edit-profile') ? 'is-active' : ''); ?>">
     <a href="<?php echo e(route('dr-edit-profile')); ?>">حساب کاربری</a>
     </li>
     <li class="item-li <?php echo e(Request::routeIs('my-dr-appointments') ? 'is-active' : ''); ?>">
     <a href="<?php echo e(route('my-dr-appointments')); ?>"> نوبت های من</a>
     </li>
     <li class="item-li <?php echo e(Request::routeIs('dr-edit-profile-security') ? 'is-active' : ''); ?>">
     <a href="<?php echo e(route('dr-edit-profile-security')); ?>"> امنیت</a>
     </li>
     <li class="item-li <?php echo e(Request::routeIs('dr-edit-profile-upgrade') ? 'is-active' : ''); ?>">
     <a href="<?php echo e(route('dr-edit-profile-upgrade')); ?>">ارتقا حساب</a>
     </li>
     <li class="item-li <?php echo e(Request::routeIs('dr-my-performance') ? 'is-active' : ''); ?>">
     <a href="<?php echo e(route('dr-my-performance')); ?>"> عملکرد و رتبه من</a>
     </li>
     <li class="item-li <?php echo e(Request::routeIs('dr-subuser') ? 'is-active' : ''); ?>">
     <a href="<?php echo e(route('dr-subuser')); ?>">کاربران زیر مجموعه</a>
     </li>
     </ul>
     </li>
     <li
     class="item-li i-comments <?php echo e(Request::routeIs('dr-panel-tickets') ? 'is-active' : ''); ?> d-flex flex-column justify-content-center"
     id="gozaresh-mali">
     <a href="#" class="d-flex justify-content-between w-100 align-items-center">
     پیام
     <div class="d-flex justify-content-end w-100 align-items-center">
     <svg width="6" height="9" class="svg-caret-left" viewBox="0 0 7 11" fill="none"
     xmlns="http://www.w3.org/2000/svg" style="transition: transform 0.3s; transform: rotate(180deg);">
     <path fill-rule="evenodd" clip-rule="evenodd"
     d="M0.658146 0.39655C0.95104 0.103657 1.42591 0.103657 1.71881 0.39655L6.21881 4.89655C6.5117 5.18944 6.5117 5.66432 6.21881 5.95721L1.71881 10.4572C1.42591 10.7501 0.95104 10.7501 0.658146 10.4572C0.365253 10.1643 0.365253 9.68944 0.658146 9.39655L4.62782 5.42688L0.658146 1.45721C0.365253 1.16432 0.365253 0.689443 0.658146 0.39655Z"
     fill="currentColor"></path>
     </svg>
     </div>
     </a>
     <ul class="drop-toggle d-none">
     <li class="item-li i-tickets <?php echo e(Request::routeIs('dr-panel-tickets') ? 'is-active' : ''); ?>">
     <a href="<?php echo e(route('dr-panel-tickets')); ?>">تیکت ها</a>
     </li>
     <li class="item-li i-comments ">
     <a href="">صفحه گفتگو</a>
     </li>
     </ul>
     </li>
     <li class="item-li i-transactions <?php echo e(Request::routeIs('dr-my-performance-chart') ? 'is-active' : ''); ?>">
     <a href="<?php echo e(route('dr-my-performance-chart')); ?>">آمار ونمودار</a>
     </li>
     <li class="item-li i-exit">
     <a href="<?php echo e(route('dr.auth.logout')); ?>" class="logout-sidebar"> خروج</a>
     </li>
  <?php elseif(Auth::guard('secretary')->check()): ?>
   <?php
   $permissions = is_array($permissions) ? $permissions : json_decode($permissions ?? '[]', true);
   ?>
   <?php $__currentLoopData = config('permissions'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permissionKey => $permissionData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if(in_array($permissionKey, $permissions)): ?>
     <li
      class="item-li <?php echo e(Request::routeIs($permissionData['routes'][0] ?? '') ? 'is-active' : ''); ?> <?php echo e($permissionData['icon']); ?>">
      <a
       href="<?php echo e(is_array($permissionData['routes']) && !empty($permissionData['routes'][0]) ? route($permissionData['routes'][0]) : (is_string($permissionData['routes']) ? route($permissionData['routes']) : '#')); ?>"
       class="d-flex justify-content-between w-100 align-items-center">
       <?php echo e($permissionData['title']); ?>

       <?php if(
            $permissionKey !== 'dashboard' &&
            $permissionKey !== 'insurance' &&
            $permissionKey !== 'permissions' &&
            $permissionKey !== 'statistics'
         ): ?>
        <div class="d-flex justify-content-end w-100 align-items-center">
         <svg width="6" height="9" class="svg-caret-left" viewBox="0 0 7 11" fill="none"
          xmlns="http://www.w3.org/2000/svg" style="transition: transform 0.3s; transform: rotate(180deg);">
          <path fill-rule="evenodd" clip-rule="evenodd"
           d="M0.658146 0.39655C0.95104 0.103657 1.42591 0.103657 1.71881 0.39655L6.21881 4.89655C6.5117 5.18944 6.5117 5.66432 6.21881 5.95721L1.71881 10.4572C1.42591 10.7501 0.95104 10.7501 0.658146 10.4572C0.365253 10.1643 0.365253 9.68944 0.658146 9.39655L4.62782 5.42688L0.658146 1.45721C0.365253 1.16432 0.365253 0.689443 0.658146 0.39655Z"
           fill="currentColor"></path>
         </svg>
        </div>
       <?php endif; ?>
      </a>
      <?php if(!empty($permissionData['routes']) && is_array($permissionData['routes'])): ?>
       <ul class="drop-toggle d-none">
        <?php $__currentLoopData = $permissionData['routes']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $routeKey => $routeTitle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
         <?php if(is_string($routeKey) && Route::has($routeKey)): ?>
          <li class="item-li <?php echo e(Request::routeIs($routeKey) ? 'is-active' : ''); ?>">
           <a href="<?php echo e(route($routeKey)); ?>"><?php echo e($routeTitle); ?></a>
          </li>
         <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
       </ul>
      <?php endif; ?>
     </li>
    <?php endif; ?>
   <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

   <li class="item-li i-exit">
    <a href="<?php echo e(route('dr.auth.logout')); ?>" class="logout-sidebar"> خروج</a>
   </li>
  <?php endif; ?>
 </ul>
</div>
<?php /**PATH D:\MyProjects\Benobe\panel\resources\views/dr/panel/layouts/partials/sidebar.blade.php ENDPATH**/ ?>