<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/includes/classes/file-utils.php');
require_once FileUtils::normalizeFilePath('includes/session-handler.php');
require_once FileUtils::normalizeFilePath('includes/classes/session-manager.php');
require_once FileUtils::normalizeFilePath('includes/classes/csrf-token.php');
// require_once FileUtils::normalizeFilePath('includes/classes/db-connector.php');
include_once FileUtils::normalizeFilePath('includes/session-exchange.php');
include_once FileUtils::normalizeFilePath('includes/default-time-zone.php');
include_once FileUtils::normalizeFilePath('includes/error-reporting.php');

SessionManager::checkUserRoleAndRedirect();

$csrf_token = CsrfToken::generateCSRFToken();

if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

if (isset($_SESSION['info_message'])) {
    $info_message = $_SESSION['info_message'];
    unset($_SESSION['info_message']);
}

$max_login_attempts = false;

if(isset($_SESSION['maxLimit']) && $_SESSION['maxLimit'] === true) {
    $max_login_attempts = $_SESSION['maxLimit'];
    unset($_SESSION['maxLimit']);
}

// $connection = DatabaseConnection::connect();

// $sql = "";
// $user_data = array();
// $head_admin = 'head_admin';
// $admin = 'admin';

// // Checks if organization is not set to sco
// if($_SESSION['organization'] !== 'sco') {
//     $sql = "SELECT email, account_status FROM voter";
//     $stmt = $connection->prepare($sql);
// }
// else {
//     $sql = "SELECT email, account_status FROM voter WHERE role = ? OR role = ?";
//     $stmt = $connection->prepare($sql);
//     $stmt->bind_param("ss", $head_admin, $admin);
// }

// $stmt->execute();
// $result = $stmt->get_result();

// if($result->num_rows > 0) {
//     while($row = $result->fetch_assoc()) {
//         $email = $row['email'];
//         $account_status = $row['account_status'];
//         // Store email and status as key value pairs
//         $user_data[$email] = $account_status;
//     }
// }

// $connection->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Fontawesome Link for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- I run into problems in the input field when i switched to this local bootstrap -->
    <!-- <link rel="stylesheet" href="../vendor/node_modules/bootstrap/dist/css/bootstrap.min.css" /> -->

    <!-- Online Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Akronim&family=Anton&family=Aoboshi+One&family=Audiowide&family=Black+Han+Sans&family=Braah+One&family=Bungee+Outline&family=Hammersmith+One&family=Krona+One&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet" />

    <!-- Custom Stylesheets -->
    <link rel="stylesheet" href="styles/dist/landing.css">
    <link rel="stylesheet" href="styles/orgs/<?php echo $org_name; ?>.css">

    <!-- Preloader Stylesheet and Image -->
    <link rel="preload" href="images/resc/ivote-icon.webp" as="image">
    <link rel="stylesheet" href="styles/loader.css" />

    <!-- Favicon -->
    <link rel="icon" href="images/resc/ivote-favicon.png" type="image/x-icon">
    <title>Login</title>

    <!-- Bootstrap JavaScript -->
    <script src="../vendor/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="scripts/voter-login.js" defer></script>
    <script src="scripts/loader.js" defer></script>
</head>

<body class="login-body" id="<?php echo strtoupper($org_name); ?>-body">

    <!-- Preloader -->
    <?php include_once FileUtils::normalizeFilePath(__DIR__ . '/includes/components/loader.html'); ?>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top" id="login-navbar">
        <div class="container-fluid d-flex justify-content-center align-items-center">
            <a href="landing-page.php"><img src="images/resc/iVOTE-Landing2.webp" id="ivote-logo-landing-header" alt="ivote-logo"></a>
        </div>
    </nav>


    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 login-left-section">
                <div class="organization-names">
                    <img src="images/logos/<?php echo $org_name; ?>.webp" class="login-logo pb-3" alt="<?php echo strtoupper($org_name) . ' '; ?>Logo">
                    <div class="org-full-name px-5"><?php echo strtoupper($org_full_name); ?></div>
                    <div class="login-AES px-4">AUTOMATED ELECTION SYSTEM</div>

                    <div class="login-wave-footer" id="<?php echo strtoupper($org_name); ?>-wave">
                        <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                            <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" opacity=".25" class="shape-fill"></path>
                            <path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z" opacity=".5" class="shape-fill"></path>
                            <path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z" class="shape-fill"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="col-md-6 login-right-section">

                <div>
                    <form id="loginForm" action="includes/voter-login-inc.php" method="post" class="login-form needs-validation" novalidate>
                                 
                        <!-- CSRF Token hidden field -->
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <h1 class="login-account">Account Log In</h1>
                        <p class="subtitle">Sign in to your account</p>

                        <!-- Displays error message -->
                        <?php if (isset($error_message)) : ?>
                            <div id="serverSideErrorMessage" class="fw-medium border border-danger text-danger alert alert-danger alert-dismissible fade show  custom-alert" role="alert">
                                <div class="d-flex align-items-center">
                                    <span class="pe-3"><?php echo $error_message; ?></span>
                                    <button type="button" class="btn-sm btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($info_message)) : ?>
                            <div id="serverSideInfoMessage" class="fw-medium border border-primary bg-transparent text-primary alert alert-primary alert-dismissible fade show custom-info" role="alert">
                                <div class="d-flex align-items-center">
                                    <span class="pe-3"><?php echo $info_message; ?></span>
                                    <button type="button" class="btn-sm btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="col-md-12 mt-0 mb-3">
                            <input type="email" class="form-control shadow-sm email" id="Email" name="email" placeholder="Email Address" required autocomplete="email">
                            
                            <div class="fw-medium text-start invalid-feedback" id="email-login-error">
                                <!-- Display error messages here  -->
                            </div>
                        </div>  

                        <div class="col-md-12 mb-2">
                            <div class="input-group">
                                <input type="password" class="form-control shadow-sm border border-end-0 password" name="password" placeholder="Password" id="Password" autocomplete="current-password" required>
                                <button class="btn shadow-sm border border-start-0 show-toggle" type="button" id="password-toggle">Show</button>
                            </div>
                            
                            <div class="mt-1 fw-medium text-start text-danger" id="password-login-error">
                                <!-- Display error messages here -->
                            </div>  
                        </div>

                        <div role="button" class="text-align-start" data-bs-toggle="modal" data-bs-target="#forgot-password-modal" id="forgot-password">Forgot Password</div>

                        <div class="d-grid gap-2 mt-5 mb-4">
                            <!-- <button class="btn btn-primary" name="sign_in" type="submit">Sign In</button> -->
                            <button class="btn login-sign-in-button <?php echo strtoupper($org_name); ?>-login-button" id="loginSubmitBtn" name="sign-in" type="submit">Sign In</button>
                        </div>
                        <p class="sign-up-redirect">Don't have an account? <a href="register.php" id="<?php echo strtolower($org_name); ?>SignUP" class="sign-up">Sign Up</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Will be used to display if login attempts reached maximum
        const maxLoginAttempts = <?php echo json_encode((bool)$max_login_attempts); ?>
    </script>

    <!-- Max Login Attempt Limit Modal -->
    <div class="modal" id="maxLimitReachedModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" id="max-modal">
                <div class="modal-body">
                    <div class="d-flex justify-content-end">
                        <i class="fa fa-solid fa-circle-xmark fa-xl close-mark light-gray" role="button" data-bs-dismiss="modal"></i>
                    </div>
                    <div class="text-center">
                        <div class="col-md-12 mt-3 mb-3">
                            <img src="images/resc/warning.png" class="warning-icon" alt="Warning Icon">
                        </div>
                        <div class="row">
                            <div class="col-md-12 pb-3">
                                <p class="fw-bold spacing-4 text-danger warning-title">Max Limit Reached</p>
                                <p class="fw-medium warning-subtitle">Sorry, you've reached the maximum number of attempts. For security reasons, please wait for <strong id="blockTime">30 minutes</strong> before trying again.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div class="modal fade" id="forgot-password-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="justify-content-center">
                    <h1 class="modal-title fw-bold mb-2 forgot-password-title" id="<?php echo strtolower($org_name); ?>SignUP">Forgot your password?
                    </h1><!-- <hr> -->
                </div>
                <div class="modal-body">
                    <form class="needs-validation" id="forgot-password-form" name="forgot-password-form" novalidate enctype="multipart/form-data">
                        <div class="col-12 col-md-12">
                            <div class="d-flex align-items-start mb-0 pb-0 forgot-pass-email-title">
                                <p for="email" class="form-label text-start ps-1 pb-3 forgot-password-subtitle">Don't sweat it, enter your registered email address below to recover your account.</p>
                                <!-- <p>Email Address</p> -->
                            </div>
                            <input type="email" class="form-control shadow-sm email" id="email" name="email" placeholder="Email Address" autocomplete="email">
                            <div class="valid-feedback text-start fw-medium" id="email-valid">
                            </div>
                            <div class="invalid-feedback text-start fw-medium" id="email-error">
                                <!-- Displays error messages -->
                            </div>

                            <!-- Will be used for validating user -->
                            <script>

                            </script>

                        </div>
                        <div class="col-md-12 mt-4">
                            <div class="row">
                                <div class="col-5" id="cancelBtnContainer">
                                    <button type="button" id="cancelReset" class="btn border border-0 cancel-button w-100" data-bs-dismiss="modal">Cancel</button>
                                </div>
                                <div class="col-7" id="sendBtnContainer">
                                    <button class="btn send-link-button w-100" id="<?php echo strtoupper($org_name); ?>-login-button" type="submit" name="send-email-btn">Send Link</button>
                                    <script>
                                        const ORG_NAME = "<?php echo strtoupper($org_name) . '-login-button'; ?>";
                                    </script>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Email Sending Modal -->
    <!-- <div class="modal" id="emailSending" tabindex="-1" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="text-center">
                        <div class="col-md-12">
                            <img src="images/resc/loader.gif" class="loading-gif" alt="iVote Logo">
                        </div>
                        <div class="row">
                            <div class="col-md-12 pt-4">
                                <p class="fw-bold spacing-4 sending-title">Sending email...</p>
                                <p class="fw-medium spacing-5 fs-7 sending-subtitle">Please wait for a moment</span>.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->

    <!-- Success Modal -->
    <div class="modal" id="successResetPasswordLinkModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" id="success-modal">
                <div class="modal-body">
                    <div class="d-flex justify-content-end">
                        <i class="fa fa-solid fa-circle-xmark fa-xl close-mark light-gray" role="button" data-bs-dismiss="modal"></i>
                    </div>
                    <div class="text-center">
                        <div class="col-md-12">
                            <img src="images/resc/check-animation.gif" class="check-perc" alt="iVote Logo">
                        </div>
                        <div class="row">
                            <div class="col-md-12 pb-3">
                                <p class="fw-bold text-success spacing-4 success-title">Success!</p>
                                <p class="fw-medium spacing-5 success-subtitle">An email containing the password reset link has been sent. Kindly check your email.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>