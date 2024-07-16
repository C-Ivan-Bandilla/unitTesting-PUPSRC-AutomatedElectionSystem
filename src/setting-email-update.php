<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, 'includes/classes/file-utils.php');
require_once FileUtils::normalizeFilePath('includes/classes/db-connector.php');
require_once FileUtils::normalizeFilePath('includes/session-handler.php');
include_once FileUtils::normalizeFilePath('includes/error-reporting.php');

if (isset($_SESSION['voter_id']) && (isset($_SESSION['role'])) && ($_SESSION['role'] == 'student_voter')) {


  $token = $_GET['token'];
  $token_hash = hash('sha256', $token);
  
  // Get the org name from the URL
  $url_org_name = $_GET['orgName'];
  $org_name = $url_org_name;
  $_SESSION['organization'] = $org_name;
  
  include_once FileUtils::normalizeFilePath(__DIR__ . '/includes/session-exchange.php');
  
  $connection = DatabaseConnection::connect();
   
  $sql = "SELECT reset_token_hash, reset_token_expires_at FROM voter WHERE reset_token_hash = ?";
  $stmt = $connection->prepare($sql);
  $stmt->bind_param("s", $token_hash);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  
 if (!$row) {
      $_SESSION['error_message'] = 'Reset link was not found.';
      header("Location: voter-login");
      exit();
  } 
  
  $expiry_time = strtotime($row["reset_token_expires_at"]);
  $current_time = time();
  
  if ($expiry_time <= $current_time) {
      $_SESSION['error_message'] = 'Reset link has expired.';
      header("Location: voter-login");
      exit();
  }
  
  if (isset($_SESSION['error_message'])) {
      $error_message = $_SESSION['error_message'];
      unset($_SESSION['error_message']); 
  }
  
  ?>
  

  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Update Email</title>
    <link rel="icon" type="image/x-icon" href="images/resc/ivote-favicon.png">

    <!-- Montserrat Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <!-- Fontawesome CDN Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
    <!-- Bootstrap 5 code -->
    <link type="text/css" href="../vendor/node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/loader.css" />
    <link rel="stylesheet" href="styles/feedback-suggestions.css" />
    <link rel="stylesheet" href="styles/setting-email.css" />
    <link rel="stylesheet" href="<?php echo '../src/styles/orgs/' . $org_acronym . '.css'; ?>">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

  </head>

  <body>

    <?php
    include_once FileUtils::normalizeFilePath(__DIR__ . '/includes/components/loader.html');
    include_once FileUtils::normalizeFilePath(__DIR__ . '/includes/components/topnavbar.php');
    ?>
    <div class="main" id="change-password">
      <div class="container-email">
        <div class="row justify-content-center">
          <!-- FOR VERIFICATION TABLE -->
          <div class="col-md-10 card-box">
            <div class="table-wrapper" id="profile">
              <form id="update-email-form" class="needs-validation">
                <input type="hidden" id="token" name="token" value="<?= htmlspecialchars($token) ?>">
                <div class="img-container">
                  <img src="images/Emails/<?php echo strtolower($org_name); ?>-email.png" alt="Email Icon" class="forgot-password-icon">
                </div>

                <div class="form-group">
                  <h4 class="reset-password-title text-center main-color <?php echo strtoupper($org_name); ?>-text-color" id="">Update your email address</h4>
                  <p class="reset-password-subtitle text-center"><b>Please enter your new email address below to update your <br> account information.</>
                  </p>
                  <div class="row mt-5 mb-3 reset-pass">
                    <div class="col-md-8 mb-2 position-relative">
                      <div class="input-group" id="reset-password">
                        <input type="email" class="form-control reset-password-password2" name="email" onkeypress="return preventSpaces(event)" placeholder="Enter a new email address" id="password" required>
                        <label for="email" class="new-password translate-middle-y main-color <?php echo strtoupper($org_name); ?>-text-color" id="">NEW EMAIL ADDRESS</label>
                      </div>
                      <div id="emailError" class="text-danger ps-3" style="font-size: small;"></div>
                    </div>
                    <div class="col-md-8 mb-0 mt-4 position-relative">
                      <p class="reset-password-subtitlee">For security purposes, please confirm the change of your email address by entering your password.</p>
                      <div class="input-group" id="reset-password">
                        <input type="password" class="form-control reset-password-password" onkeypress="return preventSpaces(event)" id="password_confirmation" name="password_confirmation" placeholder="Enter your password" required>
                        <label for="password_confirmation" class="new-password translate-middle-y main-color <?php echo strtoupper($org_name); ?>-text-color" id="">PASSWORD</label>
                        <button class="btn btn-secondary reset-password-password" type="button" id="reset-password-toggle-2">
                          <i class="fas fa-eye-slash"></i>
                        </button>
                      </div>
                      <div id="passwordError" class="text-danger ps-3" style="font-size: small;"></div>
                    </div>
                  </div>
                  <div class="pb-4">
                    <center><button class="btn main-bg-color text-white mt-3 mb-3" id="submit-new-email" type="submit" disabled>Update Email</button></center>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Success Modal -->
    <div class="modal" id="successEmailModal" tabindex="-1" role="dialog" aria-hidden="false" 
          data-backdrop="static" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" id="success-modal">
                <div class="modal-body">
                    <div class="text-center">
                        <div class="col-md-12">
                            <img src="images/resc/check-animation.gif" class="check-perc" alt="Checked Logo">
                        </div>
                        <div class="row">
                            <div class="col-md-12 pb-3">
                                <p class="fw-bold fs-3 text-success spacing-4">Successfully updated!</p>
                                <p class="fw-medium spacing-5">Your email address has been updated.
                                </p>
                                <button class="button-check main-bg-color text-white py-2 px-4" id="Home">
                                  <a class="custom-link" href="includes/voter-logout.php"><b>Back to Landing Page</b></a>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer">
      <?php include_once __DIR__ . '/includes/components/footer.php'; ?>
    </div>

    <script src="../src/scripts/feather.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="../vendor/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="scripts/loader.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="scripts/setting-email-update.js"></script>

  </body>

  </html>

<?php
} else {
  header("Location: landing-page");
}
?>