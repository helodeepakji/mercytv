<?php
    $link = $_SERVER['PHP_SELF'];
    $link_array = explode('/',$link);
    $page = end($link_array);
?>

<?php if ($page !== 'layout-horizontal.php' && $page !== 'layout-detached.php' && $page !== 'layout-modern.php' && $page !== 'layout-box.php'&& $page !== 'layout-two-column.php'&& $page !== 'layout-hovered.php'&& $page !== 'layout-horizontal-single.php'&& $page !== 'layout-horizontal-overlay.php'&& $page !== 'layout-horizontal-box.php'&& $page !== 'layout-horizontal-sidemenu.php'
&& $page !== 'layout-without-header.php'&& $page !== 'layout-dark.php'&& $page !== 'reset-password-success-3.php'&& $page !== 'two-step-verification.php'&& $page !== 'two-step-verification-2.php'&& $page !== 'two-step-verification-3.php'&& $page !== 'under-maintenance.php'&& $page !== '404-error.php'&& $page !== '500-error.php'&& $page !== 'blank-page.php'&& $page !== 'coming-soon.php'
&& $page !== 'login.php'&& $page !== 'login-2.php'&& $page !== 'login-3.php'&& $page !== 'register.php'&& $page !== 'register-2.php'&& $page !== 'register-3.php'&& $page !== 'forgot-password.php'&& $page !== 'forgot-password-2.php'&& $page !== 'forgot-password-3.php'&& $page !== 'email-verification.php'&& $page !== 'email-verification-2.php'&& $page !== 'email-verification-3.php'
&& $page !== 'lock-screen.php'&& $page !== 'error-500.php'&& $page !== 'error-404.php'&& $page !== 'success.php'&& $page !== 'success-2.php'&& $page !== 'success-3.php'&& $page !== 'reset-password.php'&& $page !== 'reset-password-2.php'&& $page !== 'reset-password-3.php'&& $page !== 'job-details.php') {   ?>

	<!-- Theme Script js -->
	<script src="assets/js/theme-script.js"></script>
	<?php } ?>
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">

	<!-- Feather CSS -->
	<link rel="stylesheet" href="assets/plugins/icons/feather/feather.css">

	<!-- Tabler Icon CSS -->
	<link rel="stylesheet" href="assets/plugins/tabler-icons/tabler-icons.css">
    <!-- Feathericon CSS -->
    <link rel="stylesheet" href="assets/css/feather.css">

	<!-- Select2 CSS -->
	<link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">

	<!-- Fontawesome CSS -->
	<link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
	<link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">

	 <!-- Color Picker Css -->
	<link rel="stylesheet" href="assets/plugins/flatpickr/flatpickr.min.css">
	<link rel="stylesheet" href="assets/plugins/@simonwep/pickr/themes/nano.min.css">

	<!-- Daterangepikcer CSS -->
	<link rel="stylesheet" href="assets/plugins/daterangepicker/daterangepicker.css">

	<!-- Datatable CSS -->
	<link rel="stylesheet" href="assets/css/dataTables.bootstrap5.min.css">

	<!-- Summernote CSS -->
	<link rel="stylesheet" href="assets/plugins/summernote/summernote-lite.min.css">
	<link rel="stylesheet" href="assets/css/notify.css" rel="stylesheet" />

	<!-- Datetimepicker CSS -->
	<link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">
	<?php  if ($page == 'ui-rangeslider.php') {   ?>
	<!-- Rangeslider CSS -->
	<link rel="stylesheet" href="assets/plugins/ion-rangeslider/css/ion.rangeSlider.min.css">
	<?php } ?>
	<?php  if ($page == 'ui-stickynote.php') {   ?>
	<!-- Sticky CSS -->
	<link rel="stylesheet" href="assets/plugins/stickynote/sticky.css">
	<?php } ?>
	<?php  if ($page == 'chart-c3.php') {   ?>
	<!-- ChartC3 CSS -->
	<link rel="stylesheet" href="assets/plugins/c3-chart/c3.min.css">
	<?php } ?>
	<?php  if ($page == 'ui-scrollbar.php') {   ?>
	<!-- Main CSS -->
	<link rel="stylesheet" href="assets/plugins/scrollbar/scroll.min.css">
	<?php } ?>
	<?php  if ($page == 'chart-morris.php') {   ?>
	<!-- Morris CSS -->
	<link rel="stylesheet" href="assets/plugins/morris/morris.css">
	<?php } ?>
	<?php  if ($page == 'form-wizard.php') {   ?>
	<!-- Wizard CSS -->
	<link rel="stylesheet" href="assets/plugins/twitter-bootstrap-wizard/form-wizard.css">
	<?php } ?>
	<?php  if ($page == 'icon-flag.php') {   ?>
	<!-- Pe7 CSS -->
	<link rel="stylesheet" href="assets/plugins/icons/flags/flags.css">
	<?php } ?>
	<?php  if ($page == 'icon-ionic.php') {   ?>
	<!-- Ionic CSS -->
	<link rel="stylesheet" href="assets/plugins/icons/ionic/ionicons.css">
	<?php } ?>
	<?php  if ($page == 'icon-material.php') {   ?>
	<!-- Material CSS -->
	<link rel="stylesheet" href="assets/plugins/material/materialdesignicons.css">
	<?php } ?>
	<?php  if ($page == 'icon-pe7.php') {   ?>
	<!-- Pe7 CSS -->
	<link rel="stylesheet" href="assets/plugins/icons/pe7/pe-icon-7.css">
	<?php } ?>
	<?php  if ($page == 'icon-simpleline.php') {   ?>
	<link rel="stylesheet" href="assets/plugins/simpleline/simple-line-icons.css">
	<?php } ?>
	<?php  if ($page == 'icon-themify.php') {   ?>
	<link rel="stylesheet" href="assets/plugins/icons/themify/themify.css">
	<?php } ?>
	<?php  if ($page == 'icon-typicon.php') {   ?>
	<link rel="stylesheet" href="assets/plugins/icons/typicons/typicons.css">
	<?php } ?>
	<?php  if ($page == 'icon-weather.php') {   ?>
	<link rel="stylesheet" href="assets/plugins/icons/weather/weathericons.css">
	<?php } ?>
	<!-- Select2 CSS -->
	<link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">

	<!-- Main CSS -->
	<link rel="stylesheet" href="assets/css/style.css">
	<link rel="stylesheet" href="assets/css/notifix.css" rel="stylesheet" />