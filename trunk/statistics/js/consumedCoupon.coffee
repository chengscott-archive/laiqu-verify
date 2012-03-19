 #
 # ConsumedCoupon.coffee  coffee file for consumedCoupon.php
 #
 # @package   VerifyCoupon
 # @category  coupon
 # @author    everpointer  zhangfeng@laicheap.com
 # @version   v1.000
 # @history   v.1000 first release 2012-3-14 下午3:21
$ ->
    # common varibale define
    url = 'consumedCoupons.php'
    tableRowNums = 6
    page = parseInt $("#page").val()
    totalCouponNums = parseInt $("#totalCouponNums").val()
    nextPage = page+1
    prevPage = if (page - 1)<1 then 1 else page-1
    teamId = parseInt $("#teamId").val()
    platform = $("#platform").val()
    couponRowWrapNums = $('div[id^="couponRowWrap_"]').size()
    orderId = $("#orderId").val()
    couponId = $("#couponId").val()
    username = $("#username").val()

    # initialize page bar
    init = ->
       # hide next page bar 
       if couponRowWrapNums < tableRowNums or totalCouponNums <= tableRowNums*page
           $('#goNextPage').hide() 
       if page is 1
           $('#goPrevPage').hide() 
       $("#page_num_"+page).css("color","#FAAD08").css("background-color","#FFFFFF")

    get_pageUrl = ->
       # generate page url with hidden value
       pageUrl = url + '?action=page'
       pageUrl += '&orderId=' + orderId if orderId isnt ""
       pageUrl += '&couponId=' + couponId if couponId isnt ""
       pageUrl += '&username=' + username if username isnt ""
       return pageUrl

    $("#goNextPage").click ->
       #$.get url, {'teamId':$("#teamId").val(), 'page':parseInt($("#page").val()) + 1 }
       window.location = get_pageUrl() + '&page=' + nextPage
       return false
    $("#goPrevPage").click ->
       #$.get url, {'teamId':$("#teamId").val(), 'page':parseInt($("#page").val()) - 1 }
       window.location = get_pageUrl() + '&page=' + prevPage
       return false
    $("#searchCoupon").click ->
        orderIdSearch = $("#orderIdSearch").val()
        couponIdSearch = $("#couponIdSearch").val()
        usernameSearch = $("#usernameSearch").val()

        window.location=url if orderIdSearch is "" and couponIdSearch is "" and usernameSearch is ""

        searchUrl = url + '?action=search'
        searchUrl += '&orderId=' + orderIdSearch if orderIdSearch isnt "" 
        searchUrl += '&couponId=' + couponIdSearch if couponIdSearch isnt "" 
        searchUrl += '&username=' + usernameSearch if usernameSearch isnt "" 
        window.location = searchUrl
        return false
    $('input[id$="Search"]').keydown (event) ->
        if event.type is 'keydown' and event.keyCode is 13    #if press enter key
            $("#searchCoupon").trigger 'click'

    #js real action
    init()



    
