$ ->
    codeSuccess = 22000
    msgBadNamePass = "输入的用户名或密码不正确!"
    msgMissingName = "请输入的用户名!"
    msgMissingPass = "请输入的密码!"
    verifyLoginUrl = "doVerifyLogin.php"
    afterLoginUrl  = "../VerifyCoupon/index.html"
    #13 means enter key code
    triggerKeyCode = 13 

    do_login = (name, passwd) ->
        $.ajax
            url: verifyLoginUrl
            type: 'POST'
            data: {'username':name, 'password':passwd}
            success: (data) ->
                window.location = afterLoginUrl if parse_verifyResponse data            

    parse_verifyResponse = (data) ->
        response = $.parseJSON data
        return true if response.code is codeSuccess
        $("#msg_area").text "输入的用户名或密码不正确!"
        return false
            
    check_input = ->
        if $("#username").val() is ""
            $("#msg_area").text msgMissingName
            return false
        if $("#password").val() is ""
            $("#msg_area").text msgMissingPass
            return false
        return true
        
    $("#login_btn").click ->
        name = $("#username").val()
        pass = $("#password").val()
        do_login name,pass if check_input()
        return false
    $(document).keydown (event) =>
        if event.type is 'keydown' and event.keyCode is triggerKeyCode
            $("#login_btn").trigger 'click'


