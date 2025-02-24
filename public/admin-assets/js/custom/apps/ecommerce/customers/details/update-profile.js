﻿'use strict';
var KTEcommerceUpdateProfile = (function () {
 var e, t, i;
 return {
  init: function () {
   (i = document.querySelector('#kt_ecommerce_customer_profile')),
    (e = i.querySelector('#kt_ecommerce_customer_profile_submit')),
    (t = FormValidation.formValidation(i, {
     fields: {
      name: { validators: { notEmpty: { message: 'نام الزامی است' } } },
      gen_email: {
       validators: { notEmpty: { message: 'ایمیل مورد نیاز است' } },
      },
     },
     plugins: {
      trigger: new FormValidation.plugins.Trigger(),
      bootstrap: new FormValidation.plugins.Bootstrap5({
       rowSelector: '.fv-row',
       eleInvalidClass: '',
       eleValidClass: '',
      }),
     },
    })),
    e.addEventListener('click', function (i) {
     i.preventDefault(),
      t &&
       t.validate().then(function (t) {
        console.log('validated!'),
         'Valid' == t
          ? (e.setAttribute('data-kt-indicator', 'on'),
            (e.disabled = !0),
            setTimeout(function () {
             e.removeAttribute('data-kt-indicator'),
              Swal.fire({
               text: 'Your profile has been saved!',
               icon: 'success',
               buttonsStyling: !1,
               confirmButtonText: 'باشه فهمیدم!',
               customClass: { confirmButton: 'btn btn-primary' },
              }).then(function (t) {
               t.isConfirmed && (e.disabled = !1);
              });
            }, 2e3))
          : Swal.fire({
             text:
              'متأسفیم ، به نظر می رسد برخی خطاها شناسایی شده است ، لطفاً دوباره امتحان کنید.',
             icon: 'error',
             buttonsStyling: !1,
             confirmButtonText: 'باشه فهمیدم!',
             customClass: { confirmButton: 'btn btn-primary' },
            });
       });
    });
  },
 };
})();
KTUtil.onDOMContentLoaded(function () {
 KTEcommerceUpdateProfile.init();
});
