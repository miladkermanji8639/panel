document.addEventListener('DOMContentLoaded', function () {
 const radioButtons = document.querySelectorAll(
  'input[name="calculation_method"]'
 );
 const contentDivs = [
  document.getElementById('selected-number-one'),
  document.getElementById('selected-number-two'),
  document.getElementById('selected-number-three'),
 ].filter(div => div !== null); // حذف `null` ها از آرایه

 function updateVisibleDiv() {
  // همه div ها را پنهان کن
  contentDivs.forEach((div) => {
   div.classList.add('d-none');
  });

  // div مرتبط با رادیو چک‌شده را نمایش بده
  contentDivs.forEach((div, index) => {
   if (radioButtons[index] && radioButtons[index].checked) {
    div.classList.remove('d-none');
   }
  });
 }

 radioButtons.forEach((radio) => {
  radio.addEventListener('change', updateVisibleDiv);
 });

 // نمایش div صحیح هنگام بارگذاری صفحه
 updateVisibleDiv();
});
