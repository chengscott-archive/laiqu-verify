(function() {

  $(function() {
    var get_pageUrl, init, nextPage, page, prevPage, tableRowNums, teamNums, teamRowWrapNums, url;
    url = 'teamList.php';
    tableRowNums = 4;
    page = parseInt($("#page").val());
    teamNums = parseInt($("#teamNums").val());
    nextPage = page + 1;
    prevPage = (page - 1) < 1 ? 1 : page - 1;
    teamRowWrapNums = $('div[id^="teamRowWrap_"]').size();
    init = function() {
      highlight_left_nav_bar('statistics');        
      if (teamRowWrapNums < tableRowNums || teamNums <= page * tableRowNums) {
        $('#goNextPage').hide();
      }
      if (page === 1) $('#goPrevPage').hide();
      return $("#page_num_" + page).css("color", "#FAAD08").css("background-color", "#FFFFFF");
    };
    get_pageUrl = function() {
      var pageUrl;
      pageUrl = url;
      return pageUrl;
    };
    $("#goNextPage").click(function() {
      return window.location = url + '?page=' + nextPage;
    });
    $("#goPrevPage").click(function() {
      return window.location = url + '?page=' + prevPage;
    });
    return init();
  });

}).call(this);
