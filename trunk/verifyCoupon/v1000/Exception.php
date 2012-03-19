<?php
/**
 * Exception.php Exception definantion for VerifyCoupon
 *
 * @package   VerifyCoupon
 * @category  VerifyCoupon
 * @author    everpointer zhangfeng@laicheap.com
 * @version   v1.100
 * @history   v1.100 first release 2012-3-9 下午5:36
 */
 
/**
 * VerifyCoupon_Exception exceptions for package VerifyCoupon
 * 
 * @extends PEAR_Exception PEAR package
 * @implements
 */
 class VerifyCoupon_Exception extends PEAR_Exception
 {
    /** code consants defination */
    // 2xx: Success - platform's API is called and Coupon successfully goes through verification
    const CODE_SUCCESS_VERIFY_COUPON = 200;
    
    // 3xx: Fail - platform's API is requested and Coupon verification failed 
    const CODE_FAIL_VERIFY_COUPON = 300;

    // 4xx: Error - fail to request platforms' API or to parse the response 
    const CODE_ERROR_API_CALL = 400;
    const CODE_ERROR_PARSE_RESPONSR = 401;
    const CODE_ERROR_ONLY_SUPPORT_REST = 402;
    const CODE_ERROR_UNIVERSAL_SERVER =  403;
    const CODE_ERROR_UNKNOWN =  404;
    const CODE_ERROR_INVALID_ARG =  405;
  
    /** messages according to the code */
    static private $_codeMessages = array (
        self::CODE_SUCCESS_VERIFY_COUPON => "验证成功",
        self::CODE_FAIL_VERIFY_COUPON => "验证失败",
        self::CODE_ERROR_API_CALL => "接口调用出错",
        self::CODE_ERROR_PARSE_RESPONSE => "解析接口返回出错",
        self::CODE_ERROR_ONLY_SUPPORT_REST => "平台目前只支持RESTful接口的平台",
        self::CODE_ERROR_UNIVERSAL_SERVER => "验证平台后端通用错误",
        self::CODE_ERROR_UNKNOWN => "未知错误",
        self::CODE_ERROR_INVALID_ARG => "无效的参数"
    );

   /**
    * Constructor, can set package error code
    *
    * @param string exception message
    * @param int    package error code, one of class constants
    *
    * throw PEAR_Exception
    */
    public function __construct($message = null, $code = null)
    {
        if ($message != null && $code == null)
        {
            try {
                $message = self::get_message($code);
                parent::__construct($message, $code);
            } 
            catch (PEAR_Exception $pr)
            {
                throw $pr;
            }
        } else {
            parent::__construct($message, $code);
        }
    }    

   /**
    * get message according to specified code
    *
    * @param    int $code response code
    * @return   string|array|null message for $code if $code is given
    *                             (throw Exception if no message is available) , array of 
    *                             all messages if $code is null.
    * @throws   PEAR_Exception
    */
    static public function get_message($code = null)
    {
        if ($code == null)
            return self::$_codeMessages;

        if (! isset(self::$_codeMessages[$code]))
            throw new PEAR_Exception(self::$_codeMessage(self::CODE_ERROR_INVALID_ARG), 
                                     self::CODE_ERROR_INVALID_ARG);
        else
            return self::$_codeMessages[$code];
    } 
    
 }
?>
