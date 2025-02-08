<?php
$link = $_SERVER['PHP_SELF'];
$link_array = explode('/', $link);
$page = end($link_array);
?>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
	<!-- Logo -->
	<div class="sidebar-logo">
		<a href="index.php" class="logo logo-normal text-center">
			<img src="assets/img/LOGO.png" width="50px" alt="Logo">
		</a>
		<a href="index.php" class="logo-small">
			<img src="assets/img/LOGO.png" alt="Logo">
		</a>
		<a href="index.php" class="dark-logo">
			<img src="assets/img/LOGO.png" alt="Logo">
		</a>
	</div>
	<!-- /Logo -->
	<div class="modern-profile p-3 pb-0">
		<div class="text-center rounded bg-light p-3 mb-4 user-profile">
			<div class="avatar avatar-lg online mb-3">
				<img src="assets/img/profiles/avatar-02.jpg" alt="Img" class="img-fluid rounded-circle">
			</div>
			<h6 class="fs-12 fw-normal mb-1">Adrian Herman</h6>
			<p class="fs-10">System Admin</p>
		</div>
		<div class="sidebar-nav mb-3">
			<ul class="nav nav-tabs nav-tabs-solid nav-tabs-rounded nav-justified bg-transparent" role="tablist">
				<li class="nav-item"><a class="nav-link active border-0" href="#">Menu</a></li>
				<li class="nav-item"><a class="nav-link border-0" href="chat.php">Chats</a></li>
				<li class="nav-item"><a class="nav-link border-0" href="email.php">Inbox</a></li>
			</ul>
		</div>
	</div>
	<div class="sidebar-header p-3 pb-0 pt-2">
		<div class="text-center rounded bg-light p-2 mb-4 sidebar-profile d-flex align-items-center">
			<div class="avatar avatar-md onlin">
				<img src="assets/img/profiles/avatar-02.jpg" alt="Img" class="img-fluid rounded-circle">
			</div>
			<div class="text-start sidebar-profile-info ms-2">
				<h6 class="fs-12 fw-normal mb-1">Adrian Herman</h6>
				<p class="fs-10">System Admin</p>
			</div>
		</div>
		<div class="input-group input-group-flat d-inline-flex mb-4">
			<span class="input-icon-addon">
				<i class="ti ti-search"></i>
			</span>
			<input type="text" class="form-control" placeholder="Search in HRMS">
			<span class="input-group-text">
				<kbd>CTRL + / </kbd>
			</span>
		</div>
		<div class="d-flex align-items-center justify-content-between menu-item mb-3">
			<div class="me-3">
				<a href="calendar.php" class="btn btn-menubar">
					<i class="ti ti-layout-grid-remove"></i>
				</a>
			</div>
			<div class="me-3 notification-item">
				<a href="activity.php" class="btn btn-menubar position-relative me-1">
					<i class="ti ti-bell"></i>
					<span class="notification-status-dot"></span>
				</a>
			</div>
			<div class="me-0">
				<a href="email.php" class="btn btn-menubar">
					<i class="ti ti-message"></i>
				</a>
			</div>
		</div>
	</div>
	<div class="sidebar-inner slimscroll">
		<div id="sidebar-menu" class="sidebar-menu">
			<ul>
				<li class="menu-title"><span>MAIN MENU</span></li>
				<li>
					<ul>
						<li class="submenu">
							<a href="javascript:void(0);" class=" <?php echo ($page == 'index.php') ? 'active subdrop' : ''; ?>">

								<i class="ti ti-smart-home"></i>
								<span>Dashboard</span>
								<span class="menu-arrow"></span>
							</a>
							<ul>
								<li><a href="index.php" class="<?php echo ($page == 'index.php') ? 'active' : ''; ?>">Dashboard</a></li>
							</ul>
						</li>
					</ul>
				</li>
				<li class="menu-title"><span>Admin Controller</span></li>
				<li>
					<ul>
						<li class="<?php echo ($page == 'users.php') ? 'active' : ''; ?>">
							<a href="users.php">
								<i class="ti ti-users"></i><span>Admin User</span>
							</a>
						</li>
						<li class="<?php echo ($page == 'add-epg.php') ? 'active' : ''; ?>">
							<a href="add-epg.php">
								<i class="ti ti-calendar-event"></i><span>EPG</span>
							</a>
						</li>
					</ul>
				</li>
				<li class="menu-title"><span>Other</span></li>
				<li>
					<ul>
						<li class="<?php echo ($page == 'privacy-policy.php') ? 'active' : ''; ?>">
							<a href="privacy-policy.php">
								<i class="ti ti-file-description"></i><span>Privacy Policy</span>
							</a>
						</li>
						<li class="<?php echo ($page == 'terms-condition.php') ? 'active' : ''; ?>">
							<a href="terms-condition.php">
								<i class="ti ti-file-check"></i><span>Terms & Conditions</span>
							</a>
						</li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
</div>
<!-- /Sidebar -->