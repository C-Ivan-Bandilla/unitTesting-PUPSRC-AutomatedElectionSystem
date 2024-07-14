$(document).ready(function () {
  
  // Function to toggle password visibility
  $("#reset-password-toggle-2").click(function () {
    togglePasswordVisibility("#password_confirmation", $(this));
  });

  function togglePasswordVisibility(inputSelector, toggleElement) {
    var passwordInput = $(inputSelector);
    var eyeIcon = toggleElement.find("i");

    var type = passwordInput.attr("type") === "password" ? "text" : "password";
    passwordInput.attr("type", type);

    eyeIcon.toggleClass("fa-eye-slash fa-eye");
  }
  
  // Function to prevent spaces from input fields
  function preventSpaces(event) {
    var input = $(event.target);
    var value = input.val().replace(/\s/g, "");
    input.val(value);
  }

  // Disallow whitespaces from email and password confirmation fields
  $("#password, #password_confirmation").on("input", function (event) {
    preventSpaces(event);
  });

  $(document).ready(function() {
    // Function to check if the input fields are empty
    function checkInputFields() {
      var email = $('#password').val().trim();
      var password = $('#password_confirmation').val().trim();

      if (email === "" || password === "") {
          $('#submit-new-email').attr('disabled', 'disabled');
      } else {
          $('#submit-new-email').removeAttr('disabled');
      }
  }

  // Monitor input fields
  $('#password, #password_confirmation').on('input', function() {
      checkInputFields();
  });

  // Initial check
  checkInputFields();

  $('#update-email-form').on('submit', function(e) {
    e.preventDefault(); // Prevent the default form submission

        $.ajax({
            url: 'includes/process-setting-email.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.status === 'success') {
                    $('#successEmailModal').modal('show');
                } else if (response.status === 'error') {
                    if (response.error === 'email_in_use') {
                        $('#emailError').text('Email address is already in use. Please choose another one.').show();
                        $('#password').css('border', '1px solid red'); // Add red border
                        $('#reset-password-toggle-1').css({
                            'border-top': '1px solid red',
                            'border-right': '1px solid red',
                            'border-bottom': '1px solid red'
                        });
                    } else if (response.error === 'incorrect_password') {
                        $('#passwordError').text('Incorrect password. Please try again.').show();
                        $('#password_confirmation').css('border', '1px solid red');
                        $('#reset-password-toggle-2').css({
                            'border-top': '1px solid red',
                            'border-right': '1px solid red',
                            'border-bottom': '1px solid red'
                        });
                    } else {
                        alert('Error: ' + response.message);
                    }
                } else {
                    alert('Unexpected error. Please try again.');
                }
            },
            error: function(xhr, status, error) {
                alert('An error occurred while processing your request: ' + error);
            }
        });
    });

    
// Clear error messages and borders when typing
$('#password').on('input', function() {
    $('#emailError').text('').hide();
    $('#password').css('border', ''); // Remove red border
    $('#reset-password-toggle-1').css({
        'border-top': '',
        'border-right': '',
        'border-bottom': ''
    }); // Remove red border from top, right, and bottom
});

$('#password_confirmation').on('input', function() {
    $('#passwordError').text('').hide();
    $('#password_confirmation').css('border', ''); // Remove red border
    $('#reset-password-toggle-2').css({
        'border-top': '',
        'border-right': '',
        'border-bottom': ''
    }); // Remove red border from top, right, and bottom
});
});
 
});
