<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/includes/classes/file-utils.php');
require_once FileUtils::normalizeFilePath('includes/classes/db-connector.php');
require_once FileUtils::normalizeFilePath('includes/session-handler.php');
require_once FileUtils::normalizeFilePath('includes/classes/session-manager.php');
require_once FileUtils::normalizeFilePath('includes/classes/query-handler.php');

if (isset($_SESSION['voter_id']) && ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'head_admin')) {



	include FileUtils::normalizeFilePath('includes/session-exchange.php');
	include FileUtils::normalizeFilePath('submission_handlers/manage-acc.php');
?>

	<!DOCTYPE html>
	<html lang="en">

	<head>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="icon" type="image/x-icon" href="images/resc/ivote-favicon.png">
		<title>Manage Account</title>

		<!-- Icons -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
		<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">

		<!-- Styles -->
		<link rel="stylesheet" href="<?php echo 'styles/orgs/' . $org_name . '.css'; ?>" id="org-style">
		<link rel="stylesheet" href="styles/style.css" />
		<link rel="stylesheet" href="styles/core.css" />
		<link rel="stylesheet" href="styles/tables.css" />
		<link rel="stylesheet" href="styles/manage-voters.css" />
		<link rel="stylesheet" href="styles/profile.css" />
		<link rel="stylesheet" href="styles/loader.css" />
		<link rel="stylesheet" href="../vendor/node_modules/bootstrap/dist/css/bootstrap.min.css" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

	</head>

	<body>

		<?php 
        include_once FileUtils::normalizeFilePath(__DIR__ . '/includes/components/loader.html');
        include_once FileUtils::normalizeFilePath(__DIR__ . '/includes/components/sidebar.php');
        ?>

		<div class="main">


			<div class="container">
				<div class="row justify-content-center">
					<!-- FOR VERIFICATION TABLE -->
					<div class="col-md-10 card-box">
						<div class="table-wrapper" id="profile">
							<div class="table-title">
								<div class="row">
									<!-- Table Header -->
									<div class="col-sm-6">
										<p class="fs-3 main-color fw-bold ls-10 spacing-6">Profile Information</p>
									</div>
									<div class="col-sm-6">
										<div class="row">
											<div class="col-md-12 text-end flex-end hehe">
												<a href="edit-profile.php" type="button" class="btn main-color edit-button">Edit Profile</a>
											</div>
										</div>
									</div>
								</div>
								<div class="row mt-3">
									<div class="col-md-6 text-center position-relative card-container">
										<div class="member-card main-border-color">
											<div class="member-card-header main-bg-color">
												iVOTE Committee Role
											</div>
											<div class="member-card-body">
												MEMBER
											</div>
										</div>
										<div class="vertical-line"></div>
										
									</div>
									<div class="col-md-6">
									<hr>
										<div class="profile-info">
											<div>
												<span class="label main-color">Full Name</span>
												<p class="user-name text-truncate">Dator, Rhey Yuri Marcelino</p>
											</div>
											<div>
												<span class="label main-color">Email Address</span>
												<p class="user-email text-truncate">rhey.yuridator@gmail.com</p>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>

		<?php include_once __DIR__ . '/includes/components/footer.php'; ?>

		<script src="../vendor/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
		<script src="scripts/script.js"></script>
		<script src="scripts/feather.js"></script>
		<script src="scripts/table-funcs.js"></script>
		<script src="scripts/loader.js"></script>

	</body>


	</html>

<?php
} else {
	header("Location: landing-page.php");
}
?>