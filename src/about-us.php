<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, 'includes/classes/file-utils.php');
require_once FileUtils::normalizeFilePath('includes/session-handler.php');
require_once FileUtils::normalizeFilePath('includes/classes/session-manager.php');

// Check if voter_id and role is set in session
SessionManager::checkUserRoleAndRedirect();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Fontawesome Link for Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="styles/dist/landing.css">
  <link rel="stylesheet" href="styles/loader.css">
  <link rel="stylesheet" href="styles/dist/all-footer.css">
  <link rel="stylesheet" href="styles/dist/landing-animation.css">
  <link rel="icon" href="images/resc/ivote-favicon.png" type="image/x-icon">
  <title>iVote</title>
  <title>iVote</title>

  <!-- Montserrat Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet">
</head>

<body id="index-body">

  <?php
  include_once FileUtils::normalizeFilePath('includes/components/loader.html');
  include_once FileUtils::normalizeFilePath(__DIR__ . '/includes/components/outside-header.php');
  ?>

  <!-- Parallax section -->
  <section class="parallax">
    <div class="container">
      <div class="row fade-in">
        <div class="col text-center text-white justify-content-center slide-in">
          <img src="images/resc/iVOTE4.webp" class="img-fluid ivote-main-logo" alt="iVote Logo">
          <h5 id="index-PUPSRC" class="text-truncate mt-3">Polytechnic University of the Philippines - Santa Rosa Campus
          </h5>
          <h1 id="index-AES">AUTOMATED ELECTION SYSTEM</h1>
        </div>
      </div>
      <div class="index-wave-footer">
        <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
          <path
            d="M985.66,92.83C906.67,72,823.78,31,743.84,14.19c-82.26-17.34-168.06-16.33-250.45.39-57.84,11.73-114,31.07-172,41.86A600.21,600.21,0,0,1,0,27.35V120H1200V95.8C1132.19,118.92,1055.71,111.31,985.66,92.83Z"
            class="shape-fill"></path>
        </svg>
      </div>
    </div>
  </section>

  <!-- Normal section -->
  <section class="about-us-section" id="normal-section">
    <div class="container-fluid fade-in">
      <div class="row">
        <div class="col col-md-6 about-us slide-in">
          <img src="images/resc/icons/about-us.png" alt="about us icon" class="about-us-icon">
        </div>
        <div class="col col-md-6 about-us-body slide-in">
          <h2 class="landing-organization-title"><span class="hello-text">About</span> iVote</h2>
          <p class="about-us-body">Introducing iVote, the Automated Election System for Polytechnic University of the
            Philippines Santa Rosa Campus (PUPSRC). Say goodbye to the old manual process and hello to a modern,
            hassle-free voting experience! No more long queues or paper ballots – just a straightforward, efficient
            system designed to make your voice heard. Trust iVote to bring accuracy, ease, and transparency to campus
            and student organizations elections. </p>
          <p class="landing-organization-title">Connect with the team</p>
          <a href="https://twitter.com/iVOTEpupsrc"><img
              src="data:image/webp;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAADw0lEQVR4nO2ZXYgURxDHW+NXjBqN8cFPJCQvCUIwoijIIQgBFX1Ilng33XtJ1HsIEUEFUYRmqsZvQyKC5lDwKS8GjaAoKvEDQhDZ3LlVc55KiDFRk5D4rZh46kqvu+uonNO3N7M7hP3BwjLb2zX/nqrq6hohatSoUeP/QS7XQwEdUMi5F36AHiqgGVGbl8jzijYk8PGFm871LXuytNc6UgFfDRWDdGme9l+LSoRy+X2F3FEQ0d6whoZ0e1IJnA4XwjmJ/E0UItLQ9o4Cvl5YoL+l9t8UUSGRvrURo5A/6o4dxzs9XCJdeLwwdFe5NFlESb0+87pE+sPiqVxzdHZUOTZS2h8gkVqKceegXy/iwAF/tpWLAR00iaJLInbufEkC7wksyFIRJwp5h52L+Qu6OO/mQOLYJuLG0ecGKaBfw1My37YNUoW0OJDKD9Tpo71EJXDc7FSJ9CA8XugH4zIvFAE8SwHfL4z3U2szr1ZEROkGkL6ycjGgJZ3N0Qj0nnlyxX3oY+TRFRWRvwl9vp9ZQYvA/7cBsuOe/3/rWAX8Z0HErUbPf1dUC8fLjpfA9yxcrCWl/T5PxRkyFWLpvsmGotpIYNdy1wczvqk501sCHy5dd+lzkQTq9NFeCumkhZgO6bZNksBfB8RtFEnCWeW/nS8nwuPl5pPvvFvrXE+RNBTQEstazDyJjNxw6hWRRLTO9ZTIx2yEpMH/UCSZRpNSkW6EuxhfjOR8EScO8HxLF9shko5E2msVKx5/IJLMXN0+QiH9ZyHmr09WtQwTSUUhr7XOYMB7RBJR6C8IpNk7lmLSIkmkXZ5WdCkJ9LM5j0ig30LFmEaD9seI5OzufK0g4p96aHvLXFdI0/N9r/By//uuHo8j59nOh0SeEvxdAW2xixlaKKpF6osfX1ZIJwqr+lAiOc+OadKZ/hL4bHjpQndNT6sqZYlC/i5QFC7rbKzpTxWPtGF1WFNzpndFhSjgTQEf3x42XgKvsSwqV1ayFFkUcIkjwRNgZ5gxCumUhZgO5dGE+EV4NLPkJkBtjbp1sO1/TQzYnF1Uft7z/WIUkR1f6nwAXS4n/0ug5XYuRutjEZF/xYD8e3HXTnv+xLLPLsDHLYQ8UC7VRSrCdD4kcLZkwMvO6c58DZreMG0gi13/l0/XtQ+MRIRJhwr5UNQbl3L5M8visjmqV2BbA3vFlyIqcrkeEni/jRjHo5ndsuUgrwg85n1hvdxyzi4S6YpFFrusVp8eWpYR5VGq1KwG/imuzocpayyz2K447NeoUaOGqAqPAOgfysMaeqR8AAAAAElFTkSuQmCC"
              class="connect-with-us"></a>
          <a href="https://www.facebook.com/profile.php?id=61558930417110"><img
              src="data:image/webp;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAADaklEQVR4nO2Yv08UURDHF39Q+CPGWFn4I1FLbew1ahTFwiieRm7eoRbE+A/YaJabOQGVRguNiQ2xUNAGFWPUxNgZQwI3s4D2doKCioAFa94ChnC3x+7tu9s14ZtMcsXezPvsvDc7byxrWctKtlK2s04h1yuSdiB+rkg+KZRvgPJH29zvYYX8DFDaMtn8Mf0fKxFy3Rq9IIXSDchTisQNY0A8qUi6gJyj2pcVh1SOU4qEwy7e33hAoZyqGsA5HNylSN6aAyiw12A7OysKATlpUChjFYRwvS2H/ENhvtE8gevWKJKWSgOoQms3d3ZctwZI7sUA4XqGfNcIjC6VsUHQPxiKBKFIzsYOQXPnhjgdoTrxT0OLmFTID3TJ9vy2Dm1KtfdtaGzjjU12//ZGzO8Gko7Sfng8Q7KjnGwYKbGA8iadG9q8VDwgvhDghbwKBZFB57SZTEjvfvvdqiAxg4AoEjeT45NhSm3kLzYgj+gtFPTlBQUB5P5AVczrncwc0NagEGFAlLas1C3pUDeAJkAy13mPX4wm5L2AcseLNW/EH4P750clIS7eGF5fThdbJNB4qrt7pR+EIp6O5B/ld7Pdt6ZENrz7hIFtxQO+MYg7jWSc5Ig/CEm7iSCA8t4vBqB8qfgZhNmbnQmQl759m74tmsl6jz8IymcjICS9xfynbKfWUDZcfW0ulZHR/wVEEX/1BYlcTaqaEZ4yCqJbBt38LbRS05HFz84bEN8MmZHpEltLvod+M1k+bBkQID8OmZERX2feLCo+kP5QsZEH/Z2R9MYBYtvuCiCZMFZ+9UcmDpBGGtxm9PqbzvHx0A6zcln3TwvNdy7lujWLn/V6ryxfKiNunS+IbsQUyq/QTqtcfoFkAm4NrC2ZZiB+knQQRdJlLSXdVSYdJJ3NHwo4kGMnuSDse0UokMrlTyQWBLk+MEjUcVClQCDsOEhLD868qXhSQFDGlO1stcqRbggV8kzsIMgzaeQzVhQBCcYOQtJimZBCuR0XCJB0WCYFyFeCbjMjIOjFMpOJApicNAS5s0QFAeLR4DPeKJ0qyovKgXDPeZItVrU02ykXH3OWB8If9NzZiksZyu8Dkod6TBoWZPY7xZ3ah5UUpWynFsg5CCRXdWEo9kzz/b7VCvmpQr6WycoB/Z/qr3RZy7LC6C+bcabimCLC+AAAAABJRU5ErkJggg=="
              class="connect-with-us" alt="Connect with us"></a>
          <a href="https://www.instagram.com/ivotepupsrc/"><img
              src="data:image/webp;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAADoElEQVR4nO2aS08UQRCA56BeJD7ueDAqmngxCh6MaKJ4Bk046HbvevIPKHicbPXwiifEg2gA/4MRo8simHgjcadqUeSMJ18RCHoRTM3OLASZ2Xm5DQmVVEII3V1f16NrujGMXdkV/ZIB+6QAvC8VTQigjxJoWSpaS0WBlnlOAVQQirp5rdQBpIXNErCYmtGh4fCdUHQhMcCd4Zm9EvCRBFytO4SqwvDaQ5fNN3tiQdzqw8NC4aQ2APUPUJFtiuOJ+oeSqqlvO83yvtAgTjjpN3ptKxVAD6Mktr6cUDVDbDUHeC6MN7ZjSK1t8kohEIJrt1YDFb3ImHYjqwAaD/rbmzB7whfEPey0gWRMu9Gz5baiI8EhRl0BIFTYMSCKXgeBfEoeHvhNKBqRlt3utDQPSvtZ3famQwKNSqDvW44FGmcABwLwZY215oJCazE2BNCKUGhlzPkDgYloGEZn/8xBCdjDY2JvGOCi7wLxJ6WFjSVR5PGaVPiUd81pLKsNIT6RCtuq61nYzGPjrmukCcKGeLEte0tNAmi6dvjRlFd13Aq1oBcEaMXzRFbZl/xi3y+XMnm71YHJU4tQ+EsbCOeE54koEBthhFk+7q7drwWEjeCkdXIiRDgFzDPJc+TM94eibkZKIDSyntjxIDzN5O2r7vpj9Q8ty26vjHGqUyIQqWjYyTMLr9cfpLfU5I6ZSwrCpTlOr2ekAdJplhtcjywl9wgu8VydZrlhp4P8dDxizh/Y2aGl8IO20BKAHTym0nYkBAF67KwPdKP+HgEadUOrLblHylfcTXmm90BUNBUbBLDoHYhC0Y/6e6RiRE8FpHRUKvySqEUBHIg6PjUQp9GzsNlJ1LzdyoaFHgv4VaryRR6btcrntTaNclMbz7sb6nYSsJhVdGz9kxY/x1nbSBOkCpOnFm8e7p247eCyWjlncEkCznJ18hLb80RcCBkEkuRTl0NDAPVx0ho1pNLp4oAA/P1fPnVFCpcPbis+xg1grgdPcQfAyj/z77jERq1OMvrlg97rIBnNI6/8QRR16zZQhvY83vUF4QsB7QaqaL2er/D7oHYjVU31v2X0JGeVzwiFf7aBsWtbKdsW6lnB9cqQboOlnwINGmGFHx63Y4gJoOlIT28s/PC4zR59JsIctr6e4Tc7nU9xgvMVaDD28/RGycLsaaHwuYZQKmQs+6yRtjjnDFAXlz/nXzhSuXjwFJe4yeQTWyi6F/i0tiu7YtRN/gIfvSaMXEnrtwAAAABJRU5ErkJggg=="
              class="connect-with-us"></a>
        </div>
      </div>
    </div>
  </section>

  <?php include_once FileUtils::normalizeFilePath(__DIR__ . '/includes/components/all-footer.php'); ?>

  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="scripts/landing-animation.js"></script>
  <script src="scripts/loader.js"></script>

  <!-- JavaScript for dynamic text change PUPSRC -->
  <script>
    const indexPUPSRC = document.getElementById('index-PUPSRC');

    function updateText() {
      if (window.innerWidth <= 768) {
        indexPUPSRC.textContent = 'PUP SANTA ROSA CAMPUS';
      } else {
        indexPUPSRC.textContent = 'Polytechnic University of the Philippines - Santa Rosa Campus';
      }
    }

    window.addEventListener('load', updateText);
    window.addEventListener('resize', updateText);
  </script>

  <script>
    // Wait for the document to finish loading
    document.addEventListener("DOMContentLoaded", function () {
      // Get the element to scroll to
      var normalSection = document.getElementById("normal-section");
      // Scroll to the element
      normalSection.scrollIntoView();
    });
  </script>

</body>

</html>