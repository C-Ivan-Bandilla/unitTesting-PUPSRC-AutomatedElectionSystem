// Global variables
let currentPage = 1;
let currentSortBy = '';
let currentSortOrder = '';
let selectedAdminIds = [];
let deleteAdminState = false;
let currentFilter = [];

function formatRoleString(roleString) {
  const words = roleString.split('_');
  const capitalizedWords = words.map(word => word.charAt(0).toUpperCase() + word.slice(1));
  return capitalizedWords.join(' ');
}

function formatDate(dateString) {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('en-US', options);
}

function applyFilters() {
  const searchTerm = $("#searchInput").val();
  const filterForm = $("#filterForm");
  console.log("Filter form:", filterForm);
  console.log("Serialized form data:", filterForm.serialize());

  currentFilter = filterForm.serializeArray()
    .filter(item => item.name === "filter[]")
    .map(item => item.value);

  console.log("Filter values:", currentFilter);

  loadPage(
    "adminTable",
    "committeePagination",
    searchTerm !== "" ? "submission_handlers/search-committee.php" : "submission_handlers/fetch-committee.php",
    currentPage,
    searchTerm,
    currentSortBy,
    currentSortOrder,
    currentFilter
  );
}

function loadPage(tableId, paginationId, ajaxUrl, page, searchTerm = "", sortBy = currentSortBy, sortOrder = currentSortOrder, filter = currentFilter) {
  const requestData = {
    page: page,
    search: searchTerm,
    sort_by: sortBy,
    sort_order: sortOrder,
    filter: filter
  };

  console.log("AJAX request data:", requestData);

  $.ajax({
    url: ajaxUrl,
    type: "GET",
    data: requestData,
    success: function (response) {
      console.log("Server response:", response);
      const data = JSON.parse(response);
      const voters = data.voters;
      const totalRows = data.totalRows;
      const totalPages = Math.max(1, Math.ceil(totalRows / 5));

      console.log(`Total rows: ${totalRows}, Total pages: ${totalPages}`);

      const tbody = $(`#${tableId} tbody`);
      tbody.empty();

      if (voters.length === 0) {
        tbody.append(`
          <tr>
            <td colspan="4">
              <div class="pt-4 col-md-12 no-registration text-center">
                <img src="images/resc/not-found.png" class="not-found-illus">
                <p class="fw-bold spacing-6 black">No records found</p>
                <p class="spacing-3 pt-1 black">Maybe try a different keyword?</p>
              </div>
            </td>
          </tr>
        `);
      } else {
        voters.forEach((voter) => {
          const formattedDate = formatDate(voter.acc_created);
          const isChecked = selectedAdminIds.includes(voter.voter_id) ? "checked" : "";
          const fullName = `${voter.first_name} ${voter.middle_name} ${voter.last_name} ${voter.suffix}`;
      
          const row = `
              <tr>
                  <td class="col-md-3 text-center checkbox-delete-admin ${deleteAdminState ? "" : "d-none"}">
                      <input type="checkbox" class="adminCheckbox" data-id="${voter.voter_id}" ${isChecked}>
                  </td>
                  <td class="col-md-4 text-center">
                      <a href="account-details.php?voter_id=${voter.voter_id}" 
                         class="truncate-name" 
                         title="${fullName}">
                          ${fullName}
                      </a>
                  </td>
                  <td class="col-md-3 text-center">
                      <span class="role-background ${voter.role.toLowerCase()} ${voter.role === 'head_admin' ? 'head-admin' : ''}">${formatRoleString(voter.role)}</span>
                  </td>
                  <td class="col-md-3 text-center">
                      <span>${formattedDate}</span>
                  </td>
              </tr>
          `;
      
          tbody.append(row);
        });
      }

      if (tableId === "adminTable") {
        selectedAdminIds.forEach((id) => {
          $(`.adminCheckbox[data-id="${id}"]`).prop("checked", true);
        });
      }

      generatePagination(paginationId, totalPages, page, tableId, ajaxUrl, searchTerm);
      currentPage = page;
      currentSortBy = sortBy;
      currentSortOrder = sortOrder;
      currentFilter = filter;
    },
    error: function (error) {
      console.error(`Error loading page ${page} for ${tableId} with search term: "${searchTerm}", sortBy: "${sortBy}", sortOrder: "${sortOrder}", filter: ${JSON.stringify(filter)}`, error);
    },
  });
}

function generatePagination(paginationId, totalPages, currentPage, tableId, ajaxUrl, searchTerm = "") {
  console.log(`Generating pagination for ${tableId} (Page ${currentPage} of ${totalPages})`);

  const pagination = $(`#${paginationId}`);
  pagination.empty();

  totalPages = Math.max(1, totalPages);
  currentPage = Math.min(Math.max(1, currentPage), totalPages);

  const prevArrowClass = currentPage === 1 ? "disabled-arrow" : "";
  const pointerPrevNone = currentPage === 1 ? "pe-none" : "";
  const prevPage = currentPage > 1 ? currentPage - 1 : 1;
  const prevItem = `
         <li class="page-item ${pointerPrevNone}" id="left-arrow-page">
             <a href="#" class="page-link" data-page="${prevPage}">
                 <span class="fas fa-chevron-left left-arrow-icon ${prevArrowClass}"></span>
             </a>
         </li>`;
  pagination.append(prevItem);

  for (let i = 1; i <= totalPages; i++) {
    const activeClass = i === currentPage ? "active" : "";
    const pageItem = `
             <li class="page-item ${activeClass}">
                 <a href="#" class="page-link" data-page="${i}">${i}</a>
             </li>`;
    pagination.append(pageItem);
  }

  const nextArrowClass = currentPage === totalPages ? "disabled-arrow" : "";
  const pointerNextNone = currentPage === totalPages ? "pe-none" : "";
  const nextPage = currentPage < totalPages ? currentPage + 1 : totalPages;
  const nextItem = `
         <li class="page-item ${pointerNextNone}" id="right-arrow-page">
             <a href="#" class="page-link" data-page="${nextPage}">
                 <span class="fas fa-chevron-right ${nextArrowClass}"></span>
             </a>
         </li>`;
  pagination.append(nextItem);

  $(`#${paginationId} .page-link`)
    .off("click")
    .on("click", function (e) {
      e.preventDefault();
      if (!$(this).parent().hasClass('disabled')) {
        const page = $(this).data("page");
        console.log(`Page link clicked: ${page}`);
        loadPage(tableId, paginationId, ajaxUrl, page, searchTerm, currentSortBy, currentSortOrder, currentFilter);
      }
    });
}

$(document).ready(function () {
  // Load initial data
  loadPage('adminTable', 'committeePagination', 'submission_handlers/fetch-committee.php', currentPage);

  // Search event listener
  $("#searchInput").on("input", function() {
    currentPage = 1; // Reset to first page on new search
    applyFilters();
  });

  // Filter checkbox event listener
  $("#filterForm input[type='checkbox']").on("change", function () {
    console.log("Checkbox changed. Checkbox value:", $(this).val(), "Checked:", $(this).prop('checked'));
    currentPage = 1; // Reset to first page on filter change
    applyFilters();
  });

  // Sort event listener
  $('.sort-link').on('click', function (e) {
    e.preventDefault();
    currentSortBy = $(this).data('sort');
    currentSortOrder = $(this).data('order');
    applyFilters();
  });

  // Delete functionality
  $(".admin-delete-btn").click(function () {
    deleteAdminState = true;
    toggleDeleteState('admin');
  });

  $(".cancel-admin").click(function () {
    deleteAdminState = false;
    cancelDelete('admin');
  });

  $("#selectAllAdmin").click(function () {
    $(".adminCheckbox").prop("checked", this.checked);
    checkAdminCheckboxes();
  });

  $("#adminTable").on("change", ".adminCheckbox", function () {
    checkAdminCheckboxes();
  });

  $("#deleteSelectedAdmin").click(function () {
    const selectedIds = getSelectedIds("adminTable");

    if (selectedIds.length > 0) {
      showModalAndConfirmDeletion("adminTable", selectedIds);
    } else {
      alert("Please select at least one record to delete.");
    }
  });
});

function toggleDeleteState(tableType) {
  if (tableType === 'admin') {
    checkAdminCheckboxes();
  }

  var checkBoxDeleteClass = ".checkbox-delete-admin";
  var checkBoxAllClass = ".checkbox-all-admin";
  var tableId = "#adminTable";
  var cancelBtnClass = ".cancel-admin";
  var finalDeleteBtnClass = ".final-delete-btn-admin";

  $(`${checkBoxDeleteClass}, ${checkBoxAllClass}`).removeClass("d-none");
  $(`${tableId} th.checkbox-${tableType}`).removeClass("d-none").addClass("tl-left");
  $(`${tableId} th.del-center`).removeClass("tl-left");
  $(`${finalDeleteBtnClass}`).toggleClass("d-none");
  $(`${cancelBtnClass}`).toggleClass("d-none");

  $(`.${tableType}-delete-btn`).prop("disabled", true).addClass("light-gray");
  $(`${cancelBtnClass}`).prop("disabled", false);
}

function cancelDelete(tableType) {
  var checkBoxDeleteClass = ".checkbox-delete-admin";
  var checkBoxAllClass = ".checkbox-all-admin";
  var tableId = "#adminTable";
  var cancelBtnClass = ".cancel-admin";
  var finalDeleteBtnClass = ".final-delete-btn-admin";

  $(`${checkBoxDeleteClass}, ${checkBoxAllClass}`).addClass("d-none");
  $(`${tableId} th.checkbox-${tableType}`).addClass("d-none");
  $(`${tableId} th.del-center`).addClass("tl-left");
  $(`${finalDeleteBtnClass}`).toggleClass("d-none");
  $(`${cancelBtnClass}`).toggleClass("d-none");

  $(`${cancelBtnClass}`).prop("disabled", true);
  $(`.${tableType}-delete-btn`).prop("disabled", false).removeClass("light-gray");

  $(`${tableId} .${tableType}Checkbox`).prop("checked", false);
  $(`#selectAllAdmin`).prop("checked", false);

  selectedAdminIds = [];
  applyFilters();
}

function checkAdminCheckboxes() {
  const isAnyChecked = $(".adminCheckbox:checked").length > 0;
  const areAnyAvailable = $(".adminCheckbox").length > 0;
  console.log("Admin checkboxes checked:", isAnyChecked);
  console.log("Admin checkboxes available:", areAnyAvailable);
  $("#deleteSelectedAdmin").prop(
    "disabled",
    !isAnyChecked || !areAnyAvailable
  );

  selectedAdminIds = $(".adminCheckbox:checked")
    .map(function () {
      return $(this).data("id");
    })
    .get();
}

function getSelectedIds(tableId) {
  const selectedIds = [];
  $(`#${tableId} input[type=checkbox]:checked`).each(function () {
    selectedIds.push($(this).data("id"));
  });
  return selectedIds;
}

function showModalAndConfirmDeletion(tableId, selectedIds) {
  $("#rejectModal").modal("show");
  $("#confirm-move")
    .off("click")
    .on("click", function (e) {
      e.preventDefault();
      performDeletion(tableId, selectedIds);
    });
}

function performDeletion(tableId, selectedIds) {
  $.ajax({
    url: "submission_handlers/move-trashbin-accs.php",
    type: "POST",
    data: { ids: selectedIds },
    success: function (response) {
      const result = JSON.parse(response);
      if (result.success) {
        handleSuccessfulDeletion(tableId);
        applyFilters();
      } else {
        alert(result.message);
      }
    },
    error: function (error) {
      console.error("Error deleting records:", error);
    },
  });
}

function handleSuccessfulDeletion(tableId) {
  $("#rejectModal").modal("hide");
  $("#trashbinMoveDone").modal("show");
  updateAdminTableState();
}

function updateAdminTableState() {
  deleteAdminState = false;
  $(".cancel-admin").addClass("d-none");
  $(".checkbox-delete-admin").addClass("d-none");
  $(".final-delete-btn-admin").toggleClass("d-none");
  $("#adminTable th.del-center").addClass("tl-left");
  $("#selectAllAdmin").closest("th").addClass("d-none");
  $(".admin-delete-btn").prop("disabled", false).removeClass("light-gray");
}

function redirectToPage(url) {
  window.location.href = url;
}