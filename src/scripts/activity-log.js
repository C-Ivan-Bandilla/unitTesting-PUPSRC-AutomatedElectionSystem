$(document).ready(function () {
  // Bootstrap Popovers
  const popoverTriggerList = document.querySelectorAll(
    '[data-bs-toggle="popover"]'
  );
  const popoverList = [...popoverTriggerList].map(
    (popoverTriggerEl) => new bootstrap.Popover(popoverTriggerEl)
  );

  /* Scroll to top event listener  */

  let scrollTopPageBtn = $("#scrollTopBtn");
  let canShowScrollTop = true;

  $(window).scroll(function () {
    let scrollTop = $(window).scrollTop();

    if (scrollTop > 0 && canShowScrollTop) {
      scrollTopPageBtn.addClass("show");
    } else {
      scrollTopPageBtn.removeClass("show");
    }

    // if (
    //   $(window).scrollTop() + $(window).height() >=
    //   $(document).height() - 600
    // ) {
    //   loadMoreLogs();
    // }
  });

  scrollTopPageBtn.click(function () {
    canShowScrollTop = false;
    scrollTopPageBtn.prop("disabled", true).removeClass("show");

    $("html, body").animate({ scrollTop: 0 }, "slow", function () {
      canShowScrollTop = true;
      scrollTopPageBtn.prop("disabled", false);
    });

    return false;
  });

  /* Load data on the page */
  // let page = 1;
  // let loading = false;

  // function loadMoreLogs() {
  //   if (loading) return;
  //   loading = true;
  //   $.ajax({
  //     url: "includes/load-more-logs.php",
  //     method: "GET",
  //     data: { page: page },
  //     success: function (response) {
  //       var $newLogs = $(response).hide();
  //       $(".timeline").append($newLogs);
  //       $newLogs.fadeIn("slow");
  //       page++;
  //       loading = false;
  //     },
  //     error: function (xhr, status, error) {
  //       console.error("Error loading more logs:", error);
  //       loading = false;
  //     },
  //   });
  // }
  function loadActivities(filter) {
    $.ajax({
      url: "includes/filter-logs.php",
      method: "POST",
      data: { filter: filter },
      success: function (response) {
        $(".activity-log-content").html(response);
      },
      error: function (xhr, status, error) {
        console.error(error);
      },
    });
  }

  // Event listener for dropdown item clicks
  $(".custom-dropdown-item").on("click", function (e) {
    e.preventDefault();
    var filter = $(this).attr("id");
    var filterText = $(this).text();

    loadActivities(filter);

    $(".activity-dropdown").text(
      filterText + ' <i data-feather="chevron-down" class="chevron"></i>'
    );

    feather.replace();
  });
});
