<?php
/**
 * VerifyCoupon verify coupons by each platform's api
 *
 * @author    everpointer zhangfeng@laicheap.com
 * @copyright  
 * @version   v1.000
 * @history   v1.000 first release 2011-03-07
 */
class VerifyCoupon
{
    // response code for VerifyCoupon
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

    // api name for parsing configure file
    const TARGET_API = 'verifyCoupon';

    // response message according to the code
    static private $_codeMessages = array (
        self::CODE_SUCCESS_VERIFY_COUPON => "验证成功",
        self::CODE_FAIL_VERIFY_COUPON => "验证失败",
        self::CODE_ERROR_API_CALL => "接口调用出错",
        self::CODE_ERROR_PARSE_RESPONSE => "解析接口返回出错",
        self::CODE_ERROR_ONLY_SUPPORT_REST => "平台目前只支持RESTful接口的平台",
        self::CODE_ERROR_UNIVERSAL_SERVER => "验证平台后端通用错误",
        self::CODE_ERROR_UNKNOWN => "未知错误");

    // platforms supporting coupon verification
    private $_supportPlatforms = array("laiqu", "360buy");

    private $_platform = '';
    private $_requestMethod = '';   // api request method => 'REST'
    private $_httpMethod = '';          // http request method => 'GET','POST'
    private $_responseType = '';        // api response type => 'XML','JSON'
    private $_apiUrl = '';
    private $_requestParams = array();
    private $_requestParamsStatic = array();
    private $_codeTag = '';             // tag name for holding response code
    private $_codeSuccess = '';         // code value stand for successful api call
    
     
    
    /**
    * initialize class by parse specified configure file
    *
    * @param    string $platform platform token
    *
    * @return   bool  
    *
    * @throws    
    */
    public function init($platform)
    {
        if ( !$this->is_supported($platform) )
            return false;
        $this->_platform = $platform;

        $configFileName = "platform_api_confs/" . $this->_platform . ".conf"; 
        $content = file_get_contents($configFileName);
        $configs = json_decode($content);

        if (!isset($configs))
            return false;

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
            return false;
        $inputs['couponId'] = $request['couponId'];

        if (isset($this->_requestParams->couponPwd))
        {
            if ($request['couponPwd'] == '')
                return false;
            else
                $inputs['couponPwd'] = $request['couponPwd'];

        }
        return $inputs;
    }

    /**
    * get response code from a api response 
    *
    * @param    string raw response message
    *
    * @return   integer code value stand for successful api calling
    *
    * @throws    
    */
    public function get_responseCode($response)
    {
        $responseCode = "";    //验证结果编码
        if ($response == "" || $this->_responseType == "" || 
            $this->_codeTag == ""  || $this->_codeSuccess == "")
            return self::CODE_ERROR_PARSE_RESPONSE; 

        if ($this->_responseType == "LAIQU")
        {
            preg_match_all("/(<([\w]+)[^>]*>)(.*?)(<\/\\2>)/", $response, $matches, PREG_SET_ORDER);        
            foreach ($matches as $val) {
                if ($val[2] == $this->_codeTag)
                {
                    $responseCode = $val[3];
                }
            }
        } else if ($this->_responseType == "XML")
        {
            $xmlResponse = simplexml_load_string($response);
            $responseCode = $xmlResponse->$code;
        } 

        if ($responseCode == '')
            return self::CODE_ERROR_PARSE_RESPONSE;
        else
            return $responseCode == $this->_codeSuccess ? self::CODE_SUCCESS_VERIFY_COUPON : self::CODE_FAIL_VERIFY_COUPON;
    }

    /**
    * get request parameters for api
    *
    * @param    array inputs from clients
    *
    * @return   array handled parameters for each platform api
    *
    * @throws    
    */
    public function get_requestParams($inputs)
    {
        $params = array();
        foreach ($this->_requestParams as $key => $value)
        {
            //take actual param from $requestParams and value from $input
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
    * @param    array original response
    *
    * @return   string response in json
    *
    * @throws    
    */
    static public function gen_response_json($result)
    {
        if (!$result || self::$_codeMessages[$result] == '')
            return false;
            
        $response['code'] = $result;
        $response['msg']  = self::$_codeMessages[$result];
        $response['dateTime'] = date("Y-m-d, h:i:s");

        return json_encode($response);
    }
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
        if ($code == null)
            return self::$_codeMessages;
        else
            return isset(self::$_codeMessages[$code]) ? self::$_codeMessages[$code] : null;
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
