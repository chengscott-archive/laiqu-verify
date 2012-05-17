<?php
/**
 * VerifyCoupon.php class VerifyCoupon
 *
 * @package   VerifyCoupon
 * @category  VerifyCoupon
 * @author    everpointer zhangfeng@laicheap.com
 * @version   v1.001
 * @history:  v1.000 first release 2011-03-07
 *            v1.001 add Exception handling 2012-03-09 17:21:25
 */

require_once('../common/Exception.php');
require_once('../common/CodeMessages.php');

/**
 * VerifyCoupon verify coupons through platforms's API
 */
class VerifyCoupon
{
    // response code for VerifyCoupon


    // api name for parsing configure file
    const TARGET_API = 'verifyCoupon';


    // platforms supporting coupon verification
    private $_supportPlatforms = array("laiqu", "juhuasuan");  //  currently not support 360buy

    private $_platform = '';
    private $_requestMethod = '';   // api request method => 'REST'
    private $_httpMethod = '';          // http request method => 'GET','POST'
    private $_responseType = '';        // api response type => 'XML','JSON'
    private $_apiUrl = '';
    private $_requestParams = array();
    private $_requestParamsStatic = array();
    private $_codeTag = '';             // tag name for holding response code
    private $_codeSuccess = '';         // code value stand for successful api call
    private $_couponId = 0;             // coupon id for verification
    
     
    
    /**
    * initialize class by parse specified configure file
    *
    * @param    string $platform platform token
    *
    * @return   bool only true mean initializing successfully else return 
    *                specified error code
    *
    * @throws   VerifyCoupon_ParseConfigFileException
    */
    public function init($platform)
    {
        if ( !$this->is_supported($platform) )
        {
            throw new VerifyCoupon_RequestException(
                VerifyCouponCodeMsg::get_message(VerifyCouponCodeMsg::UNKNOWN_PLATFORM_ERROR),
                VerifyCouponCodeMsg::UNKNOWN_PLATFORM_ERROR);
        }
        $this->_platform = $platform;

        $configFileName = "../platform_api_confs/" . $this->_platform . ".conf"; 
        $content = file_get_contents($configFileName);
        $configs = json_decode($content);

        if ($configs == null)
        {
            throw new VerifyCoupon_ParseConfigFileException(
                VerifyCouponCodeMsg::get_message(VerifyCouponCodeMsg::PARSE_CONFIG_FILE_ERROR),
                VerifyCouponCodeMsg::PARSE_CONFIG_FILE_ERROR);
        }

        $this->_requestMethod = $configs->requestMethod;
        $this->_httpMethod = $configs->httpMethod;
        $this->_responseType = $configs->responseType;
        $targetApi = self::TARGET_API;
        $targetApiConfs = $configs->$targetApi;

        $this->_apiUrl = $targetApiConfs->apiUrl;
        $this->_requestParams = $targetApiConfs->requestParams;
        $this->_requestParamsStatic = $targetApiConfs->requestParamsStatic;
        $this->_codeTag = $targetApiConfs->responseResult->codeTag;
        $this->_codeSuccess = $targetApiConfs->responseResultStatic->codeSuccess;

        return true;
    } 

    /**
    * check whether specified platform is supported
    *
    * @param    string $platform platform token
    *
    * @return   bool  
    *
    * @throws    
    */
    public function is_supported($platform)
    {
        if ($platform == '')
            return false;
        foreach ($this->_supportPlatforms as $support)
        {
            if ($platform == $support)
                return true;
        }

        return false;
    } 


    /**
    * verify inputs from application
    *
    * @param    array store the input values
    * 
    * @return   array 
    *
    * @throws    
    */
    public function init_verifyInputs($request)
    {
        if (!isset($request['couponId']) || $request['couponId'] == '')
        {
            throw new VerifyCoupon_RequestException(
                VerifyCouponCodeMsg::get_message(VerifyCouponCodeMsg::INVALID_COUPONID_ARG_ERROR),
                VerifyCouponCodeMsg::INVALID_COUPONID_ARG_ERROR);   
        }
        $inputs['couponId'] = $request['couponId'];

        if (isset($this->_requestParams->couponPwd))
        {
            if ($request['couponPwd'] == '')
            {
                throw new VerifyCoupon_RequestException(
                    VerifyCouponCodeMsg::get_message(VerifyCouponCodeMsg::INVALID_COUPONPWD_ARG_ERROR),
                    VerifyCouponCodeMsg::INVALID_COUPONPWD_ARG_ERROR);            
            }
            else
                $inputs['couponPwd'] = $request['couponPwd'];

        }
        if (isset($this->_requestParams->consumeCount))
        {
            $consumeCount = $request['consumeCount'];
            if ($consumeCount === "" || !is_numeric($consumeCount) || intval($consumeCount) < 1)
            {
                throw new VerifyCoupon_RequestException(
                    VerifyCouponCodeMsg::get_message(VerifyCouponCodeMsg::MALFORMED_COUNT_NUMBER),
                    VerifyCouponCodeMsg::MALFORMED_COUNT_NUMBER);            
            }
            else
                $inputs['consumeCount'] = $consumeCount;

        }
        return $inputs;
    }

    /**
    * get response code from a api response 
    *
    * @param    string  raw response message
    *
    * @return   int  value VERIFY_COUPON_SUCCESS stand for coupon can be used
    *                value VERIFY_COUPON_FAIL stand for coupon can't be used
    *
    * @throws   VerifyCoupon_ParseResponseException
    */
    public function get_responseCode($response)
    {
        $responseCode = "";    //验证结果编码
        if (($response == "" && $this->_responseType !== "JUHUASUAN") || $this->_responseType == "" || 
            $this->_codeTag == ""  || $this->_codeSuccess == "")
        {
            throw new VerifyCoupon_ParseResponseException(
                VerifyCouponCodeMsg::get_message(VerifyCouponCodeMsg::PARSE_RESPONSE_EXCEPTION),
                VerifyCouponCodeMsg::PARSE_RESPONSE_EXCEPTION);            
        }            

        $codeTag = $this->_codeTag;
        $responseType = strtoupper($this->_responseType);
        if ($responseType== "LAIQU")
        {
            preg_match_all("/(<([\w]+)[^>]*>)(.*?)(<\/\\2>)/", $response, $matches, PREG_SET_ORDER);        
            foreach ($matches as $val) {
                if ($val[2] == $this->_codeTag)
                {
                    $responseCode = $val[3];
                }
            }
        } else if ($responseType == "XML")
        {
            $xmlResponse = simplexml_load_string($response);
            $responseCode = $xmlResponse->$codeTag;
        } else if ($responseType === "JSON")
        {
            $jsonResponse = json_decode($response);
            $responseCode = $jsonResponse->$codeTag;
        } else if ($responseType === "JUHUASUAN")
        {
            // 针对聚划算的返回结果进行特殊处理
            // 返回结果中有true, 说明验证成功
            if (false === stripos($response, "false"))
            {
                $responseCode = "true";
            } else if (false !== strpos($response, "302")){
                // 聚划算验证结果中如果包含 status:302, 说明登陆session过期了.
                $responseCode = "login_expired";
            } else {
                $responseCode = "false";
            }
        }

        if ($responseCode == '')
        {
            throw new VerifyCoupon_ParseResponseException(
                VerifyCouponCodeMsg::get_message(VerifyCouponCodeMsg::PARSE_RESPONSE_EXCEPTION),
                VerifyCouponCodeMsg::PARSE_RESPONSE_EXCEPTION);            
        }
        if ($responseCode == $this->_codeSuccess)
        {
            return VerifyCouponCodeMsg::VERIFY_COUPON_SUCCESS;
        } 
        else if ($responseCode === "login_expired")
        {
            return VerifyCouponCodeMsg::JUHUASUAN_LOGIN_EXPIRED;
        }
        else {
            return VerifyCouponCodeMsg::VERIFY_COUPON_FAIL;
        }
    }

    /**
    * get request parameters for api
    *
    * @param    array inputs from clients
    *
    * @return   array|int  array means transfer inputs to request params successfully
    *                      int means error code
    *
    * @throws    
    */
    public function get_requestParams($inputs)
    {
        if (!is_array($inputs))
        {
            throw new VerifyCoupon_RequestException(
                VerifyCouponCodeMsg::get_message(VerifyCouponCodeMsg::TRANSFER_REQUEST_PARAM_ERROR),
                VerifyCouponCodeMsg::TRANSFER_REQUEST_PARAM_ERROR);            
        }

        $params = array();
        foreach ($this->_requestParams as $key => $value)
        {
            //take actual param from $requestParams and value from $input
            if (!isset($inputs[$key]))
            {
                throw new VerifyCoupon_RequestException(
                    VerifyCouponCodeMsg::get_message(VerifyCouponCodeMsg::TRANSFER_REQUEST_PARAM_ERROR),
                    VerifyCouponCodeMsg::TRANSFER_REQUEST_PARAM_ERROR);            
            }
            else
                $params[$value] = $inputs[$key];
        }    
        if ($this->_requestParamsStatic != null)
        {
            //set static param such as VenderKey
            foreach ($this->_requestParamsStatic as $key => $value)
            {
                $params[$key] = $value;
            }
        }
        return $params;
    }

    /**
    * genenrate response for clients in json stype
    *
    * @param    int|Exception code or exception to response
    *
    * @return   string response in json
    *
    * @throws    
    */
    static public function gen_response_json($result, $msg = "")
    {
        // success or fail
        $code = $result;
        if ($msg == "")
        {
            // msg from VerifyCouponCodeMsg 
            $msg = VerifyCouponCodeMsg::get_message($code);

            // gen_response_json exception
            if (empty($msg))
            {
                $code =  CommonCodeMsg::INVALID_ARGUMENT_ERROR;
                $msg  =  CommonCodeMsg::get_message($code);  
            }
        }

        if ($code === VerifyCouponCodeMsg::VERIFY_COUPON_SUCCESS)
        {
            $response['success'] = true;
        } else {
            $response['success'] = false;
        }
        $response['code'] = $code;
        $response['msg']  = $msg;
        $response['dateTime'] = date("Y-m-d, h:i:s");

        return json_encode($response);
    }
    /**
    * get member platform
    */
    public function get_platform()
    {
        return $this->_platform;
    } 
    /**
    * get member platform
    */
    public function get_requestMethod()
    {
        return $this->_requestMethod;
    } 
    /**
    * get member platform
    */
    public function get_httpMethod()
    {
        return $this->_httpMethod;
    } 
    /**
    * get member platform
    */
    public function get_responseType()
    {
        return $this->_responseType;
    } 
    /**
    * get member platform
    */
    public function get_apiUrl()
    {
        return $this->_apiUrl;
    } 
    /**
    * get member platform
    */
    public function get_codeTag()
    {
        return $this->_codeTag;
    } 
    /**
    * get member platform
    */
    public function get_codeSuccess()
    {
        return $this->_codeSuccess;
    } 
}
?>
