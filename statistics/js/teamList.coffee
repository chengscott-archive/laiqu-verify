 #
 # teamList.coffee  coffee file for teamList.php
 #
 # @package   VerifyCoupon
 # @category  statistics
 # @author    everpointer  zhangfeng@laicheap.com
 # @version   v1.000
 # @history   v.1000 first release 2012-3-14 下午3:21
$ ->
    # common varibale define
    url = 'teamList.php'
    tableRowNums = 4
    page = parseInt $("#page").val()
    teamNums = parseInt $("#teamNums").val()
    nextPage = page+1
    prevPage = if (page - 1)<1 then 1 else page-1
    teamRowWrapNums = $('div[id^="teamRowWrap_"]').size()

    init = ->
       if teamRowWrapNums < tableRowNums or teamNums <= page*tableRowNums
           $('#goNextPage').hide() 
       if page is 1
           $('#goPrevPage').hide() 
       $("#page_num_"+page).css("color","#FAAD08").css("background-color","#FFFFFF")

    get_pageUrl = ->
       # generate page url with hidden value
       pageUrl = url
       return pageUrl

    $("#goNextPage").click ->
       window.location = url + '?page=' + nextPage
    $("#goPrevPage").click ->
       window.location = url + '?page=' + prevPage

    #js real action
    init()

