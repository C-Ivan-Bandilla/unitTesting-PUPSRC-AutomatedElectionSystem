
feather.replace();
  
    function handleInput() {
        var passwordInput = document.querySelector('.password-input');
        var toggleIcon = document.querySelector('.toggle-password');

        if (passwordInput.value.trim() === '') {
            toggleIcon.style.display = 'none';
        } else {
            toggleIcon.style.display = 'inline-block';

        }
    }


   function togglePasswordVisibility() {
    var passwordInput = document.querySelector('.password-input');
    var toggleIcon = document.querySelector('.toggle-password i');

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    } else {
        passwordInput.type = "password";
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    }
}

function toggleFullScreen(elementId) {
  var iframe = document.getElementById(elementId);

  if (!document.fullscreenElement &&    // alternative standard method
      !document.mozFullScreenElement && 
      !document.webkitFullscreenElement && 
      !document.msFullscreenElement ) {  // current working methods
    if (iframe.requestFullscreen) {
      iframe.requestFullscreen();
    } else if (iframe.msRequestFullscreen) {
      iframe.msRequestFullscreen();
    } else if (iframe.mozRequestFullScreen) {
      iframe.mozRequestFullScreen();
    } else if (iframe.webkitRequestFullscreen) {
      iframe.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
    }
  } else {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    } else if (document.msExitFullscreen) {
      document.msExitFullscreen();
    } else if (document.mozCancelFullScreen) {
      document.mozCancelFullScreen();
    } else if (document.webkitExitFullscreen) {
      document.webkitExitFullscreen();
    }
  }
}

document.getElementById('password').addEventListener('input', function() {
    var passwordInput = document.getElementById('password').value;
    var submitButton = document.getElementById('realSubmitBtn');
    
    if (passwordInput.trim() === "") {
        submitButton.disabled = true;
    } else {
        submitButton.disabled = false;
    }
});

  // Function to prevent spaces from input fields
  function preventSpaces(event) {
    var input = $(event.target);
    var value = input.val().replace(/\s/g, "");
    input.val(value);
  }

// Disallow whitespaces from password field
$("#password").on("input", function (event) {
    preventSpaces(event);
  });

$(document).ready(function() {
    console.log('Document ready'); // Check if document ready event fires

    // Handle form submission for password verification
    $('#confirmPasswordForm').on('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        var formData = new FormData(this); // Create FormData from form
        var email = formData.get('email'); // Extract email from form data

        $.ajax({
            type: 'POST',
            url: 'includes/verify-password.php',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#confirmPassModal').modal('hide');
                    $("#emailSending").modal("show");
                    $.ajax({
                        type: 'POST',
                        url: 'includes/send-email-change.php',
                        data: { email: email }, 
                        success: function(emailResponse) {
                            $("#emailSending").modal("hide");
                            $('#approvalModal').modal('show');
                        },
                        error: function(xhr, status, error) {
                            console.error('Error sending email:', error);
                        }
                    });
                } else if (response.maxLimit) {
                    // Maximum attempts exceeded
                    $('#approvalModal').modal('hide');
                    $('#maximumAttemptsModal').modal('show');
                } else {
                    // Password verification failed
                    var attemptsLeft = response.message.split('Attempts left: ')[1];
                    $('#errorMessage').text(response.message).show();
                    $('#password').addClass('error-border');
                }
            },
            error: function(xhr, status, error) {
                console.error('Verify Password AJAX Error:', xhr.responseText); // Log verify password error
                // Handle verify password error
            }
        });
    });

    // Ajax request to destroy the session of a user
    $("#closeMaximumAttemptsModal").on("click", function () {
        $.ajax({
            url: "includes/voter-logout.php",
            type: "POST",
            success: function () {
                window.location.href = "landing-page.php";
            },
            error: function (xhr, status, error) {
                console.error("Error:", error);
                $("#error-message").text("An error occurred. Please try again later.");
            },
        });
    });

    // Optional: Handle input error removal
    $('#password').on('input', function() {
        $(this).removeClass('error-border');
        $('#errorMessage').hide();
    });

    // Handle real submit button click
    $('#realSubmitBtn').on('click', function() {
        $('#confirmPasswordForm').submit();
    });
});