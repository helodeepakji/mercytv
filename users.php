<?php
include 'layouts/session.php';


$block = $conn->prepare("SELECT `users`.* FROM `users` WHERE `is_active` = 0 ORDER BY `users`.`name` ASC");
$block->execute();
$block = $block->fetchAll(PDO::FETCH_ASSOC);

$sql = $conn->prepare("SELECT `users`.* FROM `users` WHERE `is_active` = 1 ORDER BY `users`.`name` ASC");
$sql->execute();
$users = $sql->fetchAll(PDO::FETCH_ASSOC);

$no_verify = $conn->prepare("SELECT `users`.* FROM `users` WHERE `is_verify_email` = 0 AND `is_verify_number` = 0 ORDER BY `users`.`name` ASC");
$no_verify->execute();
$no_verify = $no_verify->fetchAll(PDO::FETCH_ASSOC);

if (!is_array($no_verify)) {
	$no_verify = [];
}

if (!is_array($users)) {
	$users = [];
}

if (!is_array($block)) {
	$block = [];
}

?>
<?php include 'layouts/head-main.php'; ?>

<head>
	<title>Mercy TV Users List</title>
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
						<h2 class="mb-1">Users</h2>
						<nav>
							<ol class="breadcrumb mb-0">
								<li class="breadcrumb-item">
									<a href="admin-dashboard.php"><i class="ti ti-smart-home"></i></a>
								</li>
								<li class="breadcrumb-item">
									Users
								</li>
								<li class="breadcrumb-item active" aria-current="page">Users List</li>
							</ol>
						</nav>
					</div>
					<div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
						<div class="me-2 mb-2">
							<div class="d-flex align-items-center border bg-white rounded p-1 me-2 icon-list">
								<a href="users.php" class="btn btn-icon btn-sm active bg-primary text-white me-1"><i
										class="ti ti-list-tree"></i></a>
							</div>
						</div>
						<div class="mb-2">
							<a href="#" data-bs-toggle="modal" data-bs-target="#add_employee"
								class="btn btn-primary d-flex align-items-center"><i
									class="ti ti-circle-plus me-2"></i>Add Users</a>
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

				<div class="row">

					<!-- Total Plans -->
					<div class="col-lg-3 col-md-6 d-flex">
						<div class="card flex-fill">
							<div class="card-body d-flex align-items-center justify-content-between">
								<div class="d-flex align-items-center overflow-hidden">
									<div>
										<span class="avatar avatar-lg bg-dark rounded-circle"><i
												class="ti ti-users"></i></span>
									</div>
									<div class="ms-2 overflow-hidden">
										<p class="fs-12 fw-medium mb-1 text-truncate">Total Users</p>
										<h4><?php echo count($block) + count($users) ?></h4>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- /Total Plans -->

					<!-- Total Plans -->
					<div class="col-lg-3 col-md-6 d-flex">
						<div class="card flex-fill">
							<div class="card-body d-flex align-items-center justify-content-between">
								<div class="d-flex align-items-center overflow-hidden">
									<div>
										<span class="avatar avatar-lg bg-success rounded-circle"><i
												class="ti ti-user-share"></i></span>
									</div>
									<div class="ms-2 overflow-hidden">
										<p class="fs-12 fw-medium mb-1 text-truncate">Active Users</p>
										<h4><?php echo count($users) ?></h4>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- /Total Plans -->

					<!-- Inactive Plans -->
					<div class="col-lg-3 col-md-6 d-flex">
						<div class="card flex-fill">
							<div class="card-body d-flex align-items-center justify-content-between">
								<div class="d-flex align-items-center overflow-hidden">
									<div>
										<span class="avatar avatar-lg bg-danger rounded-circle"><i
												class="ti ti-user-pause"></i></span>
									</div>
									<div class="ms-2 overflow-hidden">
										<p class="fs-12 fw-medium mb-1 text-truncate">Inactive Users</p>
										<h4><?php echo count($block) ?></h4>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- /Inactive Companies -->

					<!-- No of Plans  -->
					<div class="col-lg-3 col-md-6 d-flex">
						<div class="card flex-fill">
							<div class="card-body d-flex align-items-center justify-content-between">
								<div class="d-flex align-items-center overflow-hidden">
									<div>
										<span class="avatar avatar-lg bg-info rounded-circle"><i
												class="ti ti-user-plus"></i></span>
									</div>
									<div class="ms-2 overflow-hidden">
										<p class="fs-12 fw-medium mb-1 text-truncate">Not Verified Users</p>
										<h4><?php echo count($no_verify) ?></h4>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- /No of Plans -->

				</div>

				<div class="card">
					<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
						<h5>Users List</h5>
						<div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
							<div class="me-3">
								<select id="selectedStatus" class="form-select">
									<option value="">Select Status</option>
									<option value="1">Active</option>
									<option value="0">Block</option>
								</select>
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
										<th>Full Name</th>
										<th>Gender</th>
										<th>Email</th>
										<th>Phone</th>
										<th>Created Date</th>
										<th>Status</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<?php
									$i = 0;
									foreach ($users as $user) { ?>
										<tr>
											<td>
												<div class="form-check form-check-md">
													<input class="form-check-input" type="checkbox">
												</div>
											</td>
											<td><a
													href="employee-details.php?id=<?php echo base64_encode($user['id']) ?>"><?php echo ++$i ?></a>
											</td>
											<td>
												<div class="d-flex align-items-center">
													<a class="avatar avatar-md" data-bs-toggle="modal"
														data-bs-target="#view_details"><img
															src="assets/img/users/user-32.jpg"
															class="img-fluid rounded-circle" alt="img"></a>
													<div class="ms-2">
														<p class="text-dark mb-0"><a data-bs-toggle="modal"
																data-bs-target="#view_details"><?php echo $user['name'] ?></a>
														</p>
													</div>
												</div>
											</td>
											<td><?php echo ucfirst($user['gender']) ?></td>
											<td><?php echo $user['email'] ?> <?php if($user['is_verify_email']){ ?> <i class="ti ti-check text-success"></i> <?php } ?> </td>
											<td><?php echo $user['phone'] ?> <?php if($user['is_verify_number']){ ?>  <i class="ti ti-check text-success"></i> <?php } ?></td>
											<td><?php echo date('d M, Y h:i A', strtotime($user['created_at'])) ?></td>
											<td>
												<span
													class="badge badge-<?php echo $user['is_active'] == 0 ? 'danger' : 'success' ?> d-inline-flex align-items-center badge-xs">
													<i class="ti ti-point-filled me-1"></i><?php echo $user['is_active'] == 0 ? 'Inactive' : 'Active' ?>
												</span>
											</td>
											<td>
												<div class="action-icon d-inline-flex">
													<a href="#" class="me-2" data-bs-toggle="modal"
														data-bs-target="#edit_employee"
														onclick="getEmployee(<?php echo $user['id'] ?>)"><i
															class="ti ti-edit"></i></a>
													<a href="#" data-bs-toggle="modal" data-bs-target="#delete_modal"
														onclick="deleteEmployee(<?php echo $user['id'] ?>)"><i
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
							<h4 class="modal-title me-2">Add New User</h4>
						</div>
						<button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
							aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<form id="addEmployee">
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
										<div class="col-md-6">
											<div class="mb-3">
												<label class="form-label">Name <span class="text-danger">
														*</span></label>
												<input type="text" class="form-control" name="name" required>
												<input type="hidden" name="type" value="addEmployee">
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
												<label class="form-label">Email <span class="text-danger">
														*</span></label>
												<input type="email" class="form-control" name="email" required>
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
												<label class="form-label">Phone <span class="text-danger">
														*</span></label>
												<input type="number" class="form-control" name="phone" required>
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
												<label class="form-label">Gender</label>
												<select class="select" name="gender" required>
													<option>Select</option>
													<option value="male">Male</option>
													<option value="female">Female</option>
													<option value="other">Other</option>
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
												<label class="form-label">Password <span class="text-danger">
														*</span></label>
												<input type="password" class="form-control" name="password" required>
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
												<label class="form-label">Confirm Password <span class="text-danger">
														*</span></label>
												<input type="password" class="form-control" name="cpassword" required>
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
							<h4 class="modal-title me-2">Edit User</h4>
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
							<form id="editEmployee">
								<div class="modal-body pb-0 ">
									<div class="row">
										<div class="col-md-6">
											<div class="mb-3">
												<label class="form-label">Name <span class="text-danger">
														*</span></label>
												<input type="text" class="form-control" name="name" id="name" required>
												<input type="hidden" name="type" value="editEmployee">
												<input type="hidden" name="id" value="" id="id">
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
												<label class="form-label">Email <span class="text-danger">
														*</span></label>
												<input type="email" class="form-control" name="email" id="email"
													required>
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
												<label class="form-label">Phone <span class="text-danger">
														*</span></label>
												<input type="number" class="form-control" name="phone" id="phone"
													required>
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
												<label class="form-label">Gender</label>
												<select class="form-control" name="gender" id="gender" required>
													<option>Select</option>
													<option value="male">Male</option>
													<option value="female">Female</option>
													<option value="other">Other</option>
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
												<label class="form-label">Status</label>
												<select class="form-control" name="status" id="status" required>
													<option>Select</option>
													<option value="1">Active</option>
													<option value="0">Inactive</option>
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
												<label class="form-label">Password <span class="text-danger">
														*</span></label>
												<input type="password" class="form-control" name="password" id="password" required>
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
												<label class="form-label">Confirm Password <span class="text-danger">
														*</span></label>
												<input type="password" class="form-control" name="cpassword" id="cpassword" required>
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
		function deleteEmployee(id) {
			$('#employeeDelete').data('id', id);
		}

		$('#employeeDelete').click(() => {
			var id = $('#employeeDelete').data('id');
			$.ajax({
				url: 'settings/api/userApi.php',
				type: 'GET',
				data: {
					type: 'deleteEmployee',
					user_id: id
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


		$('#selectedStatus').on('click', function() {
			fetchFilteredData();
		});

		function fetchFilteredData() {
			const status = $('#selectedStatus').val();

			$.ajax({
				url: 'settings/api/userApi.php',
				method: 'POST',
				data: {
					status: status,
					type: 'FilterEmployee',
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

		$('#addEmployee').submit(function() {
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
					location.reload();
				},
				error: function(xhr, status, error) {
					var errorMessage = xhr.responseJSON ? xhr.responseJSON.message : "Something went wrong.";
					notyf.error(errorMessage);
				}
			});
		});

		$('#editEmployee').submit(function() {
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
					location.reload();
				},
				error: function(xhr, status, error) {
					var errorMessage = xhr.responseJSON ? xhr.responseJSON.message : "Something went wrong.";
					notyf.error(errorMessage);
				}
			});
		});


		function formatDate(dateString) {
			if (!dateString) return '';
			const date = new Date(dateString);
			const day = String(date.getDate()).padStart(2, '0');
			const month = String(date.getMonth() + 1).padStart(2, '0');
			const year = date.getFullYear();
			return `${day}/${month}/${year}`;
		}


		function getEmployee(id) {
			$.ajax({
				url: 'settings/api/userApi.php',
				type: 'GET',
				data: {
					type: 'getEmployee',
					id: id
				},
				dataType: 'json',
				success: function(response) {
					$('#id').val(response.id);
					$('#name').val(response.name);
					$('#email').val(response.email);
					$('#password').val(response.password);
					$('#cpassword').val(response.password);
					$('#status').val(response.is_active || 'Select').trigger('change');
					$('#phone').val(response.phone);
					$('#gender').val(response.gender || 'Select').trigger('change');
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