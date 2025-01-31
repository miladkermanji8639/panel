$(document).ready(function () {
  const isSidebarActive = $('.sidebar__nav').hasClass('is-active');
  const isContentActive = $('.content').hasClass('is-active');
  $('.drop-toggle').hide(); // مخفی کردن دراپ‌تاگ‌ها در ابتدا
  $('.drop-toggle a').addClass('no-background');
  $('.drop-toggle a').addClass('list-style-squre');
  $('.item-li').on('click', function (event) {
    event.stopPropagation(); // جلوگیری از تریگر شدن رویداد کلیک سایدبار
    const $currentItem = $(this);
    const $thisToggle = $currentItem.find('.drop-toggle');
    const $icon = $currentItem.find('svg.svg-caret-left');
    const isSidebarMinimized =
      $('.sidebar__nav').hasClass('is-active') &&
      $('.content').hasClass('is-active');
    const isDesktopView = window.matchMedia('(min-width: 991px)').matches;
    // بسته کردن سایر دراپ‌تاگ‌ها
    $('.item-li')
      .not($currentItem)
      .removeClass('is-active')
      .find('.drop-toggle')
      .slideUp(300, function () {
        $(this).addClass('d-none');
      });
    $('.item-li')
      .not($currentItem)
      .find('svg.svg-caret-left')
      .css({ transform: 'rotate(180deg)', transition: 'transform 0.3s' });
    //$thisToggle.find("li").css({ "border-right": "3px solid #ddd" });
    // باز کردن دراپ‌تاگ فعلی
    if ($thisToggle.hasClass('d-none')) {
      $currentItem.addClass('is-active');
      if (isDesktopView) {
        if (isSidebarMinimized) {
          $('.sidebar__nav ul li a').on('hover', function () {
            $(this).css({
              'background-color': '#eef2f8',
              transition: 'background-color 400ms ease',
            });
          });
          $('.sidebar__nav').removeClass('is-active'); // باز کردن سایدبار
          $('.content').removeClass('is-active');
          $('.svg-caret-left').removeClass('d-none');
          $('#takhasos-txt').addClass('d-none');
          setTimeout(function () {
            $thisToggle.removeClass('d-none').slideDown(300);
          }, 300); // انتظار برای انیمیشن باز شدن سایدبار
        } else {
          $thisToggle.removeClass('d-none').slideDown(300);
        }
      }
      $thisToggle.removeClass('d-none').slideDown(300);
      $icon.css({ transform: 'rotate(90deg)', transition: 'transform 0.3s' });
    } else {
      $thisToggle.slideUp(300, function () {
        $(this).addClass('d-none');
        $currentItem.removeClass('is-active');
      });
      $icon.css({ transform: 'rotate(180deg)', transition: 'transform 0.3s' });
    }
  });
  $(window).resize(function () {
    const isSidebarMinimized =
      $('.sidebar__nav').hasClass('is-active') &&
      $('.content').hasClass('is-active');
    if (!$('.svg-caret-left').hasClass('d-none')) {
      $('.svg-caret-left').addClass('d-none');
    }
    if ($(window).width() > 991) {
      $('.svg-caret-left').removeClass('d-none');
      if (isSidebarMinimized) {
        $('.svg-caret-left').addClass('d-none');
        if (!$('.fs-11.fw-bold#takhasos-txt').hasClass('d-none')) {
          $('.fs-11.fw-bold#takhasos-txt').addClass('d-none');
        }
      }
      if (
        !$('.sidebar__nav').hasClass('is-active') &&
        !$('.content').hasClass('is-active')
      ) {
        $('.sidebar__nav').addClass('is-active');
        $('.content').addClass('is-active');
      }
    }
  });
  // بسته شدن دراپ‌تاگل با کلیک خارج از آن
  $(document).on('click', function (event) {
    if (!$(event.target).closest('.sidebar__nav').length) {
      $('.drop-toggle').slideUp(300, function () {
        $(this).addClass('d-none');
      });
      $('.item-li')
        .removeClass('is-active')
        .find('svg.svg-caret-left')
        .css({ transform: 'rotate(180deg)', transition: 'transform 0.3s' });
    }
  });
  // مدیریت نمایش متن "کارشناس فیزیوتراپی" و حذف مارجین تاپ 65
  function handleSidebarMinimized() {
    if (
      $('.sidebar__nav').hasClass('is-active') &&
      $('.content').hasClass('is-active')
    ) {
      $('.fs-11.fw-bold#takhasos-txt').addClass('d-none');
      $('ul.mt-65').removeClass('mt-65');
    } else {
      $('.fs-11.fw-bold#takhasos-txt').removeClass('d-none');
      $('ul#mt-65').addClass('mt-65');
    }
  }
  // بررسی اولیه وضعیت سایدبار
  handleSidebarMinimized();
  // زمانی که وضعیت سایدبار تغییر می‌کند، تابع مدیریت را فراخوانی کنید
  $('.sidebar__nav').on('transitionend', handleSidebarMinimized);
  // افزودن رویداد برای ریسایز کردن پنجره
  $(window).resize(function () {
    if ($(window).width() > 991) {
      $('.sidebar__nav').addClass('is-active');
      $('.svg-caret-left').addClass('d-none');
    } else {
      $('.sidebar__nav')
        .removeClass('is-active')
        .find('.drop-toggle')
        .slideUp(300)
        .addClass('d-none');
    }
  });
});
$(function () {
  $('[data-toggle="tooltip"]').tooltip();
});
$(document).ready(function () {
  var dropdownOpen = false;

  // بررسی مقدار ذخیره شده در localStorage
  var selectedClinic = localStorage.getItem('selectedClinic');
  var selectedClinicId = localStorage.getItem('selectedClinicId');

  if (selectedClinic && selectedClinicId) {
    $('.dropdown-label').text(selectedClinic);
    $('.option-card').each(function () {
      if ($(this).attr('data-id') === selectedClinicId) {
        $('.option-card').removeClass('card-active');
        $(this).addClass('card-active');
      }
    });
  } else {
    localStorage.setItem('selectedClinic', 'ویزیت آنلاین به نوبه');
    localStorage.setItem('selectedClinicId', 'default');
  }

  // **بررسی کلینیک‌های غیرفعال و اضافه کردن افکت هشدار**
  function checkInactiveClinics() {
    var hasInactiveClinics = $('.option-card[data-active="0"]').length > 0;
    if (hasInactiveClinics) {
      $('.dropdown-trigger').addClass('warning');
    } else {
      $('.dropdown-trigger').removeClass('warning');
    }
  }

  checkInactiveClinics(); // اجرای بررسی هنگام بارگذاری صفحه

  // باز و بسته کردن دراپ‌داون
  $('.dropdown-trigger').on('click', function (event) {
    event.stopPropagation();
    dropdownOpen = !dropdownOpen;
    $(this).toggleClass('border border-primary');
    $('.my-dropdown-menu').toggleClass('d-none');

    setTimeout(() => {
      dropdownOpen = $('.my-dropdown-menu').is(':visible');
    }, 100);
  });

  // بستن دراپ‌داون هنگام کلیک بیرون
  $(document).on('click', function () {
    if (dropdownOpen) {
      $('.dropdown-trigger').removeClass('border border-primary');
      $('.my-dropdown-menu').addClass('d-none');
      dropdownOpen = false;
    }
  });

  // جلوگیری از بسته شدن هنگام کلیک روی منوی دراپ‌داون
  $('.my-dropdown-menu').on('click', function (event) {
    event.stopPropagation();
  });

  // انتخاب گزینه از دراپ‌داون
  $('.option-card').on('click', function () {
    var selectedText = $(this).find('.font-weight-bold.d-block.fs-15').text().trim();
    var selectedId = $(this).attr('data-id');

    $('.option-card').removeClass('card-active');
    $(this).addClass('card-active');

    $('.dropdown-label').text(selectedText);

    localStorage.setItem('selectedClinic', selectedText);
    localStorage.setItem('selectedClinicId', selectedId);

    // بررسی مجدد کلینیک‌های غیرفعال بعد از انتخاب کلینیک جدید
    checkInactiveClinics();

    $('.dropdown-trigger').removeClass('border border-primary');
    $('.my-dropdown-menu').addClass('d-none');
    dropdownOpen = false;
  });
});







