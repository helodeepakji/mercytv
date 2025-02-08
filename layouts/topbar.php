<!-- Header -->
<div class="header">
	<div class="main-header">

		<div class="header-left">
			<a href="admin-dashboard.php" class="logo">
				<img src="assets/img/LOGO.png" alt="Logo">
			</a>
			<a href="admin-dashboard.php" class="dark-logo">
				<img src="assets/img/logo-white.svg" alt="Logo">
			</a>
		</div>

		<a id="mobile_btn" class="mobile_btn" href="#sidebar">
			<span class="bar-icon">
				<span></span>
				<span></span>
				<span></span>
			</span>
		</a>

		<div class="header-user">
			<div class="nav user-menu nav-list">

				<div class="me-auto d-flex align-items-center" id="header-search">
					<a id="toggle_btn" href="javascript:void(0);" class="btn btn-menubar me-1">
						<i class="ti ti-arrow-bar-to-left"></i>
					</a>
				</div>

				<div class="d-flex align-items-center">
					<div class="me-1">
						<a href="#" class="btn btn-menubar btnFullscreen">
							<i class="ti ti-maximize"></i>
						</a>
					</div>
					<div class="me-1 notification_item">
						<a href="#" class="btn btn-menubar position-relative me-1" id="notification_popup"
							data-bs-toggle="dropdown">
							<i class="ti ti-bell"></i>
							<span class="notification-status-dot"></span>
						</a>
						<div class="dropdown-menu dropdown-menu-end notification-dropdown p-4">
							<div class="d-flex align-items-center justify-content-between border-bottom p-0 pb-3 mb-3">
								<h4 class="notification-title">Notifications </h4>
								<div class="d-flex align-items-center">
								</div>
							</div>
							<div class="noti-content">
								<div class="d-flex flex-column">
								</div>
							</div>
							<div class="d-flex p-0">
								<a href="#" class="btn btn-light w-100 me-2">Cancel</a>
								<a href="activity.php" class="btn btn-primary w-100">View All</a>
							</div>
						</div>
					</div>
					<div class="dropdown profile-dropdown">
						<a href="javascript:void(0);" class="dropdown-toggle d-flex align-items-center"
							data-bs-toggle="dropdown">
							<span class="avatar avatar-sm online">
								<img src="assets/img/profiles/avatar-12.jpg" alt="Img" class="img-fluid rounded-circle">
							</span>
						</a>
						<div class="dropdown-menu shadow-none">
							<div class="card mb-0">
								<div class="card-header">
									<div class="d-flex align-items-center">
										<span class="avatar avatar-lg me-2 avatar-rounded">
											<img src="<?php echo $_SESSION['userDetails']['profile'] ?? 'assets/img/profiles/avatar-12.jpg' ?>" alt="img">
										</span>
										<div>
											<h5 class="mb-0"><?php echo $_SESSION['userDetails']['name'] ?></h5>
											<p class="fs-12 fw-medium mb-0"><?php echo $_SESSION['userDetails']['email'] ?></p>
										</div>
									</div>
								</div>
								<div class="card-body">
									<a class="dropdown-item d-inline-flex align-items-center p-0 py-2" href="profile.php">
										<i class="ti ti-user-circle me-1"></i>My Profile
									</a>
									<a class="dropdown-item d-inline-flex align-items-center p-0 py-2" href="bussiness-settings.php">
										<i class="ti ti-settings me-1"></i>Settings
									</a>
								</div>
								<div class="card-footer">
									<a class="dropdown-item d-inline-flex align-items-center p-0 py-2" href="logout.php">
										<i class="ti ti-login me-2"></i>Logout
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Mobile Menu -->
		<div class="dropdown mobile-user-menu">
			<a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
			<div class="dropdown-menu dropdown-menu-end">
				<a class="dropdown-item" href="profile.php">My Profile</a>
				<a class="dropdown-item" href="profile-settings.php">Settings</a>
				<a class="dropdown-item" href="logout.php">Logout</a>
			</div>
		</div>
		<!-- /Mobile Menu -->
	</div>
</div>
<!-- /Header -->