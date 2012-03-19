# changePass.coffee  handle client pass changing
#
# @package   verifyCoupon
# @category  acctManager
# @author    everpointer  zhangfeng@laicheap.com
# @version   v1.000
# @history   v1.000 first release 2012-3-16 下午2:30
#


$ ->
    # messages
    msgInputOriginPwd = "请输入原密码"
    msgInputNewPwd = "请输入新密码"
    msgNewPwdNotMatch = "两次密码不一致"
    msgOriginPwdNotMatch = "原密码错误"
    msgNewPwdNotAllowed = "新密码不符合要求"
    msgChangePassSuccess = "密码修改成功"
    msgChangePassFailed = "修改密码失败"
    msgInitResult = "重要提示：首次登录使用默认密码后，建议您修改密码，并妥善保存。"

    # response codes
    codeChangeSuccess = 23000
    codeOriginPwdNotMatch = 23001
    codeNewPwdNotAllowed = 23002
    codeChangePwdFailed = 23003

    changePassUrl = "do_changePass.php"

    init = ->
        $("#result_msg_area").text msgInitResult
        clean_inputs()
        clean_inputMsgArea()

    clean_inputs = ->
        $("#origin_passwd").val ""
        $("#new_passwd").val ""
        $("#new_passwd_confirm").val ""
    clean_inputMsgArea = ->
        $("#origin_msg_area").text ""
        $("#new_confirm_msg_area").text ""

    do_changePass = (originPasswd, newPasswd) ->
         $.ajax
           url: changePassUrl
           type: 'POST'
           data: {'originPasswd':originPasswd, 'newPasswd':newPasswd}
           success: (data) ->
               handle_response data
                
    handle_response = (data) ->
        responseCode = get_jsonResponseCode data
        if responseCode is codeChangeSuccess
            init()
            msgResult = msgChangePassSuccess
        if responseCode is codeChangePwdFailed
            init()
            msgResult = msgChangePassFailed 
        if responseCode is codeOriginPwdNotMatch
            msgResult = msgOriginPwdNotMatch 
            $("#origin_passwd").val ""
        if responseCode is codeNewPwdNotAllowed
            msgResult = msgNewPwdNotAllowed 
            $("#new_passwd").val ""
            $("#new_passwd_confirm").val ""
        $("#result_msg_area").text msgResult

    get_jsonResponseCode = (data) ->
        try 
            response = $.parseJSON data
            responseCode = if response is null then msgChangePassFailed else response.code
        catch e
            responseCode = msgChangePassFailed
        return responseCode

    $("#do_change_btn").click ->
        originPasswd = $("#origin_passwd").val()
        newPasswd = $("#new_passwd").val()
        newPasswdConfirm = $("#new_passwd_confirm").val()
        $("#origin_msg_area").text if $.trim(originPasswd) is "" then msgInputOriginPwd else ""
        return $("#new_confirm_msg_area").text msgInputNewPwd if $.trim(newPasswd) is "" or $.trim(newPasswdConfirm) is ""
        return $("#new_confirm_msg_area").text msgNewPwdNotAllowed if $.trim(newPasswd).match(new RegExp(/^[a-zA-Z]\w{5,17}$/)) is null
        if $.trim(newPasswd) isnt $.trim(newPasswdConfirm) then return $("#new_confirm_msg_area").text msgNewPwdNotMatch
        else $("#new_confirm_msg_area").text ""
        do_changePass originPasswd,newPasswd   

