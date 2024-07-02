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
  $("#email, #password_confirmation").on("input", function (event) {
    preventSpaces(event);
  });

 
});
