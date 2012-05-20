(function() {

  $(function() {
    var changePassUrl, clean_inputMsgArea, clean_inputs, codeChangePwdFailed, codeChangeSuccess, codeNewPwdNotAllowed, codeOriginPwdNotMatch, do_changePass, get_jsonResponseCode, handle_response, init, msgChangePassFailed, msgChangePassSuccess, msgInitResult, msgInputNewPwd, msgInputOriginPwd, msgNewPwdNotAllowed, msgNewPwdNotMatch, msgOriginPwdNotMatch;
    msgInputOriginPwd = "请输入原密码";
    msgInputNewPwd = "请输入新密码";
    msgNewPwdNotMatch = "两次密码不一致";
    msgOriginPwdNotMatch = "原密码错误";
    msgNewPwdNotAllowed = "新密码不符合要求";
    msgChangePassSuccess = "密码修改成功";
    msgChangePassFailed = "修改密码失败";
    msgInitResult = "重要提示：首次登录使用默认密码后，建议您修改密码，并妥善保存。";
    codeChangeSuccess = 23000;
    codeOriginPwdNotMatch = 23001;
    codeNewPwdNotAllowed = 23002;
    codeChangePwdFailed = 23003;
    changePassUrl = "do_changePass.php";
    init = function() {
      highlight_left_nav_bar('acctmanager');
      $("#result_msg_area").text(msgInitResult);
      clean_inputs();
      return clean_inputMsgArea();
    };
    clean_inputs = function() {
      $("#origin_passwd").val("");
      $("#new_passwd").val("");
      return $("#new_passwd_confirm").val("");
    };
    clean_inputMsgArea = function() {
      $("#origin_msg_area").text("");
      return $("#new_confirm_msg_area").text("");
    };
    do_changePass = function(originPasswd, newPasswd) {
      return $.ajax({
        url: changePassUrl,
        type: 'POST',
        data: {
          'originPasswd': originPasswd,
          'newPasswd': newPasswd
        },
        success: function(data) {
          return handle_response(data);
        }
      });
    };
    handle_response = function(data) {
      var msgResult, responseCode;
      responseCode = get_jsonResponseCode(data);
      if (responseCode === codeChangeSuccess) {
        init();
        msgResult = msgChangePassSuccess;
      }
      if (responseCode === codeChangePwdFailed) {
        init();
        msgResult = msgChangePassFailed;
      }
      if (responseCode === codeOriginPwdNotMatch) {
        msgResult = msgOriginPwdNotMatch;
        $("#origin_passwd").val("");
      }
      if (responseCode === codeNewPwdNotAllowed) {
        msgResult = msgNewPwdNotAllowed;
        $("#new_passwd").val("");
        $("#new_passwd_confirm").val("");
      }
      return $("#result_msg_area").text(msgResult);
    };
    get_jsonResponseCode = function(data) {
      var response, responseCode;
      try {
        response = $.parseJSON(data);
        responseCode = response === null ? msgChangePassFailed : response.code;
      } catch (e) {
        responseCode = msgChangePassFailed;
      }
      return responseCode;
    };
    $("#do_change_btn").click(function() {
      var newPasswd, newPasswdConfirm, originPasswd;
      originPasswd = $("#origin_passwd").val();
      newPasswd = $("#new_passwd").val();
      newPasswdConfirm = $("#new_passwd_confirm").val();
      $("#origin_msg_area").text($.trim(originPasswd) === "" ? msgInputOriginPwd : "");
      if ($.trim(newPasswd) === "" || $.trim(newPasswdConfirm) === "") {
        return $("#new_confirm_msg_area").text(msgInputNewPwd);
      }
      if ($.trim(newPasswd).match(new RegExp(/^[a-zA-Z]\w{5,17}$/)) === null) {
        return $("#new_confirm_msg_area").text(msgNewPwdNotAllowed);
      }
      if ($.trim(newPasswd) !== $.trim(newPasswdConfirm)) {
        return $("#new_confirm_msg_area").text(msgNewPwdNotMatch);
      } else {
        $("#new_confirm_msg_area").text("");
      }
      return do_changePass(originPasswd, newPasswd);
    });
    return init();
  });

}).call(this);
