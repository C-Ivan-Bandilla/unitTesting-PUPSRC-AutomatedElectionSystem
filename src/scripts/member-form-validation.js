
// Get form elements
const adminForm = document.getElementById("admin-form");
const firstNameInput = document.getElementById("first_name");
const middleNameInput = document.getElementById("middle_name");
const lastNameInput = document.getElementById("last_name");
const emailInputField = document.getElementById("email");
const emailErrorField = document.getElementById("email_error");
const roleSelect = document.getElementById("role");
const roleError = document.getElementById("role_error");
const suffixInput = document.getElementById("suffix");
const suffixError = document.getElementById("suffix_error");

// Email validation patterns
const generalEmailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
const specialEmail = "@iskolarngbayan.pup.edu.ph";

// Email validation function
function validateEmail() {
    const emailValue = emailInputField.value.trim().replace(/\s/g, '');
    const emailExistsError = document.getElementById("email_exists_error");

    // Set the value back to the input field without spaces
    emailInputField.value = emailValue;

    if (emailValue !== "") {
        const atIndex = emailValue.indexOf('@');
        if (atIndex !== -1) {
            const domain = emailValue.slice(atIndex);
            if (domain === specialEmail) {
                // Check if the local part (before @) is valid
                const localPart = emailValue.slice(0, atIndex);
                if (!/^[a-zA-Z0-9._%+-]+$/.test(localPart)) {
                    emailErrorField.textContent = "Invalid characters before @iskolarngbayan.pup.edu.ph";
                    emailErrorField.style.color = "red";
                    emailInputField.style.borderColor = "red";
                } else {
                    emailErrorField.textContent = "";
                    emailInputField.style.borderColor = "";
                }
            } else if (domain.includes("iskolarngbayan") || domain.includes("pup.edu.ph")) {
                emailErrorField.textContent = "For PUP email, use the exact format: username@iskolarngbayan.pup.edu.ph";
                emailErrorField.style.color = "red";
                emailInputField.style.borderColor = "red";
            } else if (!generalEmailPattern.test(emailValue)) {
                emailErrorField.textContent = "Please enter a valid email address.";
                emailErrorField.style.color = "red";
                emailInputField.style.borderColor = "red";
            } else {
                emailErrorField.textContent = "";
                emailInputField.style.borderColor = "";
            }
        } else {
            emailErrorField.textContent = "Please enter a valid email address.";
            emailErrorField.style.color = "red";
            emailInputField.style.borderColor = "red";
        }
    } else {
        emailErrorField.textContent = "";
        emailInputField.style.borderColor = "";
    }

    // Clear the email exists error when the user starts typing
    emailExistsError.textContent = "";
}

// Add event listeners
emailInputField.addEventListener("input", validateEmail);
emailInputField.addEventListener("input", function() {
    this.value = this.value.replace(/\s/g, '');
});

// Form submission
adminForm.addEventListener("submit", function (event) {
    validateEmail();
    if (emailErrorField.textContent) {
        event.preventDefault();
    }
});

// First Name validation
function validateFirstName() {
    const firstNameInput = document.getElementById("first_name");
    const firstNameError = document.getElementById("first_name_error");
    const namePattern = /^[a-zA-Z]+(?:\s[a-zA-Z]+)*$/;

    if (firstNameInput.value.trim() !== "" && !namePattern.test(firstNameInput.value)) {
        firstNameError.textContent = "Please enter a valid name.";
        firstNameError.style.color = "red";
        firstNameInput.style.borderColor = "red";
    } else {
        firstNameError.textContent = "";
        firstNameInput.style.borderColor = "";
    }
}
firstNameInput.addEventListener("input", validateFirstName);

// Middle Name validation
function validateMiddleName() {
    const middleNameInput = document.getElementById("middle_name");
    const middleNameError = document.getElementById("middle_name_error");
    const namePattern = /^[a-zA-Z]+(?:\s[a-zA-Z]+)*$/;

    if (middleNameInput.value.trim() !== "" && !namePattern.test(middleNameInput.value)) {
        middleNameError.textContent = "Please enter a valid name.";
        middleNameError.style.color = "red";
        middleNameInput.style.borderColor = "red";
    } else {
        middleNameError.textContent = "";
        middleNameInput.style.borderColor = "";
    }
}
middleNameInput.addEventListener("input", validateMiddleName);

// Suffix validation
function validateSuffix() {
    const suffixInput = document.getElementById('suffix');
    const suffixError = document.getElementById('suffix_error');

    if (suffixInput.value.trim() !== "" && (!suffixInput.value.match(/^[a-zA-Z]+$/) || suffixInput.value.length > 3)) {
        suffixError.textContent = 'Please enter a valid suffix.';
        suffixError.style.color = 'red';
        suffixInput.style.borderColor = 'red';
    } else {
        suffixError.textContent = '';
        suffixInput.style.borderColor = '';
    }
}
suffixInput.addEventListener("input", validateSuffix);

// Last Name validation
function validateLastName() {
    const lastNameInput = document.getElementById("last_name");
    const lastNameError = document.getElementById("last_name_error");
    const namePattern = /^[a-zA-Z]+(?:\s[a-zA-Z]+)*$/;

    if (lastNameInput.value.trim() !== "" && !namePattern.test(lastNameInput.value)) {
        lastNameError.textContent = "Please enter a valid name.";
        lastNameError.style.color = "red";
        lastNameInput.style.borderColor = "red";
    } else {
        lastNameError.textContent = "";
        lastNameInput.style.borderColor = "";
    }
}
lastNameInput.addEventListener("input", validateLastName);

// Role validation
let roleInteracted = false;
function validateRole() {
    const roleSelect = document.getElementById("role");
    const roleError = document.getElementById("role_error");

    if (roleError && roleInteracted) {
        if (roleSelect.value === "") {
            roleError.textContent = "Please select a role.";
            roleError.style.color = "red";
            roleSelect.style.borderColor = "red";
        } else {
            roleError.textContent = "";
            roleSelect.style.borderColor = "";
        }
    }
}
roleSelect.addEventListener("change", function() {
    roleInteracted = true;
    validateRole();
});
roleSelect.addEventListener("change", validateRole);

//---------CREATE BUTTON DISABLE/ABLE--------------//

// Get the submit button
const submitButton = document.querySelector('.button-create');

// Initially disable the submit button and add the 'button-disabled' class
submitButton.disabled = true;
submitButton.classList.add('button-disabled');

// Function to validate all fields
function validateAllFields() {
    validateFirstName();
    validateLastName();
    validateEmail();
    validateRole();
    validateMiddleName();
    validateSuffix();
}

// Function to check if all required fields are valid
function areAllFieldsValid() {
    const firstNameValid = document.getElementById("first_name_error").textContent === "";
    const lastNameValid = document.getElementById("last_name_error").textContent === "";
    const emailValid = document.getElementById("email_error").textContent === "";
    const roleValid = document.getElementById("role_error").textContent === "";
    const middleNameValid = document.getElementById("middle_name_error").textContent === "";
    const suffixValid = document.getElementById("suffix_error").textContent === "";

    return firstNameValid && lastNameValid && emailValid && roleValid && middleNameValid && suffixValid;
}

// Function to toggle submit button state
function toggleSubmitButton() {
    validateAllFields(); // Run all validations

    const isFormValid = 
        firstNameInput.value.trim().length > 0 &&
        lastNameInput.value.trim().length > 0 &&
        emailInputField.value.trim().length > 0 &&
        (roleSelect.value === 'head_admin' || roleSelect.value === 'admin') &&
        areAllFieldsValid(); // Check if all fields are valid

    submitButton.disabled = !isFormValid;

    if (isFormValid) {
        submitButton.classList.remove('button-disabled');
    } else {
        submitButton.classList.add('button-disabled');
    }
}

// Add event listeners to input fields
firstNameInput.addEventListener("input", function() {
    validateFirstName();
    toggleSubmitButton();
});

middleNameInput.addEventListener("input", function() {
    validateMiddleName();
    toggleSubmitButton();
});

lastNameInput.addEventListener("input", function() {
    validateLastName();
    toggleSubmitButton();
});

emailInputField.addEventListener("input", function() {
    validateEmail();
    toggleSubmitButton();
});

roleSelect.addEventListener("change", function() {
    roleInteracted = true;
    validateRole();
    toggleSubmitButton();
});

suffixInput.addEventListener("input", function() {
    validateSuffix();
    toggleSubmitButton();
});

adminForm.addEventListener("submit", function (event) {
    validateAllFields();
    if (!areAllFieldsValid()) {
        event.preventDefault();
    }
});

// Initial call to set button state
toggleSubmitButton();

// Add CSS class for disabled button state
const styleElem = document.head.appendChild(document.createElement("style"));
styleElem.innerHTML = `
    .button-disabled {
        background-color: gray !important;
        cursor: not-allowed !important;
    }
`;

document.addEventListener('DOMContentLoaded', function () {
    var createdModal = new bootstrap.Modal(document.getElementById('createdModal'));
    var modalContent = document.querySelector('#createdModal .modal-content');

    modalContent.addEventListener('click', function (event) {
        if (event.target.classList.contains('close-mark')) {
            createdModal.hide();
        }
    });
});


