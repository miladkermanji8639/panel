﻿'use strict';
var KTUsersAddPermission = (function () {
 const t = document.getElementById('kt_modal_add_permission'),
  e = t.querySelector('#kt_modal_add_permission_form'),
  n = new bootstrap.Modal(t);
 return {
  init: function () {
   (() => {
    var o = FormValidation.formValidation(e, {
     fields: {
      permission_name: {
       validators: { notEmpty: { message: 'نام مجوز لازم است' } },
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
    });
    t
     .querySelector('[data-kt-permissions-modal-action="close"]')
     .addEventListener('click', (t) => {
      t.preventDefault(),
       Swal.fire({
        text: 'آیا مطمئن هستید که می خواهید تعطیل کنید؟',
        icon: 'warning',
        showCancelButton: !0,
        buttonsStyling: !1,
        confirmButtonText: 'بله ، ببندش',
        cancelButtonText: 'خیر',
        customClass: {
         confirmButton: 'btn btn-primary',
         cancelButton: 'btn btn-active-light',
        },
       }).then(function (t) {
        t.value && n.hide();
       });
     }),
     t
      .querySelector('[data-kt-permissions-modal-action="cancel"]')
      .addEventListener('click', (t) => {
       t.preventDefault(),
        Swal.fire({
         text: 'آیا مطمئن هستید که می خواهید لغو کنید',
         icon: 'warning',
         showCancelButton: !0,
         buttonsStyling: !1,
         confirmButtonText: 'بله ، آن را لغو کنید!',
         cancelButtonText: 'خیر',
         customClass: {
          confirmButton: 'btn btn-primary',
          cancelButton: 'btn btn-active-light',
         },
        }).then(function (t) {
         t.value
          ? (e.reset(), n.hide())
          : 'cancel' === t.dismiss &&
            Swal.fire({
             text: 'فرم شما لغو نشده است !.',
             icon: 'error',
             buttonsStyling: !1,
             confirmButtonText: 'باشه فهمیدم!',
             customClass: { confirmButton: 'btn btn-primary' },
            });
        });
      });
    const i = t.querySelector('[data-kt-permissions-modal-action="submit"]');
    i.addEventListener('click', function (t) {
     t.preventDefault(),
      o &&
       o.validate().then(function (t) {
        console.log('validated!'),
         'Valid' == t
          ? (i.setAttribute('data-kt-indicator', 'on'),
            (i.disabled = !0),
            setTimeout(function () {
             i.removeAttribute('data-kt-indicator'),
              (i.disabled = !1),
              Swal.fire({
               text: 'فرم با موفقیت ارسال شد!',
               icon: 'success',
               buttonsStyling: !1,
               confirmButtonText: 'باشه فهمیدم!',
               customClass: { confirmButton: 'btn btn-primary' },
              }).then(function (t) {
               t.isConfirmed && n.hide();
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
   })();
  },
 };
})();
KTUtil.onDOMContentLoaded(function () {
 KTUsersAddPermission.init();
});
