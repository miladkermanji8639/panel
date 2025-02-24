﻿'use strict';
var KTModalCreateProjectType = (function () {
 var e, t, o, r;
 return {
  init: function () {
   (o = KTModalCreateProject.getForm()),
    (r = KTModalCreateProject.getStepperObj()),
    (e = KTModalCreateProject.getStepper().querySelector(
     '[data-kt-element="type-next"]',
    )),
    (t = FormValidation.formValidation(o, {
     fields: {
      project_type: {
       validators: { notEmpty: { message: 'نوع پروژه مورد نیاز است' } },
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
    e.addEventListener('click', function (o) {
     o.preventDefault(),
      (e.disabled = !0),
      t &&
       t.validate().then(function (t) {
        console.log('validated!'),
         o.preventDefault(),
         'Valid' == t
          ? (e.setAttribute('data-kt-indicator', 'on'),
            setTimeout(function () {
             e.removeAttribute('data-kt-indicator'),
              (e.disabled = !1),
              r.goNext();
            }, 1e3))
          : ((e.disabled = !1),
            Swal.fire({
             text:
              'متأسفیم ، به نظر می رسد برخی خطاها شناسایی شده است ، لطفاً دوباره امتحان کنید.',
             icon: 'error',
             buttonsStyling: !1,
             confirmButtonText: 'باشه فهمیدم!',
             customClass: { confirmButton: 'btn btn-primary' },
            }));
       });
    });
  },
 };
})();
'undefined' != typeof module &&
 void 0 !== module.exports &&
 (window.KTModalCreateProjectType = module.exports = KTModalCreateProjectType);
