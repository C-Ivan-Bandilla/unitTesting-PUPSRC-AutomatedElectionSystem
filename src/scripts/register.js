// Peter Wilrexe's Vanilla JS to Carlito's jQuery
// ANG POGI MO TALAGA DOMINIC

$(document).ready(function () {
  const fields = {
    email: { touched: false },
    org: { touched: false },
    password: { touched: false },
    retypePass: { touched: false },
    cor: { touched: false },
  };

  function preventSpaces(event) {
    let input = event.target;
    let value = $(input).val();
    value = value.replace(/\s/g, "");
    $(input).val(value);
  }

  function closeModal() {
    $("#onlyPDFAllowedModal").modal("hide");
  }

  $("#onlyPDFClose").click(function () {
    closeModal();
  });

  // Check for valid email and if one already exists in the voter table
  function validateEmail(input, showErrorMessages = false) {
    let emailValue = input.val().trim();
    const errorElement = input.next();

    // Truncate email if exceeds 255 characters
    if (emailValue.length > 255) {
      emailValue = emailValue.slice(0, 255);
      input.val(emailValue);
    }

    const isValidFormat = validateEmailFormat(emailValue);
    const isExistingEmail = emails.includes(emailValue);

    if (!isValidFormat) {
      if (showErrorMessages && fields.email.touched)
        showError(input, errorElement, "Please enter a valid email address.");
      return false;
    } else if (isExistingEmail) {
      if (showErrorMessages && fields.email.touched)
        showError(input, errorElement, "Email address already exists.");
      return false;
    } else {
      clearError(input, errorElement);
      return true;
    }
  }

  // Basic email format validation
  function validateEmailFormat(email) {
    const allowedDomains = ["@gmail.com", "@iskolarngbayan.pup.edu.ph"];
    const domainMatch = allowedDomains.some((domain) => email.endsWith(domain));
    return domainMatch && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }

  function validateOrg(select, showErrorMessages = false) {
    const orgValue = select.val();
    const errorElement = select.next();

    if (orgValue === "") {
      if (showErrorMessages && fields.org.touched)
        showError(select, errorElement, "Please select an organization.");
      return false;
    } else {
      clearError(select, errorElement);
      return true;
    }
  }

  function validatePassword(input, showErrorMessages = false) {
    let passwordValue = input.val();
    const errorElement = input.next();

    // Truncate password if exceeds 20 characters
    if (passwordValue.length > 20) {
      passwordValue = passwordValue.slice(0, 20);
      input.val(passwordValue);
    }

    const passwordRegex =
      /^(?=.*[a-zA-Z])(?=.*\d)(?=.*[^a-zA-Z0-9\s])[^\s]{8,20}$/;

    if (!passwordRegex.test(passwordValue)) {
      if (showErrorMessages && fields.password.touched)
        showError(
          input,
          errorElement,
          "Password must be 8-20 characters long with letters, numbers, and symbols."
        );
      return false;
    } else {
      clearError(input, errorElement);
      return true;
    }
  }

  function validateRetypePassword(
    input,
    originalPasswordInput,
    showErrorMessages = false
  ) {
    let retypePassValue = input.val();
    const errorElement = input.next();

    // Truncate retype password if exceeds 20 characters
    if (retypePassValue.length > 20) {
      retypePassValue = retypePassValue.slice(0, 20);
      input.val(retypePassValue);
    }

    if (retypePassValue !== originalPasswordInput.val()) {
      if (showErrorMessages && fields.retypePass.touched)
        showError(input, errorElement, "Passwords do not match.");
      return false;
    } else {
      clearError(input, errorElement);
      return true;
    }
  }

  function validateCOR(showErrorMessages = false) {
    const file = $("#cor").prop("files")[0];
    const errorElement = $("#cor").next();

    if (!file) {
      if (showErrorMessages && fields.cor.touched)
        showError(
          $("#cor"),
          errorElement,
          "Please upload your Certificate of Registration."
        );
      return false;
    }

    const fileName = file.name;
    const fileExtension = fileName.split(".").pop().toLowerCase();

    // Check if file is PDF extension
    if (fileExtension !== "pdf") {
      $("#cor").val("");
      showModal("onlyPDFAllowedModal");
      return false;
    }

    // Check if file size exceeds 25mb
    const fileSizeInMB = file.size / (1024 * 1024);
    if (fileSizeInMB > 25) {
      $("#cor").val("");
      showModal("onlyPDFAllowedModal");
      return false;
    }

    clearError($("#cor"), errorElement);
    return true;
  }

  // Display error message below an input field
  function showError(input, errorElement, message) {
    if (!errorElement.length) {
      const newErrorElement = $("<div>").addClass("error-message");
      input.parent().append(newErrorElement);
      errorElement = newErrorElement;
    }
    errorElement.text(message);
    input.addClass("error-border");
  }

  // Remove error messages below an input field
  function clearError(input, errorElement) {
    if (errorElement.length) {
      errorElement.text("");
      input.removeClass("error-border");
    }
  }

  // Get the modal id to know which modal to show
  function showModal(modalId) {
    $("#" + modalId).modal("show");
  }

  // All input fields validation
  function checkFormValidity() {
    const emailValid = validateEmail($("#email"), false);
    const orgValid = validateOrg($("#org"), false);
    const passwordValid = validatePassword($("#password"), false);
    const retypePassValid = validateRetypePassword(
      $("#retype-pass"),
      $("#password"),
      false
    );
    const corValid = validateCOR(false);

    const submitButton = $("#sign-up");

    if (
      emailValid &&
      orgValid &&
      passwordValid &&
      retypePassValid &&
      corValid
    ) {
      submitButton.removeAttr("disabled");
    } else {
      submitButton.attr("disabled", "disabled");
    }
  }

  // Event listeners to prevent whitespaces haha
  $("#email, #password, #retype-pass").on("input", function (event) {
    preventSpaces(event);
    checkFormValidity();
  });

  // Event listeners for all input fields validity
  $("#email").on("change", function () {
    fields.email.touched = true;
    validateEmail($(this), true);
    checkFormValidity();
  });

  $("#org").on("change", function () {
    fields.org.touched = true;
    validateOrg($(this), true);
    checkFormValidity();
  });

  $("#password").on("input", function () {
    fields.password.touched = true;
    validatePassword($(this), true);
    checkFormValidity();
  });

  $("#retype-pass").on("input", function () {
    fields.retypePass.touched = true;
    validateRetypePassword($(this), $("#password"), true);
    checkFormValidity();
  });

  $("#cor").on("change", function () {
    fields.cor.touched = true;
    validateCOR(true);
    checkFormValidity();
  });

  $("form").on("submit", function (event) {
    checkFormValidity(true);
    if ($(this).find(".error-border").length) {
      event.preventDefault();
    }
  });

  // Show success modal if registration is successful
  if (registrationSuccess) {
    showModal("registerSuccessModal");
  }
});
