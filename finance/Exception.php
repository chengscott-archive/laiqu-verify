<?php
/**
 * Exception.php Exceptions definantion for VerifyCoupon
 *
 * @package   VerifyCoupon
 * @category  VerifyCoupon
 * @author    everpointer zhangfeng@laicheap.com
 * @version   v1.001
 * @history   v1.001 define sub exceptions 2012-3-10 下午4:25
 */
class VerifyCoupon_Exception extends Exception
{
    //// 5xx: Error - fail to request platforms' API or to parse the response 
    //const REQUEST_EXCEPTION = 500;
    //const PARSE_RESPONSE_EXCEPTION = 501;
    //const PARSE_CONFIG_FILE_EXCEPTION = 502;
    //const UNKNOWN_EXCEPTION = 503;
      
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
        parent::__construct($message, $code);
    }    
}

/**
 * Exception thrown when fail to request the API
 **/
class VerifyCoupon_RequestException extends VerifyCoupon_Exception {};

/**
 * Exception thrown when fail to request the API
 **/
class VerifyCoupon_ParseResponseException extends VerifyCoupon_Exception {};

/**
 * Exception thrown when fail to request the API
 **/
class VerifyCoupon_ParseConfigFileException extends VerifyCoupon_Exception {};
  
?>
