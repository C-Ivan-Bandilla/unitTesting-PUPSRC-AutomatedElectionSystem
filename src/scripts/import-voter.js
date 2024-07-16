$(document).ready(function() {
    const maxFileSize = 25 * 1024 * 1024; // 25MB in bytes

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
                $('l').modal('show');
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
        $('#onlyPDFAllowedModal').modal('show');
    }

    // Reload page when modals are closed
    $('#importDoneModal, #duplicatesModal, #onlyPDFAllowedModal').on('hidden.bs.modal', function () {
        location.reload();
    });

    // Close button for modals
    $('#importDoneClose').on('click', function() {
        $('#importDoneModal').modal('hide');
    });

    $('#duplicatesClose').on('click', function() {
        $('#duplicatesModal').modal('hide');
    });

    $('#onlyPDFAllowedModal').on('click', function() {
        $('#duplicatesModal').modal('hide');
    });

    // Ensure all modals are initialized
    $('#importDoneModal, #duplicatesModal, #onlyPDFAllowedModal').modal({
        backdrop: 'static',
        keyboard: false,
        show: false
    });
});