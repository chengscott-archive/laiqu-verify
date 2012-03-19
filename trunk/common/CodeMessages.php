<?php
/**
 * VerifyCouponError.php error concerning info defination
 *
 * @package   VerifyCoupon
 * @category  VerifyCoupon
 * @author    everpointer zhangfeng@laicheap.com
 * @version   v1.000
 * @history   v1.000 first release 2012-3-12 上午9:26
 */

class CommonCodeMsg
{
    /* varibale defination */
    // 20xxx: Error -  server side error before and after when verify login
    const SYSTEM_RUNTIME_ERROR = 20000;
    const UNCAUGHT_FATAL_ERROR = 20001;
    const INVALID_ARGUMENT_ERROR = 20002;    // universal error when pass wrong argument to a function
    const UNKNOWN_EXCEPTION = 20003;
    const CODEMSG_CLASS_NOT_EXIST = 20004;
    const MYSQL_CONNECT_ERROR = 20005;
    const MYSQL_SELECT_DB_ERROR = 20006;

    // response message according to the code
    static private $_codeMessages = array (
        self::SYSTEM_RUNTIME_ERROR => "系统运行时错误",
        self::UNCAUGHT_FATAL_ERROR => "未捕捉的致命错误",
        self::INVALID_ARGUMENT_ERROR => "无效的参数",
        self::UNKNOWN_EXCEPTION => "未知异常",
        self::CODEMSG_CLASS_NOT_EXIST => "错误代码信息类不存在",
        self::MYSQL_CONNECT_ERROR => "数据库连接错误",
        self::MYSQL_SELECT_DB_ERROR => "数据库选择错误"
    );

    /**
    * get message according to specified code
    *
    * @param    int $code response code
    * @return   string|array|null message for $code if $code is given
    *                             (null if no message is available) , array of 
    *                             all messages if $code is null.
    * @throws    
    */
    static public function get_message($code = null)
    {
        if ($code === null)
            return self::$_codeMessages;
        else
            return isset(self::$_codeMessages[$code]) ? self::$_codeMessages[$code] : null;
    }     
}

class VerifyCouponCodeMsg
{
    /* varibale defination */
    // 21xxx: Normal - platform's API is called without error and exception
    const VERIFY_COUPON_SUCCESS = 21000;
    const VERIFY_COUPON_FAIL = 210001;
    // 2xxxx: Error - server side error before and after API is called
    const UNKNOWN_PLATFORM_ERROR = 21100;
    const TRANSFER_REQUEST_PARAM_ERROR = 21102;
    const REQUEST_EXCEPTION = 21103;
    const PARSE_RESPONSE_EXCEPTION = 21104;
    const PARSE_CONFIG_FILE_EXCEPTION = 21105;
    const UNKNOWN_EXCEPTION = 21106;
    const MARK_COUPON_CONSUMED_ERROR = 21107;
    const INVALID_COUPONID_ARG_ERROR = 21108;
    const INVALID_COUPONPWD_ARG_ERROR = 21109;

    
    // response message according to the code
    static private $_codeMessages = array (
        self::VERIFY_COUPON_SUCCESS => "验证成功",
        self::VERIFY_COUPON_FAIL => "验证失败",
        self::UNKNOWN_PLATFORM_ERROR => "未知平台错误",
        self::TRANSFER_REQUEST_PARAM_ERROR => "将客户端输入转为API请求参数错误",
        self::REQUEST_EXCEPTION => "接口验证API异常",
        self::PARSE_RESPONSE_EXCEPTION => "解析接口返回异常",
        self::PARSE_CONFIG_FILE_EXCEPTION => "解析平台接口配置文件异常",
        self::UNKNOWN_EXCEPTION => "未知异常",
        self::MARK_COUPON_CONSUMED_ERROR => "标记团购券已消费错误",
        self::INVALID_COUPONID_ARG_ERROR => "团购券ID参数无效",
        self::INVALID_COUPONPWD_ARG_ERROR => "团购券密码参数无效"
    );

    /**
    * get message according to specified code
    *
    * @param    int $code response code
    * @return   string|array|null message for $code if $code is given
    *                             (null if no message is available) , array of 
    *                             all messages if $code is null.
    * @throws    
    */
    static public function get_message($code = null)
    {
        if ($code === null)
            return self::$_codeMessages;

        $msg = isset(self::$_codeMessages[$code]) ? self::$_codeMessages[$code] : "";
        $msg = $msg === ""? CommonCodeMsg::get_message($code) : $msg;
        return $msg;
    }     
}

class VerifyLoginCodeMsg
{
    /* varibale defination */
    // 22xxx: Error -  server side error before and after when verify login
    const VERIFY_LOGIN_SUCCESS = 22000;
    const VERIFY_LOGIN_FAIL = 22001;
    const MYSQL_CONNECT_ERROR = 22100;
    const MYSQL_SELECT_DB_ERROR = 22101;
    const INVALID_NAME_PASS = 22102;
    const ERROR_NAME_PASS = 22103;
    
    // response message according to the code
    static private $_codeMessages = array (
        self::VERIFY_LOGIN_SUCCESS => "登陆验证成功",
        self::VERIFY_LOGIN_FAIL => "登陆验证失败",
        self::MYSQL_CONNECT_ERROR => "建立数据库连接失败",
        self::MYSQL_SELECT_DB_ERROR => "选择数据库错误",
        self::INVALID_NAME_PASS => "无效的用户名和密码",
        self::ERROR_NAME_PASS => "错误的用户名和密码"
    );

    /**
    * get message according to specified code
    *
    * @param    int $code response code
    * @return   string|array|null message for $code if $code is given
    *                             (null if no message is available) , array of 
    *                             all messages if $code is null.
    * @throws    
    */
    static public function get_message($code = null)
    {
        if ($code === null)
            return self::$_codeMessages;

        $msg = isset(self::$_codeMessages[$code]) ? self::$_codeMessages[$code] : "";
        $msg = $msg === ""? CommonCodeMsg::get_message($code) : $msg;
        return $msg;
    }     
}

class ChangePassCodeMsg
{
    /* varibale defination */
    // 23xxx: Error -  server side error before and after when verify login
    const CHANGE_PASS_SUCCESS = 23000;
    const ORIGIN_PASS_NOT_MATCH = 23001;
    const NEW_PASS_NOT_ALLOWED_ERROR = 23002;
    const CHANGE_PASS_FAILED = 23003;
    
    // response message according to the code
    static private $_codeMessages = array (
        self::CHANGE_PASS_SUCCESS => "更改密码成功",
        self::ORIGIN_PASS_NOT_MATCH => "原始密码与输入密码不一致",
        self::NEW_PASS_NOT_ALLOWED_ERROR => "新密码不符合要求",
        self::CHANGE_PASS_FAILED => "更新密码失败"
    );

    /**
    * get message according to specified code
    *
    * @param    int $code response code
    * @return   string|array|null message for $code if $code is given
    *                             (null if no message is available) , array of 
    *                             all messages if $code is null.
    * @throws    
    */
    static public function get_message($code = null)
    {
        if ($code === null)
            return self::$_codeMessages;

        $msg = isset(self::$_codeMessages[$code]) ? self::$_codeMessages[$code] : "";
        $msg = $msg === ""? CommonCodeMsg::get_message($code) : $msg;
        return $msg;
    }     
}

?>
