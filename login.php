<?php
include(__DIR__ . '/settings/database/conn.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$email = $_POST['email'] ?? '';
	$password = $_POST['password'] ?? '';
	if (!empty($email) && !empty($password)) {

		$user = $conn->prepare("SELECT * FROM `users` WHERE `email` = ?");
		$user->execute([$email]);
		$user = $user->fetch(PDO::FETCH_ASSOC);
		if ($user && password_verify($password, $user['password'])) {
			session_start();
			$_SESSION['loggedin'] = true;
			$_SESSION['userId'] = $user['id'];
			$_SESSION['userDetails'] = $user;

			header("Location: index.php");
			exit;
		} else {
			$error = "Invalid Employee ID or Password";
		}
	} else {
		$error =  "All fields are required.";
	}
}

?>
<?php include 'layouts/head-main.php'; ?>

<head>
	<title>MercyTV Admin Panel</title>
	<?php include 'layouts/title-meta.php'; ?>
	<?php include 'layouts/head-css.php'; ?>
</head>

<body class="bg-white">
	<div id="global-loader" style="display: none;">
		<div class="page-loader"></div>
	</div>

	<div class="main-wrapper">

		<div class="container-fuild">
			<div class="w-100 overflow-hidden position-relative flex-wrap d-block vh-100">
				<div class="row">
					<div class="col-lg-5">
						<div class="login-background position-relative d-lg-flex align-items-center justify-content-center d-none flex-wrap vh-100">
							<div class="bg-overlay-img">
								<img src="assets/img/bg/bg-01.png" class="bg-1" alt="Img">
								<img src="assets/img/bg/bg-02.png" class="bg-2" alt="Img">
								<img src="assets/img/bg/bg-03.png" class="bg-3" alt="Img">
							</div>
							<div class="authentication-card w-100">
								<div class="authen-overlay-item border w-100">
									<h1 class="text-white display-1">Empowering people <br> through seamless MercyTV <br> Network.</h1>
									<div class="my-4 mx-auto authen-overlay-img">
										<img src="assets/img/bg/authentication-bg-01.png" alt="Img">
									</div>
									<div>
										<p class="text-white fs-20 fw-semibold text-center">Content manager of your media, streamline <br> operations effortlessly.</p>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-7 col-md-12 col-sm-12">
						<?php
						if (isset($error) && $error != '') {
							echo '<div class="alert alert-danger" role="alert">
							' . $error . '
						</div>';
						}
						?>
						<div class="row justify-content-center align-items-center vh-100 overflow-auto flex-wrap">
							<div class="col-md-7 mx-auto vh-100">
								<form action="<?php echo $_SERVER['SELF'] ?>" class="vh-100" method="post">
									<div class="vh-100 d-flex flex-column justify-content-between p-4 pb-0">
										<div class=" mx-auto mb-5 text-center">
											<img src="assets/img/LOGO.png"
												class="img-fluid" alt="Logo">
										</div>
										<div class="">
											<div class="text-center mb-3">
												<h2 class="mb-2">Sign In</h2>
												<p class="mb-0">Please enter your details to sign in</p>
											</div>
											<div class="mb-3">
												<label class="form-label">Email ID</label>
												<div class="input-group">
													<input type="email" class="form-control border-end-0" name="email" required>
													<span class="input-group-text border-start-0">
														<i class="ti ti-mail"></i>
													</span>
												</div>
											</div>
											<div class="mb-3">
												<label class="form-label">Password</label>
												<div class="pass-group">
													<input type="password" name="password" class="pass-input form-control" required>
													<span class="ti toggle-password ti-eye-off"></span>
												</div>
											</div>
											<div class="d-flex align-items-center justify-content-between mb-3">
												<div class="d-flex align-items-center">
													<div class="form-check form-check-md mb-0">
														<!-- <input class="form-check-input" id="remember_me" type="checkbox">
														<label for="remember_me" class="form-check-label mt-0">Remember Me</label> -->
													</div>
												</div>
												<div class="text-end">
													<a href="forgot-password.php" class="link-danger">Forgot Password?</a>
												</div>
											</div>
											<div class="mb-3">
												<button type="submit" class="btn btn-primary w-100">Sign In</button>
											</div>

										</div>
										<div class="mt-3 pb-1 text-center">
											<p class="mb-0 text-gray-9">Copyright &copy; <?php echo date('Y') ?> - MercyTV</p>
										</div>
										<div class="text-center">
											<p class="mb-0 text-primary">Made By <a href="https://softairtechnology.com">SoftAir Technology</a></p>
										</div>
									</div>
								</form>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>


	</div>
	<!-- end main wrapper-->
	<!-- JAVASCRIPT -->
	<?php include 'layouts/vendor-scripts.php'; ?>
</body>

</html>