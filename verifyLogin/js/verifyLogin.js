(function() {
  $(function() {
    var afterLoginUrl, check_input, codeSuccess, do_login, msgBadNamePass, msgMissingName, msgMissingPass, parse_verifyResponse, triggerKeyCode, verifyLoginUrl,
      _this = this;
    codeSuccess = 22000;
    msgBadNamePass = "输入的用户名或密码不正确!";
    msgMissingName = "请输入的用户名!";
    msgMissingPass = "请输入的密码!";
    verifyLoginUrl = "doVerifyLogin.php";
    afterLoginUrl = "../verifyCoupon/index.php";
    triggerKeyCode = 13;

    do_login = function(name, passwd) {
      $("#msg_area").text("");
      return $.ajax({
        url: verifyLoginUrl,
        type: 'POST',
        data: {
          'username': name,
          'password': passwd
        },
        success: function(data) {
          if (parse_verifyResponse(data)) { return window.location = afterLoginUrl; }
        },
        complete: function(data) {
            // 去除loading dialog
            remove_loading_twirly();
        }
      });
    };
    parse_verifyResponse = function(data) {
      var response;
      response = $.parseJSON(data);
      if (response.code === codeSuccess) return true;
      $("#msg_area").text("输入的用户名或密码不正确!");
      return false;
    };
    check_input = function() {
      if ($("#username").val() === "") {
        $("#msg_area").text(msgMissingName);
        return false;
      }
      if ($("#password").val() === "") {
        $("#msg_area").text(msgMissingPass);
        return false;
      }
      return true;
    };
    $("#login_btn").click(function() {
      var name, pass;
      name = $("#username").val();
      pass = $("#password").val();
      if (check_input()) {
          show_loading_twirly();
          do_login(name, pass);
      };
      return false;
    });

    $(document).keydown(function(event) {
      if (event.type === 'keydown' && event.keyCode === triggerKeyCode) {
        return $("#login_btn").trigger('click');
      }
    });

    function login_page_init()
    {
        $("#username").focus();
    }

    login_page_init();
  });

}).call(this);