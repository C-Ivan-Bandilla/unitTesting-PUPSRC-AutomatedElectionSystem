<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/includes/classes/file-utils.php');
require_once FileUtils::normalizeFilePath('includes/session-handler.php');
require_once FileUtils::normalizeFilePath('includes/classes/session-manager.php');
include_once FileUtils::normalizeFilePath('includes/organization-list.php');

SessionManager::checkUserRoleAndRedirect();
session_destroy();

$warning_message = '';
if (isset($_SESSION['warning_message'])) {
  $warning_message = $_SESSION['warning_message'];
  unset($_SESSION['warning_message']);
}

$warning_message_json = json_encode($warning_message);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Fontawesome Link for Icons -->
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"> -->

  <!-- Bootstrap 5 -->
  <link rel="stylesheet" href="../vendor/node_modules/bootstrap/dist/css/bootstrap.min.css" />

  <!-- Custom Stylesheets -->
  <link rel="stylesheet" href="styles/dist/landing.css">
  <link rel="stylesheet" href="styles/dist/all-footer.css">
  <link rel="stylesheet" href="styles/dist/landing-animation.css">

  <!-- Preloader Stylesheet and Image -->
  <link rel="preload" href="images/resc/ivote-icon.webp" as="image">
  <link rel="stylesheet" href="styles/loader.css" />

  <!-- Favicon -->
  <link rel="icon" href="images/resc/ivote-favicon.png" type="image/x-icon">
  <title>iVote</title>

  <!-- Montserrat Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet">

  <!-- Bootstrap JavaScript -->
  <script src="../vendor/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <!-- Custom JavaScript -->
  <script src="scripts/landing-page.js" defer></script>
  <script src="scripts/loader.js" defer></script>
</head>

<body id="index-body">

  <!-- Preloader -->
  <?php
  include_once FileUtils::normalizeFilePath(__DIR__ . '/includes/components/loader.html');
  include_once FileUtils::normalizeFilePath(__DIR__ . '/includes/components/outside-header.php');
  ?>

  <!-- Parallax section -->
  <section class="parallax">
    <div class="container first-section">

      <script>
        const warningMessage = <?php echo ($warning_message_json); ?>;
      </script>
      <!-- Warning Modal -->
      <div class="modal fade" id="susActivityModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-body">
              <div class="d-flex justify-content-end">
                <i class="fa fa-solid fa-circle-xmark fa-xl close-mark light-gray mt-2" role="button"
                  data-bs-dismiss="modal"></i>
              </div>
              <div class="text-center">
                <div class="col-md-12">
                  <img src="images/resc/yellow-warning.png" class="mt-3 warning-icon" alt="iVote Logo">
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <p class="fw-bold fs-3 text-warning spacing-4 mt-4 warning-title">Oops!</p>
                    <p class="fw-medium spacing-5 warning-subtitle" id="warningMessage">
                      <!-- Warning message loads here --></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row fade-in">
        <div class="col text-center text-white justify-content-center slide-in">
          <img src="images/resc/iVOTE4.webp" class="img-fluid ivote-main-logo" alt="iVote Logo">
          <h5 id="index-PUPSRC" class="text-truncate mt-3">Polytechnic University of the Philippines - Santa Rosa Campus
          </h5>
          <h1 id="index-AES">AUTOMATED ELECTION SYSTEM</h1>
          <a href="#organizations" type="button" class="btn btn-primary fw-bold index-button">Select Organization</a>
        </div>
      </div>
      <!-- <div class="index-wave-footer" id="organizations">
          <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M985.66,92.83C906.67,72,823.78,31,743.84,14.19c-82.26-17.34-168.06-16.33-250.45.39-57.84,11.73-114,31.07-172,41.86A600.21,600.21,0,0,1,0,27.35V120H1200V95.8C1132.19,118.92,1055.71,111.31,985.66,92.83Z" class="shape-fill"></path>
          </svg>
        </div> -->
    </div>
  </section>

  <!-- Normal section -->
  <section class="organizations">
    <div class="index-wave-footer" id="organizations">
      <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
        <path
          d="M985.66,92.83C906.67,72,823.78,31,743.84,14.19c-82.26-17.34-168.06-16.33-250.45.39-57.84,11.73-114,31.07-172,41.86A600.21,600.21,0,0,1,0,27.35V120H1200V95.8C1132.19,118.92,1055.71,111.31,985.66,92.83Z"
          class="shape-fill"></path>
      </svg>
    </div>
    <form action="includes/classes/landing-page-controller.php" method="post">
      <div class="container-fluid slide-in">
        <h2 class="landing-organization-title"><span class="hello-text">Hello,</span> Isko’t Iska!</h2>
        <p class="landing-organization-subtitle">- Select your Organization - </p>

        <div class="container-fluid">
          <div class="row justify-content-center text-center">
            <div class="col-md-3 mb-4">
              <button type="submit" name="submit_btn" value="<?php echo htmlspecialchars($org_acronyms['sco']); ?>"
                class="landing-page-org-card" id="SCO-landing-logo">
                <img src="images/logos/sco.webp" alt="SCO Logo" class="landing-page-logo-size">
                <h5 class="fw-bold pt-2 text-capitalize"><?php echo htmlspecialchars($org_full_names['sco']); ?></h5>
              </button>
            </div>
          </div>
        </div>

        <div class="container-fluid">
          <div class="row justify-content-center text-center">
            <div class="col-md-3 mb-4" id="index-ACAP">
              <button type="submit" name="submit_btn" value="<?php echo htmlspecialchars($org_acronyms['acap']); ?>"
                class="landing-page-org-card" id="ACAP-landing-logo">
                <img src="images/logos/acap.webp" alt="ACAP Logo" class="landing-page-logo-size">
                <h5 class="fw-bold pt-2 text-uppercase"><?php echo htmlspecialchars($org_acronyms['acap']); ?></h5>
              </button>
            </div>

            <div class="col-md-3 mb-4" id="index-AECES">
              <button type="submit" name="submit_btn" value="<?php echo htmlspecialchars($org_acronyms['aeces']); ?>"
                class="landing-page-org-card" id="AECES-landing-logo">
                <img src="images/logos/aeces.webp" alt="AECES Logo" class="landing-page-logo-size">
                <h5 class="fw-bold pt-2 text-uppercase"><?php echo htmlspecialchars($org_acronyms['aeces']); ?></h5>
              </button>
            </div>

            <div class="col-md-3 mb-4" id="index-AECES">
              <button type="submit" name="submit_btn" value="<?php echo htmlspecialchars($org_acronyms['elite']); ?>"
                class="landing-page-org-card" id="ELITE-landing-logo">
                <img src="images/logos/elite.webp" alt="ELITE Logo" class="landing-page-logo-size">
                <h5 class="fw-bold pt-2 text-uppercase"><?php echo htmlspecialchars($org_acronyms['elite']); ?></h5>
              </button>
            </div>
          </div>
        </div>

        <div class="container-fluid">
          <div class="row justify-content-center text-center">
            <div class="col-md-3 mb-4" id="index-ACAP">
              <button type="submit" name="submit_btn" value="<?php echo htmlspecialchars($org_acronyms['give']); ?>"
                class="landing-page-org-card" id="GIVE-landing-logo">
                <img src="images/logos/give.webp" alt="GIVE Logo" class="landing-page-logo-size">
                <h5 class="fw-bold pt-2 text-uppercase"><?php echo htmlspecialchars($org_acronyms['give']); ?></h5>
              </button>
            </div>
            <div class="col-md-3 mb-4" id="index-JEHRA">
              <button type="submit" name="submit_btn" value="<?php echo htmlspecialchars($org_acronyms['jehra']); ?>"
                class="landing-page-org-card" id="JEHRA-landing-logo">
                <img src="images/logos/jehra.webp" alt="JEHRA Logo" class="landing-page-logo-size">
                <h5 class="fw-bold pt-2 text-uppercase"><?php echo htmlspecialchars($org_acronyms['jehra']); ?></h5>
              </button>
            </div>

            <div class="col-md-3 mb-4" id="index-JMAP">
              <button type="submit" name="submit_btn" value="<?php echo htmlspecialchars($org_acronyms['jmap']); ?>"
                class="landing-page-org-card" id="JMAP-landing-logo">
                <img src="images/logos/jmap.webp" alt="JMAP Logo" class="landing-page-logo-size">
                <h5 class="fw-bold pt-2 text-uppercase"><?php echo htmlspecialchars($org_acronyms['jmap']); ?></h5>
              </button>
            </div>

          </div>
        </div>

        <div class="container-fluid">
          <div class="row justify-content-center text-center">
            <div class="col-md-3 mb-4" id="index-JPIA">
              <button type="submit" name="submit_btn" value="<?php echo htmlspecialchars($org_acronyms['jpia']); ?>"
                class="landing-page-org-card" id="JPIA-landing-logo">
                <img src="images/logos/jpia.webp" alt="JPIA Logo" class="landing-page-logo-size">
                <h5 class="fw-bold pt-2 text-uppercase"><?php echo htmlspecialchars($org_acronyms['jpia']); ?></h5>
              </button>
            </div>
            <div class="col-md-3 mb-4" id="index-PIIE">
              <button type="submit" name="submit_btn" value="<?php echo htmlspecialchars($org_acronyms['piie']); ?>"
                class="landing-page-org-card" id="PIIE-landing-logo">
                <img src="images/logos/piie.webp" alt="PIIE Logo" class="landing-page-logo-size">
                <h5 class="fw-bold pt-2 text-uppercase"><?php echo htmlspecialchars($org_acronyms['piie']); ?></h5>
              </button>
            </div>
          </div>
        </div>
      </div>
      </div>
    </form>
  </section>

  <!-- Footer -->
  <?php include_once FileUtils::normalizeFilePath(__DIR__ . '/includes/components/all-footer.php'); ?>
  <script src="scripts/landing-animation.js"></script>
</body>

</html>