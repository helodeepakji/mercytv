<?php
    $link = $_SERVER['PHP_SELF'];
    $link_array = explode('/',$link);
    $page = end($link_array);
?>
	<!-- jQuery -->
	<script src="assets/js/jquery-3.7.1.min.js"></script>

	<!-- Bootstrap Core JS -->
	<script src="assets/js/bootstrap.bundle.min.js"></script>

	<!-- Feather Icon JS -->
	<script src="assets/js/feather.min.js"></script>

	<!-- Slimscroll JS -->
	<script src="assets/js/jquery.slimscroll.min.js"></script>

	<!-- Summernote JS -->
	<script src="assets/plugins/summernote/summernote-lite.min.js"></script>

	<!-- Color Picker JS -->
	<script src="assets/plugins/@simonwep/pickr/pickr.es5.min.js"></script>

	<!-- Datatable JS -->
	<script src="assets/js/jquery.dataTables.min.js"></script>
	<script src="assets/js/dataTables.bootstrap5.min.js"></script>	

	<!-- Daterangepikcer JS -->
	<script src="assets/js/moment.js"></script>
	<script src="assets/plugins/daterangepicker/daterangepicker.js"></script>

	<!-- Datetimepicker JS -->
	<script src="assets/js/bootstrap-datetimepicker.min.js"></script>
	<script src="assets/js/notify.js"></script>

	<!-- Select2 JS -->
	<script src="assets/plugins/select2/js/select2.min.js"></script>

	<!-- Chart JS -->
	<script src="assets/plugins/apexchart/apexcharts.min.js"></script>
	<script src="assets/plugins/apexchart/chart-data.js"></script>

	<?php  if ($page == 'ui-counter.php') {   ?>
	<!-- Stickynote JS -->
	<script src="assets/plugins/countup/jquery.counterup.min.js"></script>
	<script src="assets/plugins/countup/jquery.waypoints.min.js"></script>
	<script src="assets/plugins/countup/jquery.missofis-countdown.js"></script>
	<?php } ?>
	<?php  if ($page == 'form-wizard.php') {   ?>
	<!-- Wizard JS -->
	<script src="assets/plugins/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>
	<script src="assets/plugins/twitter-bootstrap-wizard/prettify.js"></script>
	<script src="assets/plugins/twitter-bootstrap-wizard/form-wizard.js"></script>
	<?php } ?>

	<?php  if ($page == 'ui-drag-drop.php') {   ?>
	<!-- Dragula JS -->
	<script src="assets/plugins/dragula/js/dragula.min.js"></script>
	<script src="assets/plugins/dragula/js/drag-drop.min.js"></script>
	<script src="assets/plugins/dragula/js/draggable-cards.js"></script>
	<?php } ?>

	<?php  if ($page == 'ui-rating.php') {   ?>
	<!-- Raty JS -->
	<script src="assets/plugins/raty/jquery.raty.js"></script>
	<script src="assets/plugins/raty/custom.raty.js"></script>
	<?php } ?>
	<?php  if ($page == 'form-validation.php') {   ?>
	<script src="assets/js/form-validation.js"></script>
	<?php } ?>

	<?php  if ($page == 'ui-lightbox.php') {   ?>
	<!-- lightbox JS -->
	<script src="assets/plugins/lightbox/glightbox.min.js"></script>
	<script src="assets/plugins/lightbox/lightbox.js"></script>
	<?php } ?>
	
	<?php  if ($page == 'chart-c3.php') {   ?>
	<!-- Chart JS -->
	<script src="assets/plugins/c3-chart/d3.v5.min.js"></script>
	<script src="assets/plugins/c3-chart/c3.min.js"></script>
	<script src="assets/plugins/c3-chart/chart-data.js"></script>
	<?php } ?>
	<?php  if ($page == 'chart-flot.php') {   ?>
	<!-- Chart JS -->
	<script src="assets/plugins/flot/jquery.flot.js"></script>
	<script src="assets/plugins/flot/jquery.flot.fillbetween.js"></script>
	<script src="assets/plugins/flot/jquery.flot.pie.js"></script>
	<script src="assets/plugins/flot/chart-data.js"></script>
	<?php } ?>
	<?php  if ($page == 'chart-js.php') {   ?>
	<!-- Chart JS -->
	<script src="assets/plugins/chartjs/chart.min.js"></script>
	<script src="assets/plugins/chartjs/chart-data.js"></script>
	<?php } ?>

	<?php  if ($page == 'ui-rangeslider.php') {   ?>
	<!-- Rangeslider JS -->
	<script src="assets/plugins/ion-rangeslider/js/ion.rangeSlider.min.js"></script>
	<script src="assets/plugins/ion-rangeslider/js/custom-rangeslider.js"></script>
	<?php } ?>

	<?php  if ($page == 'ui-scrollbar.php') {   ?>
	<!-- Plyr JS -->
	<script src="assets/plugins/scrollbar/scrollbar.min.js"></script>
	<script src="assets/plugins/scrollbar/custom-scroll.js"></script>
	<?php } ?>

	<?php  if ($page == 'ui-stickynote.php') {   ?>
	<!-- Stickynote JS -->
	<script src="assets/js/jquery-ui.min.js"></script>
	<script src="assets/plugins/stickynote/sticky.js"></script>
	<?php } ?>
	<?php  if ($page == 'chart-morris.php') {   ?>
	<!-- Chart JS -->
	<script src="assets/plugins/morris/raphael-min.js"></script>
	<script src="assets/plugins/morris/morris.min.js"></script>
	<script src="assets/plugins/morris/chart-data.js"></script>
	<?php } ?>
	<?php  if ($page == 'chart-peity.php') {   ?>
	<!-- Chart JS -->
	<script src="assets/plugins/peity/jquery.peity.min.js"></script>
	<script src="assets/plugins/peity/chart-data.js"></script>
	<?php } ?>
	<?php  if ($page == 'form-fileupload.php') {   ?>
	<!-- Fileupload JS -->
	<script src="assets/plugins/fileupload/fileupload.min.js"></script>
	<?php } ?>
	 <!-- Theiastickysidebar JS -->
	 <script src="assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.min.js"></script>
    <script src="assets/plugins/theia-sticky-sidebar/ResizeSensor.min.js"></script>
	<!-- Custom JS -->
	<script src="assets/js/script.js"></script>
	<script src="assets/js/notifix.js"></script>
	<script src="assets/js/theme-colorpicker.js"></script>
	<script>
		var notyf = new Notyf({ position: { x: 'right', y: 'top'} });

		function seenNotification(){
			$.ajax({
				url: 'settings/api/notificationApi.php',
				type: 'POST',
				data: {
					type : 'seenNotification'
				},
				dataType: 'json',
				success: function(response) {
					location.reload();
				},
				error: function(xhr, status, error) {
					var errorMessage = xhr.responseJSON ? xhr.responseJSON.message : "Something went wrong.";
					notyf.error(errorMessage);
				}
			});
		}

	</script>
	
