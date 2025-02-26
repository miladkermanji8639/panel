<?php $__env->startSection('styles'); ?>
  <link type="text/css" href="<?php echo e(asset('dr-assets/panel/css/panel.css')); ?>" rel="stylesheet" />
  <link type="text/css" href="<?php echo e(asset('dr-assets/panel/css/my-performance/chart/chart.css')); ?>" rel="stylesheet" />

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php $__env->startSection('bread-crumb-title', 'آمار و نمودار'); ?>
<div class="chart-content w-100 d-flex flex-column align-items-center mt-4">

  <!-- 📊 نمودار ۱: تعداد ویزیت‌ها به تفکیک وضعیت -->
  <div class="chart-container">
    <h4 class="section-title">📊 تعداد ویزیت‌ها به تفکیک وضعیت</h4>
    <canvas id="doctor-performance-chart"></canvas>
  </div>

  <!-- 💰 نمودار ۲: درآمد ماهانه -->
  <div class="chart-container">
    <h4 class="section-title">💰 درآمد ماهانه</h4>
    <canvas id="doctor-income-chart"></canvas>
  </div>

  <!-- 👨‍⚕️ نمودار ۳: تعداد بیماران جدید -->
  <div class="chart-container">
    <h4 class="section-title">👨‍⚕️ بیماران جدید</h4>
    <canvas id="doctor-patient-chart"></canvas>
  </div>

  <!-- 📈 نمودار ۴: وضعیت نوبت‌ها -->
  <div class="chart-container">
    <h4 class="section-title">📈 وضعیت نوبت‌ها</h4>
    <canvas id="doctor-status-chart"></canvas>
  </div>

  <!-- 🕒 نمودار ۵: میانگین مدت زمان نوبت‌ها -->
  <div class="chart-container">
    <h4 class="section-title">🕒 میانگین مدت زمان نوبت‌ها</h4>
    <canvas id="doctor-duration-chart"></canvas>
  </div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
  <script src="<?php echo e(asset('dr-assets/panel/js/calendar/custm-calendar.js')); ?>"></script>


  <script src="<?php echo e(asset('dr-assets/panel/js/dr-panel.js')); ?>"></script>
  <script>
   var appointmentsSearchUrl = "<?php echo e(route('search.appointments')); ?>";
   var updateStatusAppointmentUrl =
    "<?php echo e(route('updateStatusAppointment', ':id')); ?>";
  </script>
  <script>
   document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('showModal')) {
     // فرض کنید ID مودال شما "activation-modal" است
     $('#activation-modal').modal('show');
    }
   });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script>
    $(document).ready(function () {
    let dropdownOpen = false;
    let selectedClinic = localStorage.getItem('selectedClinic');
    let selectedClinicId = localStorage.getItem('selectedClinicId');
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

    function checkInactiveClinics() {
      var hasInactiveClinics = $('.option-card[data-active="0"]').length > 0;
      if (hasInactiveClinics) {
      $('.dropdown-trigger').addClass('warning');
      } else {
      $('.dropdown-trigger').removeClass('warning');
      }
    }
    checkInactiveClinics();

    $('.dropdown-trigger').on('click', function (event) {
      event.stopPropagation();
      dropdownOpen = !dropdownOpen;
      $(this).toggleClass('border border-primary');
      $('.my-dropdown-menu').toggleClass('d-none');
      setTimeout(() => {
      dropdownOpen = $('.my-dropdown-menu').is(':visible');
      }, 100);
    });

    $(document).on('click', function () {
      if (dropdownOpen) {
      $('.dropdown-trigger').removeClass('border border-primary');
      $('.my-dropdown-menu').addClass('d-none');
      dropdownOpen = false;
      }
    });

    $('.my-dropdown-menu').on('click', function (event) {
      event.stopPropagation();
    });

    $('.option-card').on('click', function () {
      var selectedText = $(this).find('.font-weight-bold.d-block.fs-15').text().trim();
      var selectedId = $(this).attr('data-id');
      $('.option-card').removeClass('card-active');
      $(this).addClass('card-active');
      $('.dropdown-label').text(selectedText);

      localStorage.setItem('selectedClinic', selectedText);
      localStorage.setItem('selectedClinicId', selectedId);
      checkInactiveClinics();
      $('.dropdown-trigger').removeClass('border border-primary');
      $('.my-dropdown-menu').addClass('d-none');
      dropdownOpen = false;

      // ریلود صفحه با پارامتر جدید
      window.location.href = window.location.pathname + "?selectedClinicId=" + selectedId;
    });
    });
    document.addEventListener("DOMContentLoaded", function () {
    let selectedClinicId = localStorage.getItem('selectedClinicId') || 'default';

    function loadCharts() {
      $.ajax({
      url: "<?php echo e(route('dr-my-performance-chart-data')); ?>",
      method: 'GET',
      data: {
        clinic_id: selectedClinicId
      },
      success: function (response) {
        renderPerformanceChart(response.appointments);
        renderIncomeChart(response.monthlyIncome);
        renderPatientChart(response.newPatients);
        renderStatusChart(response.appointmentStatusByMonth);
        renderDurationChart(response.averageDurationByMonth);
      },
      error: function () {
        alert('خطا در دریافت اطلاعات نمودارها');
      }
      });
    }

    /**
     * 📊 نمودار تعداد ویزیت‌ها
     */
    function renderPerformanceChart(data) {
      let ctx = document.getElementById('doctor-performance-chart').getContext('2d');
      if (window.performanceChart) window.performanceChart.destroy();

      let labels = data.map(item => item.month);
      let scheduled = data.map(item => item.scheduled_count);
      let attended = data.map(item => item.attended_count);
      let missed = data.map(item => item.missed_count);
      let cancelled = data.map(item => item.cancelled_count);

      window.performanceChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
        label: 'برنامه‌ریزی‌شده',
        data: scheduled,
        backgroundColor: '#36a2eb'
        },
        {
        label: 'انجام‌شده',
        data: attended,
        backgroundColor: '#4bc0c0'
        },
        {
        label: 'غیبت',
        data: missed,
        backgroundColor: '#ff6384'
        },
        {
        label: 'لغو‌شده',
        data: cancelled,
        backgroundColor: '#ff9f40'
        }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false
      }
      });
    }

    /**
     * 💰 نمودار درآمد ماهانه
     */
    function renderIncomeChart(data) {
      let ctx = document.getElementById('doctor-income-chart').getContext('2d');
      if (window.incomeChart) window.incomeChart.destroy();

      let labels = data.map(item => item.month);
      let paid = data.map(item => item.total_paid_income);
      let unpaid = data.map(item => item.total_unpaid_income);

      window.incomeChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
        label: 'پرداخت‌شده',
        data: paid,
        backgroundColor: '#4caf50'
        },
        {
        label: 'پرداخت‌نشده',
        data: unpaid,
        backgroundColor: '#f44336'
        }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false
      }
      });
    }

    /**
     * 👨‍⚕️ نمودار تعداد بیماران جدید
     */
    function renderPatientChart(data) {
      let ctx = document.getElementById('doctor-patient-chart').getContext('2d');
      if (window.patientChart) window.patientChart.destroy();

      let labels = data.map(item => item.month);
      let totals = data.map(item => item.total_patients);

      window.patientChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
        label: 'بیماران جدید',
        data: totals,
        backgroundColor: '#ffce56'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false
      }
      });
    }

    /**
     * 📈 نمودار وضعیت نوبت‌ها
     */
    function renderStatusChart(data) {
      let ctx = document.getElementById('doctor-status-chart').getContext('2d');
      if (window.statusChart) window.statusChart.destroy();

      let labels = data.map(item => item.month);
      let scheduled = data.map(item => item.scheduled_count);
      let attended = data.map(item => item.attended_count);
      let missed = data.map(item => item.missed_count);
      let cancelled = data.map(item => item.cancelled_count);

      window.statusChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
        label: 'برنامه‌ریزی‌شده',
        data: scheduled,
        backgroundColor: '#42a5f5'
        },
        {
        label: 'انجام‌شده',
        data: attended,
        backgroundColor: '#66bb6a'
        },
        {
        label: 'غیبت',
        data: missed,
        backgroundColor: '#ef5350'
        },
        {
        label: 'لغو‌شده',
        data: cancelled,
        backgroundColor: '#ffb74d'
        }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false
      }
      });
    }

    /**
     * 🕒 نمودار میانگین مدت زمان نوبت‌ها
     */
    function renderDurationChart(data) {
      let ctx = document.getElementById('doctor-duration-chart').getContext('2d');
      if (window.durationChart) window.durationChart.destroy();

      let labels = data.map(item => item.month);
      let duration = data.map(item => item.average_duration);

      window.durationChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
        label: 'میانگین مدت نوبت',
        data: duration,
        backgroundColor: '#9c27b0'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false
      }
      });
    }

    loadCharts();
    });
  </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('dr.panel.layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/dr/panel/my-performance/chart/index.blade.php ENDPATH**/ ?>