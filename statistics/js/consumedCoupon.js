(function() {

  $(function() {
    var couponId, couponRowWrapNums, get_pageUrl, init, nextPage, orderId, page, platform, prevPage, tableRowNums, teamId, totalCouponNums, url, username;
    url = 'consumedCoupons.php';
    tableRowNums = 6;
    page = parseInt($("#page").val());
    totalCouponNums = parseInt($("#totalCouponNums").val());
    nextPage = page + 1;
    prevPage = (page - 1) < 1 ? 1 : page - 1;
    teamId = parseInt($("#teamId").val());
    platform = $("#platform").val();
    couponRowWrapNums = $('div[id^="couponRowWrap_"]').size();
    orderId = $("#orderId").val();
    couponId = $("#couponId").val();
    username = $("#username").val();
    init = function() {
      if (couponRowWrapNums < tableRowNums || totalCouponNums <= tableRowNums * page) {
        $('#goNextPage').hide();
      }
      if (page === 1) $('#goPrevPage').hide();
      return $("#page_num_" + page).css("color", "#FAAD08").css("background-color", "#FFFFFF");
    };
    get_pageUrl = function() {
      var pageUrl;
      pageUrl = url + '?action=page';
      if (orderId !== "") pageUrl += '&orderId=' + orderId;
      if (couponId !== "") pageUrl += '&couponId=' + couponId;
      if (username !== "") pageUrl += '&username=' + username;
      return pageUrl;
    };
    $("#goNextPage").click(function() {
      window.location = get_pageUrl() + '&page=' + nextPage;
      return false;
    });
    $("#goPrevPage").click(function() {
      window.location = get_pageUrl() + '&page=' + prevPage;
      return false;
    });
    $("#searchCoupon").click(function() {
      var couponIdSearch, orderIdSearch, searchUrl, usernameSearch;
      orderIdSearch = $("#orderIdSearch").val();
      couponIdSearch = $("#couponIdSearch").val();
      usernameSearch = $("#usernameSearch").val();
      if (orderIdSearch === "" && couponIdSearch === "" && usernameSearch === "") {
        window.location = url;
      }
      searchUrl = url + '?action=search';
      if (orderIdSearch !== "") searchUrl += '&orderId=' + orderIdSearch;
      if (couponIdSearch !== "") searchUrl += '&couponId=' + couponIdSearch;
      if (usernameSearch !== "") searchUrl += '&username=' + usernameSearch;
      window.location = searchUrl;
      return false;
    });
    $('input[id$="Search"]').keydown(function(event) {
      if (event.type === 'keydown' && event.keyCode === 13) {
        return $("#searchCoupon").trigger('click');
      }
    });
    return init();
  });

}).call(this);
