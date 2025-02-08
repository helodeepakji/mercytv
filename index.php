<?php
include 'layouts/session.php';
?>
<?php include 'layouts/head-main.php'; ?>

<head>
	<title>Form Mask - HRMS admin template</title>
	<?php include 'layouts/title-meta.php'; ?>
	<?php include 'layouts/head-css.php'; ?>
</head>

<body>
	<div id="global-loader">
		<div class="page-loader"></div>
	</div>
	<div class="main-wrapper">
		<?php include 'layouts/menu.php'; ?>
		<!-- Page Wrapper -->
		<div class="page-wrapper">
			<div class="content">

				<!-- Breadcrumb -->
				<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
					<div class="my-auto mb-2">
						<h2 class="mb-1">Dashboard</h2>
						<nav>
							<ol class="breadcrumb mb-0">
								<li class="breadcrumb-item">
									<a href="admin-dashboard.php"><i class="ti ti-smart-home"></i></a>
								</li>
								<li class="breadcrumb-item">
									Dashboard
								</li>
								<li class="breadcrumb-item active" aria-current="page">Dashboard</li>
							</ol>
						</nav>
					</div>
					<div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
						<div class="ms-2 head-icons">
							<a href="javascript:void(0);" class="" data-bs-toggle="tooltip" data-bs-placement="top"
								data-bs-original-title="Collapse" id="collapse-header">
								<i class="ti ti-chevrons-up"></i>
							</a>
						</div>
					</div>
				</div>
				<!-- /Breadcrumb -->

				<!-- Welcome Wrap -->
				<div class="card border-0">
					<div class="card-body d-flex align-items-center justify-content-between flex-wrap pb-1">
						<div class="d-flex align-items-center mb-3">
							<span class="avatar avatar-xl flex-shrink-0">
								<img src="<?php echo $_SESSION['userdetails']['profile'] ?? 'assets/img/profiles/avatar-31.jpg' ?>"
									class="rounded-circle" alt="img">
							</span>
							<div class="ms-3">
								<h3 class="mb-2">Welcome <?php echo $_SESSION['userDetails']['name'] ?> <a
										href="javascript:void(0);" class="edit-icon"><i
											class="ti ti-edit fs-14"></i></a></h3>
							</div>
						</div>
						<div class="d-flex align-items-center flex-wrap mb-1">
							<a href="#"
								class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#change-password"><i class="ti ti-lock me-1"></i>Change Password</a>
						</div>
					</div>
				</div>
				<!-- /Welcome Wrap -->


				<div class="row">

					<!-- Widget Info -->
					<div class="col-xxl-4 d-flex">
						<div class="row flex-fill">
							<div class="col-md-6 d-flex">
								<div class="card flex-fill">
									<div class="card-body">
										<span class="avatar rounded-circle bg-primary mb-2">
											<i class="ti ti-calendar-share fs-16"></i>
										</span>
										<h6 class="fs-13 fw-medium text-default mb-1">Pending Task</h6>
										<h3 class="mb-3"><?php echo $total_pending['total_pending'] ?></h3>
										<a href="task-board.php" class="link-default">View Details</a>
									</div>
								</div>
							</div>
							<div class="col-md-6 d-flex">
								<div class="card flex-fill">
									<div class="card-body">
										<span class="avatar rounded-circle bg-secondary mb-2">
											<i class="ti ti-browser fs-16"></i>
										</span>
										<h6 class="fs-13 fw-medium text-default mb-1">Runing Task</h6>
										<h3 class="mb-3"><?php echo $total_runing['total_runing'] ?></h3>
										<a href="task-board.php" class="link-default">View All</a>
									</div>
								</div>
							</div>
							<div class="col-md-6 d-flex">
								<div class="card flex-fill">
									<div class="card-body">
										<span class="avatar rounded-circle bg-info mb-2">
											<i class="ti ti-users-group fs-16"></i>
										</span>
										<h6 class="fs-13 fw-medium text-default mb-1">Total No of Clients</h6>
										<h3 class="mb-3">69/86 <span class="fs-12 fw-medium text-danger"><i
													class="fa-solid fa-caret-down me-1"></i>-11.2%</span></h3>
										<a href="clients.php" class="link-default">View All</a>
									</div>
								</div>
							</div>
							<div class="col-md-6 d-flex">
								<div class="card flex-fill">
									<div class="card-body">
										<span class="avatar rounded-circle bg-pink mb-2">
											<i class="ti ti-checklist fs-16"></i>
										</span>
										<h6 class="fs-13 fw-medium text-default mb-1">Total No of Tasks</h6>
										<h3 class="mb-3">225/28 <span class="fs-12 fw-medium text-success"><i
													class="fa-solid fa-caret-down me-1"></i>+11.2%</span></h3>
										<a href="tasks.php" class="link-default">View All</a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- /Widget Info -->


					<!-- Attendance Overview -->
					<div class="col-xxl-4 col-xl-6 d-flex">
						<div class="card flex-fill">
							<div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
								<h5 class="mb-2">Attendance Overview</h5>
								<div class="dropdown mb-2">
									<a href="javascript:void(0);"
										class="btn btn-white border btn-sm d-inline-flex align-items-center"
										data-bs-toggle="dropdown">
										<i class="ti ti-calendar me-1"></i>Today
									</a>
									<ul class="dropdown-menu  dropdown-menu-end p-3">
										<li>
											<a href="javascript:void(0);" class="dropdown-item rounded-1">This Month</a>
										</li>
										<li>
											<a href="javascript:void(0);" class="dropdown-item rounded-1">This Week</a>
										</li>
										<li>
											<a href="javascript:void(0);" class="dropdown-item rounded-1">Today</a>
										</li>
									</ul>
								</div>
							</div>
							<div class="card-body">
								<div class="chartjs-wrapper-demo position-relative mb-4">
									<canvas id="attendance" height="200"></canvas>
									<div class="position-absolute text-center attendance-canvas">
										<p class="fs-13 mb-1">Total Attendance</p>
										<h3>120</h3>
									</div>
								</div>
								<h6 class="mb-3">Status</h6>
								<div class="d-flex align-items-center justify-content-between">
									<p class="f-13 mb-2"><i class="ti ti-circle-filled text-warning me-1"></i>Permission
									</p>
									<p class="f-13 fw-medium text-gray-9 mb-2">2%</p>
								</div>
								<div class="d-flex align-items-center justify-content-between mb-2">
									<p class="f-13 mb-2"><i class="ti ti-circle-filled text-danger me-1"></i>Absent</p>
									<p class="f-13 fw-medium text-gray-9 mb-2">15%</p>
								</div>
							</div>
						</div>
					</div>
					<!-- /Attendance Overview -->

					<!-- Todo -->
					<div class="col-xxl-4 col-xl-6 d-flex">
						<div class="card flex-fill">
							<div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
								<h5 class="mb-2">Todo</h5>
								<div class="d-flex align-items-center">
									<div class="dropdown mb-2 me-2">
										<a href="javascript:void(0);"
											class="btn btn-white border btn-sm d-inline-flex align-items-center"
											data-bs-toggle="dropdown">
											<i class="ti ti-calendar me-1"></i>Today
										</a>
										<ul class="dropdown-menu  dropdown-menu-end p-3">
											<li>
												<a href="javascript:void(0);" class="dropdown-item rounded-1">This
													Month</a>
											</li>
											<li>
												<a href="javascript:void(0);" class="dropdown-item rounded-1">This
													Week</a>
											</li>
											<li>
												<a href="javascript:void(0);" class="dropdown-item rounded-1">Today</a>
											</li>
										</ul>
									</div>
									<a href="#"
										class="btn btn-primary btn-icon btn-xs rounded-circle d-flex align-items-center justify-content-center p-0 mb-2"
										data-bs-toggle="modal" data-bs-target="#add_todo"><i
											class="ti ti-plus fs-16"></i></a>
								</div>
							</div>
							<div class="card-body">
								<div class="d-flex align-items-center todo-item border p-2 br-5 mb-2">
									<i class="ti ti-grid-dots me-2"></i>
									<div class="form-check">
										<input class="form-check-input" type="checkbox" id="todo1">
										<label class="form-check-label fw-medium" for="todo1">Add Holidays</label>
									</div>
								</div>
								<div class="d-flex align-items-center todo-item border p-2 br-5 mb-2">
									<i class="ti ti-grid-dots me-2"></i>
									<div class="form-check">
										<input class="form-check-input" type="checkbox" id="todo2">
										<label class="form-check-label fw-medium" for="todo2">Add Meeting to
											Client</label>
									</div>
								</div>
								<div class="d-flex align-items-center todo-item border p-2 br-5 mb-2">
									<i class="ti ti-grid-dots me-2"></i>
									<div class="form-check">
										<input class="form-check-input" type="checkbox" id="todo3">
										<label class="form-check-label fw-medium" for="todo3">Chat with Adrian</label>
									</div>
								</div>
								<div class="d-flex align-items-center todo-item border p-2 br-5 mb-2">
									<i class="ti ti-grid-dots me-2"></i>
									<div class="form-check">
										<input class="form-check-input" type="checkbox" id="todo4">
										<label class="form-check-label fw-medium" for="todo4">Management Call</label>
									</div>
								</div>
								<div class="d-flex align-items-center todo-item border p-2 br-5 mb-2">
									<i class="ti ti-grid-dots me-2"></i>
									<div class="form-check">
										<input class="form-check-input" type="checkbox" id="todo5">
										<label class="form-check-label fw-medium" for="todo5">Add Payroll</label>
									</div>
								</div>
								<div class="d-flex align-items-center todo-item border p-2 br-5 mb-0">
									<i class="ti ti-grid-dots me-2"></i>
									<div class="form-check">
										<input class="form-check-input" type="checkbox" id="todo6">
										<label class="form-check-label fw-medium" for="todo6">Add Policy for Increment
										</label>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- /Todo -->

				</div>

			</div>

			<div class="footer d-sm-flex align-items-center justify-content-between border-top bg-white p-3">
				<p class="mb-0"><?php echo date('Y') ?> &copy; MercyTV</p>
				<p>Designed &amp; Developed By <a href="javascript:void(0);" class="text-primary">SoftAir Technology</a></p>
			</div>

		</div>
		<!-- /Page Wrapper -->

		<!-- Add Todo -->
		<div class="modal fade" id="add_todo">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">Add New Todo</h4>
						<button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
							aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<form action="admin-dashboard.php">
						<div class="modal-body">
							<div class="row">
								<div class="col-12">
									<div class="mb-3">
										<label class="form-label">Todo Title</label>
										<input type="text" class="form-control">
									</div>
								</div>
								<div class="col-6">
									<div class="mb-3">
										<label class="form-label">Tag</label>
										<select class="select">
											<option>Select</option>
											<option>Internal</option>
											<option>Projects</option>
											<option>Meetings</option>
											<option>Reminder</option>
										</select>
									</div>
								</div>
								<div class="col-6">
									<div class="mb-3">
										<label class="form-label">Priority</label>
										<select class="select">
											<option>Select</option>
											<option>Medium</option>
											<option>High</option>
											<option>Low</option>
										</select>
									</div>
								</div>
								<div class="col-lg-12">
									<div class="mb-3">
										<label class="form-label">Descriptions</label>
										<div class="summernote"></div>
									</div>
								</div>
								<div class="col-12">
									<div class="mb-3">
										<label class="form-label">Add Assignee</label>
										<select class="select">
											<option>Select</option>
											<option>Sophie</option>
											<option>Cameron</option>
											<option>Doris</option>
											<option>Rufana</option>
										</select>
									</div>
								</div>
								<div class="col-12">
									<div class="mb-0">
										<label class="form-label">Status</label>
										<select class="select">
											<option>Select</option>
											<option>Completed</option>
											<option>Pending</option>
											<option>Onhold</option>
											<option>Inprogress</option>
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
							<button type="submit" class="btn btn-primary">Add New Todo</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- /Add Todo -->


	</div>

	<!-- Change Password -->
	<div class="modal fade custom-modal" id="change-password">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content doctor-profile">
				<div class="modal-header d-flex align-items-center justify-content-between border-bottom">
					<h5 class="modal-title">Change Password</h5>
					<a href="javascript:void(0);" data-bs-dismiss="modal" aria-label="Close"><i
							class="ti ti-circle-x-filled fs-20"></i></a>
				</div>
				<div class="modal-body p-4">
					<form id="changePassword">
						<div class="mb-3">
							<label class="form-label">New Password<span class="text-danger">*</span></label>
							<div class="pass-group">
								<input type="password" class="pass-inputs form-control" name="password" required>
								<span class="ti toggle-passwords ti-eye-off"></span>
								<input type="hidden" name="type" value="changePassword">
							</div>
						</div>
						<div class="mb-3">
							<label class="form-label">Confirm Password<span class="text-danger">*</span></label>
							<div class="pass-group">
								<input type="password" class="form-control pass-inputa" name="cpassword" required>
								<span class="ti toggle-passworda ti-eye-off"></span>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer border-top">
					<div class="acc-submit">
						<a href="javascript:void(0);" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
						<button class="btn btn-primary" type="submit">Save</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- /Change Password -->

	<!-- end main wrapper-->
	<!-- JAVASCRIPT -->
	<?php include 'layouts/vendor-scripts.php'; ?>
	<script src="assets/js/todo.js"></script>
	<!-- Bootstrap Tagsinput JS -->
	<script src="assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js"></script>
	<!-- Chart JS -->
	<script src="assets/plugins/chartjs/chart.min.js"></script>
	<script src="assets/plugins/chartjs/chart-data.js"></script>
	<script>
		$('#changePassword').submit(function(event) {
			event.preventDefault();
			var formData = new FormData(this);
			$.ajax({
				url: 'settings/api/userApi.php',
				type: 'POST',
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				dataType: 'json',
				success: function(response) {
					notyf.success(response.message);
					setTimeout(() => {
						location.reload();
					}, 1000);
				},
				error: function(xhr, status, error) {
					var errorMessage = xhr.responseJSON ? xhr.responseJSON.message : "Something went wrong.";
					notyf.error(errorMessage);
				}
			});
		});
	</script>
</body>

</html>