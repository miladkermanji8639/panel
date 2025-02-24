﻿'use strict';
var KTUsersViewMain = {
 init: function () {
  document
   .getElementById('kt_modal_sign_out_sesions')
   .addEventListener('click', (t) => {
    t.preventDefault(),
     Swal.fire({
      text: 'آیا مطمئن هستید که می خواهید از سیستم همه جلسات خارج شوید؟',
      icon: 'warning',
      showCancelButton: !0,
      buttonsStyling: !1,
      confirmButtonText: 'بله، از سیستم خارج شوید!',
      cancelButtonText: 'خیر',
      customClass: {
       confirmButton: 'btn btn-primary',
       cancelButton: 'btn btn-active-light',
      },
     }).then(function (t) {
      t.value
       ? Swal.fire({
          text: 'شما از سیستم تمام جلسات خارج شده اید!.',
          icon: 'success',
          buttonsStyling: !1,
          confirmButtonText: 'باشه فهمیدم!',
          customClass: { confirmButton: 'btn btn-primary' },
         })
       : 'cancel' === t.dismiss &&
         Swal.fire({
          text: 'جلسات شما همچنان حفظ می شود!',
          icon: 'error',
          buttonsStyling: !1,
          confirmButtonText: 'باشه فهمیدم!',
          customClass: { confirmButton: 'btn btn-primary' },
         });
     });
   }),
   document
    .querySelectorAll('[data-kt-users-sign-out="single_user"]')
    .forEach((t) => {
     t.addEventListener('click', (n) => {
      n.preventDefault();
      const e = t.closest('tr').querySelectorAll('td')[1].innerText;
      Swal.fire({
       text: 'آیا مطمئن هستید که می خواهید از سیستم خارج شوید؟ ' + e + '?',
       icon: 'warning',
       showCancelButton: !0,
       buttonsStyling: !1,
       confirmButtonText: 'بله، از سیستم خارج شوید!',
       cancelButtonText: 'خیر',
       customClass: {
        confirmButton: 'btn btn-primary',
        cancelButton: 'btn btn-active-light',
       },
      }).then(function (n) {
       n.value
        ? Swal.fire({
           text: 'شما از سیستم خارج شده اید ' + e + '!.',
           icon: 'success',
           buttonsStyling: !1,
           confirmButtonText: 'باشه فهمیدم!',
           customClass: { confirmButton: 'btn btn-primary' },
          }).then(function () {
           t.closest('tr').remove();
          })
        : 'cancel' === n.dismiss &&
          Swal.fire({
           text: e + "'جلسه  هنوز حفظ شده است!.",
           icon: 'error',
           buttonsStyling: !1,
           confirmButtonText: 'باشه فهمیدم!',
           customClass: { confirmButton: 'btn btn-primary' },
          });
      });
     });
    }),
   document
    .getElementById('kt_users_delete_two_step')
    .addEventListener('click', (t) => {
     t.preventDefault(),
      Swal.fire({
       text:
        'آیا مطمئن هستید که می خواهید این احراز هویت دو مرحله ای را حذف کنید؟',
       icon: 'warning',
       showCancelButton: !0,
       buttonsStyling: !1,
       confirmButtonText: 'بله، آن را حذف کنید!',
       cancelButtonText: 'خیر',
       customClass: {
        confirmButton: 'btn btn-primary',
        cancelButton: 'btn btn-active-light',
       },
      }).then(function (t) {
       t.value
        ? Swal.fire({
           text: 'شما این احراز هویت دو مرحله ای را حذف کرده اید!.',
           icon: 'success',
           buttonsStyling: !1,
           confirmButtonText: 'باشه فهمیدم!',
           customClass: { confirmButton: 'btn btn-primary' },
          })
        : 'cancel' === t.dismiss &&
          Swal.fire({
           text: 'احراز هویت دو مرحله ای شما هنوز معتبر است!.',
           icon: 'error',
           buttonsStyling: !1,
           confirmButtonText: 'باشه فهمیدم!',
           customClass: { confirmButton: 'btn btn-primary' },
          });
      });
    }),
   (() => {
    const t = document.getElementById('kt_users_email_notification_form'),
     n = t.querySelector('#kt_users_email_notification_submit'),
     e = t.querySelector('#kt_users_email_notification_cancel');
    n.addEventListener('click', (t) => {
     t.preventDefault(),
      n.setAttribute('data-kt-indicator', 'on'),
      (n.disabled = !0),
      setTimeout(function () {
       n.removeAttribute('data-kt-indicator'),
        (n.disabled = !1),
        Swal.fire({
         text: 'فرم با موفقیت ارسال شد!',
         icon: 'success',
         buttonsStyling: !1,
         confirmButtonText: 'باشه فهمیدم!',
         customClass: { confirmButton: 'btn btn-primary' },
        });
      }, 2e3);
    }),
     e.addEventListener('click', (n) => {
      n.preventDefault(),
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
       }).then(function (n) {
        n.value
         ? t.reset()
         : 'cancel' === n.dismiss &&
           Swal.fire({
            text: 'فرم شما لغو نشده است !.',
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
KTUtil.onDOMContentLoaded(function () {
 KTUsersViewMain.init();
});
