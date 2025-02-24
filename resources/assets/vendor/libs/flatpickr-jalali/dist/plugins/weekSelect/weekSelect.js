/* flatpickr v4.3.2, @license MIT */
(function (global, factory) {
 typeof exports === 'object' && typeof module !== 'undefined'
  ? (module.exports = factory())
  : typeof define === 'function' && define.amd
    ? define(factory)
    : (global.weekSelect = factory());
})(this, function () {
 'use strict';

 function weekSelectPlugin() {
  return function (fp) {
   function onDayHover(event) {
    var day = event.target;
    if (!day.classList.contains('flatpickr-day')) return;
    var days = fp.days.childNodes;
    var dayIndex = day.$i;
    var dayIndSeven = dayIndex / 7;
    var weekStartDay = days[7 * Math.floor(dayIndSeven)].dateObj;
    var weekEndDay = days[7 * Math.ceil(dayIndSeven + 0.01) - 1].dateObj;
    for (var i = days.length; i--; ) {
     var day_1 = days[i];
     var date = day_1.dateObj;
     if (date > weekEndDay || date < weekStartDay)
      day_1.classList.remove('inRange');
     else day_1.classList.add('inRange');
    }
   }
   function highlightWeek() {
    if (fp.selectedDateElem) {
     fp.weekStartDay =
      fp.days.childNodes[7 * Math.floor(fp.selectedDateElem.$i / 7)].dateObj;
     fp.weekEndDay =
      fp.days.childNodes[
       7 * Math.ceil(fp.selectedDateElem.$i / 7 + 0.01) - 1
      ].dateObj;
    }
    var days = fp.days.childNodes;
    for (var i = days.length; i--; ) {
     var date = days[i].dateObj;
     if (date >= fp.weekStartDay && date <= fp.weekEndDay)
      days[i].classList.add('week', 'selected');
    }
   }
   function clearHover() {
    var days = fp.days.childNodes;
    for (var i = days.length; i--; ) days[i].classList.remove('inRange');
   }
   function onReady() {
    if (fp.daysContainer !== undefined)
     fp.daysContainer.addEventListener('mouseover', onDayHover);
   }
   function onDestroy() {
    if (fp.daysContainer !== undefined)
     fp.daysContainer.removeEventListener('mouseover', onDayHover);
   }
   return {
    onValueUpdate: highlightWeek,
    onMonthChange: highlightWeek,
    onYearChange: highlightWeek,
    onClose: clearHover,
    onParseConfig: function () {
     fp.config.mode = 'single';
     fp.config.enableTime = false;
     fp.config.dateFormat = fp.config.dateFormat
      ? fp.config.dateFormat
      : '\\W\\e\\e\\k #W, Y';
     fp.config.altFormat = fp.config.altFormat
      ? fp.config.altFormat
      : '\\W\\e\\e\\k #W, Y';
    },
    onReady: [onReady, highlightWeek],
    onDestroy: onDestroy,
   };
  };
 }

 return weekSelectPlugin;
});
