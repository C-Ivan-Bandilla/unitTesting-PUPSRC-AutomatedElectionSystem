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
        var $button = $(this);
        var originalText = $button.text();

        // Change button state to loading
        setButtonLoading($button);

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
                    } else {
                        showErrorModal(result.message, result.duplicates, result.invalidIds);
                    }
                } catch (e) {
                    console.error("Error parsing response:", e);
                    showErrorModal('An unexpected error occurred during the import process.');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX error:", textStatus, errorThrown);
                showErrorModal('An error occurred while processing your request.');
            }
        });
    });

    function setButtonLoading($button) {
        $button.prop('disabled', true)
               .text('Import Voters')
               .addClass('btn-secondary')
               .removeClass('btn-main-primary');
    }

    function resetButton($button, originalText) {
        $button.text(originalText)
               .removeClass('btn-secondary')
               .addClass('btn-main-primary')
               .prop('disabled', true);  // Always disable the button
    }

    function showErrorModal(message, duplicates, invalidIds) {
        $('#dangerTitle').text('Import Failed');
        let content = `<p>${message}</p>`;
        
        if (duplicates && duplicates.length > 0) {
            content += '<h5>Duplicate Emails:</h5><ul>';
            duplicates.forEach(email => {
                content += `<li>${email}</li>`;
            });
            content += '</ul>';
        }
        
        if (invalidIds && invalidIds.length > 0) {
            content += '<h5>Invalid Student IDs:</h5><ul>';
            invalidIds.forEach(id => {
                content += `<li>${id}</li>`;
            });
            content += '</ul>';
        }
        
        $('#dangerSubtitle').html(content);
        $('#invalidContentModal').modal('show');
    }

    // Close button for modals
    $('#importDoneClose, #invalidContentClose').on('click', function() {
        $(this).closest('.modal').modal('hide');
    });

    // Ensure all modals are initialized
    $('#importDoneModal, #invalidContentModal').modal({
        backdrop: 'static',
        keyboard: false,
        show: false
    });

    // Disable button when any modal is shown
    $('.modal').on('show.bs.modal', function () {
        $('#import-voters').prop('disabled', true);
    });

    // Reset button state when any modal is closed
    $('.modal').on('hidden.bs.modal', function () {
        resetButton($('#import-voters'), 'Import Voters');
        $('#voters-list').val(''); // Clear the file input
        $('#import-voters').prop('disabled', true); // Ensure the button is disabled
    });
});