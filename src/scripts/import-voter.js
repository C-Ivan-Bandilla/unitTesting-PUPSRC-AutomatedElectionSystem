$(document).ready(function() {
    const maxFileSize = 25 * 1024 * 1024; // 25MB in bytes

    // Disable the Import Voters button initially
    $('#import-voters').prop('disabled', true);

    $('#voters-list').on('change', function() {
        if (this.files.length > 0) {
            if (this.files[0].size > maxFileSize) {
                console.log("File size exceeds 25MB limit. File rejected.");
                $(this).val(''); // Clear the file input
                $('#import-voters').prop('disabled', true);
            } else {
                $('#import-voters').prop('disabled', false);
            }
        } else {
            $('#import-voters').prop('disabled', true);
        }
    });

    $('#import-voters').on('click', function() {
        var formData = new FormData();
        formData.append('file', $('#voters-list')[0].files[0]);

        $.ajax({
            url: 'submission_handlers/import.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log("Import response:", response);
                try {
                    var result = JSON.parse(response);
                    if (result.status === 'success') {
                        $('#importDoneModal').modal('show');
                    } else if (result.status === 'warning') {
                        showDuplicatesModal(result.message, result.duplicates);
                    } else if (result.status === 'error') {
                        showInvalidContentModal(result.message);
                    } else {
                        showWrongFormatModal(result.message);
                    }
                } catch (e) {
                    console.error("Error parsing response:", e);
                    showWrongFormatModal('An unexpected error occurred during the import process.');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX error:", textStatus, errorThrown);
                showWrongFormatModal('An error occurred while processing your request.');
            }
        });
    });

    function showDuplicatesModal(message, duplicates) {
        $('#duplicatesTitle').text('Duplicate Students Found!');
        $('#duplicatesSubtitle').text(message);
        
        let duplicatesList = '<ul>';
        duplicates.forEach(email => {
            duplicatesList += `<li>${email}</li>`;
        });
        duplicatesList += '</ul>';
        
        $('#duplicatesList').html(duplicatesList);
        $('#duplicatesModal').modal('show');
    }

    function showWrongFormatModal(message) {
        $('#onlyPDFAllowedTitle').text('Invalid file format!');
        $('#onlyPDFAllowedSubtitle').text(message);
        $('#onlyPDFAllowedModal').modal('show');
    }

    function showInvalidContentModal(message) {
        $('#dangerTitle').text('Invalid Content!');
        $('#dangerSubtitle').text(message || 'The file content is invalid. Please ensure that: The file headers are correct and in the right order, All required fields are filled, Data formats are correct (e.g., valid email addresses). Please check your file and try again.');
        $('#invalidContentModal').modal('show');
    }

    // Reload page when modals are closed
    $('#importDoneModal, #duplicatesModal, #onlyPDFAllowedModal, #invalidContentModal').on('hidden.bs.modal', function () {
        location.reload();
    });

    // Close button for modals
    $('#importDoneClose, #duplicatesClose, #onlyPDFClose, #invalidContentClose').on('click', function() {
        $(this).closest('.modal').modal('hide');
    });

    // Ensure all modals are initialized
    $('#importDoneModal, #duplicatesModal, #onlyPDFAllowedModal, #invalidContentModal').modal({
        backdrop: 'static',
        keyboard: false,
        show: false
    });
});