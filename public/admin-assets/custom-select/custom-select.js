/* خط 1: وقتی صفحه لود شد */
document.addEventListener('DOMContentLoaded', () => {
 /* خط 2: همه سلکت‌های کاستوم رو پیدا کن */
 document.querySelectorAll('.custom-select').forEach(wrapper => {
  /* خط 3: اینپوت اصلی */
  const input = wrapper.querySelector('.custom-select-input');
  /* خط 4: کانتینر گزینه‌ها */
  const optionsContainer = wrapper.querySelector('.custom-select-options');
  /* خط 5: دکمه پاک کردن */
  const clearBtn = wrapper.querySelector('.custom-select-clear');
  /* خط 6: اینپوت جستجو */
  const searchInput = wrapper.querySelector('.custom-select-search');
  /* خط 7: همه گزینه‌ها */
  const options = wrapper.querySelectorAll('.custom-select-option');
  /* خط 8: مقدار اولیه از data-value */
  let selectedValue = input.dataset.value || '';

  /* خط 9: اگه مقدار اولیه داشت */
  if (selectedValue) {
   /* خط 10: گزینه انتخاب‌شده رو پیدا کن */
   const selectedOption = wrapper.querySelector(`.custom-select-option[data-value="${selectedValue}"]`);
   /* خط 11: اگه پیدا شد */
   if (selectedOption) {
    /* خط 12: متنش رو توی اینپوت بذار */
    input.value = selectedOption.textContent;
   }
  }

  /* خط 13: کلیک روی اینپوت */
  input.addEventListener('click', () => {

   /* خط 14: باز و بسته کردن گزینه‌ها */
   optionsContainer.classList.toggle('open');
   /* خط 15: فوکوس روی جستجو */
   searchInput.focus();
  });

  /* خط 16: کلیک بیرون برای بستن */
  document.addEventListener('click', (e) => {
   /* خط 17: اگه بیرون از سلکت کلیک شد */
   if (!wrapper.contains(e.target)) {
    /* خط 18: ببند */
    optionsContainer.classList.remove('open');
   }
  });

  /* خط 19: برای هر گزینه */
  options.forEach(option => {
   /* خط 20: کلیک روی گزینه */
   option.addEventListener('click', () => {
    /* خط 21: مقدار گزینه */
    const value = option.dataset.value;
    /* خط 22: متن گزینه */
    const text = option.textContent;
    /* خط 23: ست کردن متن توی اینپوت */
    input.value = text;
    /* خط 24: ست کردن مقدار توی دیتاست */
    input.dataset.value = value;
    /* خط 25: حذف کلاس انتخاب‌شده از همه */
    options.forEach(opt => opt.classList.remove('selected'));
    /* خط 26: اضافه کردن کلاس به گزینه انتخاب‌شده */
    option.classList.add('selected');
    /* خط 27: بستن گزینه‌ها */
    optionsContainer.classList.remove('open');
    /* خط 28: ارسال رویداد به Livewire */
    input.dispatchEvent(new Event('input'));
   });
  });

  /* خط 29: جستجو توی گزینه‌ها */
  searchInput.addEventListener('input', (e) => {
   /* خط 30: متن فیلتر */
   const filter = e.target.value.toLowerCase();
   /* خط 31: برای هر گزینه */
   options.forEach(option => {
    /* خط 32: متن گزینه */
    const text = option.textContent.toLowerCase();
    /* خط 33: نمایش یا مخفی کردن بر اساس فیلتر */
    option.style.display = text.includes(filter) ? 'block' : 'none';
   });
  });

  /* خط 34: پاک کردن مقدار */
  clearBtn.addEventListener('click', () => {

   /* خط 35: خالی کردن اینپوت */
   input.value = '';
   /* خط 36: خالی کردن دیتاست */
   input.dataset.value = '';
   /* خط 37: حذف کلاس انتخاب‌شده از همه */
   options.forEach(opt => opt.classList.remove('selected'));
   /* خط 38: بستن گزینه‌ها */
   optionsContainer.classList.remove('open');
   /* خط 39: ارسال رویداد به Livewire */
   input.dispatchEvent(new Event('input'));
  });
 });
});