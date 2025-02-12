<?php
include 'layouts/session.php';

$program = $conn->prepare("SELECT * FROM `program` WHERE `date` >= CURDATE()");
$program->execute();
$program = $program->fetchAll(PDO::FETCH_ASSOC);

?>
<?php include 'layouts/head-main.php'; ?>

<head>
	<title>Mercy TV Program List</title>
	<?php include 'layouts/title-meta.php'; ?>
	<?php include 'layouts/head-css.php'; ?>
</head>

<body>
	<div id="global-loader" style="display: none;">
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
						<h2 class="mb-1">Program</h2>
						<nav>
							<ol class="breadcrumb mb-0">
								<li class="breadcrumb-item">
									<a href="admin-dashboard.php"><i class="ti ti-smart-home"></i></a>
								</li>
								<li class="breadcrumb-item">
                                	Admin Controller
								</li>
								<li class="breadcrumb-item active" aria-current="page">Program List</li>
							</ol>
						</nav>
					</div>
					<div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
						<div class="mb-2">
							<a href="#" data-bs-toggle="modal" data-bs-target="#add_employee"
								class="btn btn-primary d-flex align-items-center"><i
									class="ti ti-circle-plus me-2"></i>Add EPG</a>
						</div>
						<div class="head-icons ms-2">
							<a href="javascript:void(0);" class="" data-bs-toggle="tooltip" data-bs-placement="top"
								data-bs-original-title="Collapse" id="collapse-header">
								<i class="ti ti-chevrons-up"></i>
							</a>
						</div>
					</div>
				</div>
				<!-- /Breadcrumb -->

				<div class="card">
					<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
						<h5>Program List</h5>
						<div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
							<div class="me-3">
								<div class="input-icon-end position-relative">
									<input type="text" class="form-control date-range bookingrange" placeholder="dd/mm/yyyy - dd/mm/yyyy" name="date" id="dateRange">
									<span class="input-icon-addon">
										<i class="ti ti-chevron-down"></i>
									</span>
								</div>
							</div>
						</div>
					</div>
					<div class="card-body p-0">
						<div class="custom-datatable-filter table-responsive">
							<table class="table datatable">
								<thead class="thead-light">
									<tr>
										<th class="no-sort">
											<div class="form-check form-check-md">
												<input class="form-check-input" type="checkbox" id="select-all">
											</div>
										</th>
										<th>Sno</th>
										<th>Program</th>
                                        <th>Description</th>
										<th>Date</th>
										<th>Time</th>
										<th>Duration (min)</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<?php
									$i = 0;
									foreach ($program as $value) { 
										$test = 'assets/img/users/user-32.jpg';
										$image = $value['image'];
										?>
										<tr>
											<td>
												<div class="form-check form-check-md">
													<input class="form-check-input" type="checkbox">
												</div>
											</td>
											<td><a><?php echo ++$i ?></a>
											</td>
											<td>
												<div class="d-flex align-items-center">
													<a class="avatar avatar-md" data-bs-toggle="modal"
														data-bs-target="#view_details"><img
															src="<?php echo $image ?? $test ?>"
															class="img-fluid rounded-circle" alt="img"></a>
													<div class="ms-2">
														<p class="text-dark mb-0"><a data-bs-toggle="modal"
																data-bs-target="#view_details"><?php echo $value['program'] ?></a>
														</p>
													</div>
												</div>
											</td>
											<td><?php echo ucfirst($value['desc']) ?></td>
											<td><?php echo date('h:i A', strtotime($value['time'])) ?></td>
											<td><?php echo date('d M, Y', strtotime($value['date'])) ?></td>
											<td><?php echo $value['duration'] ?></td>
											<td>
												<div class="action-icon d-inline-flex">
													<a href="#" class="me-2" data-bs-toggle="modal"
														data-bs-target="#edit_employee"
														onclick="getProgram(<?php echo $value['id'] ?>)"><i
															class="ti ti-edit"></i></a>
													<a href="#" data-bs-toggle="modal" data-bs-target="#delete_modal"
														onclick="deleteProgram(<?php echo $value['id'] ?>)"><i
															class="ti ti-trash"></i></a>
												</div>
											</td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>

		</div>
		<!-- /Page Wrapper -->

		<!-- Add Employee -->
		<div class="modal fade" id="add_employee">
			<div class="modal-dialog modal-dialog-centered modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<div class="d-flex align-items-center">
							<h4 class="modal-title me-2">Add Weekly Program List</h4>
						</div>
						<button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
							aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<form id="addExcel">
						<div class="contact-grids-tab">
							<ul class="nav nav-underline" id="myTab" role="tablist">
								<li class="nav-item" role="presentation">
									<button class="nav-link active" id="info-tab" data-bs-toggle="tab"
										data-bs-target="#basic-info" type="button" role="tab" aria-selected="true">Basic
										Information</button>
								</li>
							</ul>
						</div>
						<div class="tab-content" id="myTabContent">
							<div class="tab-pane fade show active" id="basic-info" role="tabpanel"
								aria-labelledby="info-tab" tabindex="0">
								<div class="modal-body pb-0 ">
									<div class="row">
										<div class="col-md-12">
											<div class="mb-3">
												<label class="form-label">Excel File <span class="text-danger">
														*</span></label>
												<input type="file" class="form-control" name="excelFile" required>
											</div>
										</div>
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-outline-light border me-2"
										data-bs-dismiss="modal">Cancel</button>
									<button type="submit" class="btn btn-primary">Save </button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- /Add Employee -->

		<!-- Edit Employee -->
		<div class="modal fade" id="edit_employee">
			<div class="modal-dialog modal-dialog-centered modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<div class="d-flex align-items-center">
							<h4 class="modal-title me-2">Edit Program</h4>
						</div>
						<button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
							aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<div class="contact-grids-tab">
						<ul class="nav nav-underline" id="myTab2" role="tablist">
							<li class="nav-item" role="presentation">
								<button class="nav-link active" id="info-tab2" data-bs-toggle="tab"
									data-bs-target="#basic-info2" type="button" role="tab" aria-selected="true">Basic
									Information</button>
							</li>
						</ul>
					</div>
					<div class="tab-content" id="myTabContent2">
						<div class="tab-pane fade show active" id="basic-info2" role="tabpanel"
							aria-labelledby="info-tab2" tabindex="0">
							<form id="editProgram">
								<div class="modal-body pb-0 ">
									<div class="row">
										<div class="col-md-6">
											<div class="mb-3">
												<label class="form-label">Program Name <span class="text-danger">
														*</span></label>
												<input type="text" class="form-control" name="program" id="program" required>
												<input type="hidden" name="type" value="editProgram">
												<input type="hidden" name="id" value="" id="id">
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
												<label class="form-label">Date <span class="text-danger">
														*</span></label>
												<input type="date" class="form-control" name="date" id="date"
													required>
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
												<label class="form-label">Time <span class="text-danger">
														*</span></label>
												<input type="time" class="form-control" name="time" id="time"
													required>
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
												<label class="form-label">Duration <span class="text-danger">
														*</span></label>
												<input type="number" class="form-control" name="duration" id="duration"
													required>
											</div>
										</div>
										<div class="col-md-12">
											<div class="mb-3">
												<label class="form-label">Image <span class="text-danger">
														*</span></label>
												<input type="file" class="form-control" accept="*/image" name="image" id="image">
											</div>
										</div>
										<div class="col-md-12">
											<div class="mb-3">
												<label class="form-label">Description <span class="text-danger">
														*</span></label>
												<textarea name="desc" class="form-control" id="desc"></textarea>
											</div>
										</div>
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-outline-light border me-2"
										data-bs-dismiss="modal">Cancel</button>
									<button type="submit" class="btn btn-primary">Save </button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Edit Employee -->


		<!-- Delete Modal -->
		<div class="modal fade" id="delete_modal">
			<div class="modal-dialog modal-dialog-centered modal-sm">
				<div class="modal-content">
					<div class="modal-body text-center">
						<span class="avatar avatar-xl bg-transparent-danger text-danger mb-3">
							<i class="ti ti-trash-x fs-36"></i>
						</span>
						<h4 class="mb-1">Confirm Delete</h4>
						<p class="mb-3">You want to delete all the marked items, this cant be undone once you delete.
						</p>
						<div class="d-flex justify-content-center">
							<a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
							<a id="employeeDelete" class="btn btn-danger">Yes, Delete</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Delete Modal -->

	</div>
	<!-- end main wrapper-->
	<!-- JAVASCRIPT -->
	<?php include 'layouts/vendor-scripts.php'; ?>
	<script>
		function deleteProgram(id) {
			$('#employeeDelete').data('id', id);
		}

		$('#employeeDelete').click(() => {
			var id = $('#employeeDelete').data('id');
			$.ajax({
				url: 'settings/api/programApi.php',
				type: 'GET',
				data: {
					type: 'deleteProgram',
					id: id
				},
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


		$('#dateRange').on('change', function() {
			fetchFilteredData();
		});

		function fetchFilteredData() {
			const dateRange = $('#dateRange').val();

			$.ajax({
				url: 'settings/api/programApi.php',
				method: 'POST',
				data: {
					dateRange: dateRange,
					type: 'filterProgram',
				},
				success: function(response) {
					$('.datatable').DataTable().destroy();
					$('tbody').html(response);
					$('.datatable').DataTable();
				},
				error: function() {
					alert('Error fetching data.');
				},
			});
		}
	</script>
	<script>

		$('#addExcel').submit(function() {
			event.preventDefault();
			var formData = new FormData(this);
			$.ajax({
				url: 'settings/api/weeklyEpgApi.php',
				type: 'POST',
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				dataType: 'json',
				success: function(response) {
					location.reload();
				},
				error: function(xhr, status, error) {
					var errorMessage = xhr.responseJSON ? xhr.responseJSON.message : "Something went wrong.";
					notyf.error(errorMessage);
				}
			});
		});

		$('#editProgram').submit(function() {
			event.preventDefault();
			var formData = new FormData(this);
			$.ajax({
				url: 'settings/api/programApi.php',
				type: 'POST',
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				dataType: 'json',
				success: function(response) {
					location.reload();
				},
				error: function(xhr, status, error) {
					var errorMessage = xhr.responseJSON ? xhr.responseJSON.message : "Something went wrong.";
					notyf.error(errorMessage);
				}
			});
		});


		function getProgram(id) {
			$.ajax({
				url: 'settings/api/programApi.php',
				type: 'GET',
				data: {
					type: 'getProgram',
					id: id
				},
				dataType: 'json',
				success: function(response) {
					$('#id').val(response.id);
					$('#program').val(response.program);
					$('#duration').val(response.duration);
					$('#time').val(response.time);
					$('#date').val(response.date);
					$('#desc').text(response.desc);
				},
				error: function(xhr, status, error) {
					var errorMessage = xhr.responseJSON ? xhr.responseJSON.message : "Something went wrong.";
					notyf.error(errorMessage);
				}
			});
		}
	</script>
</body>

</html>