<?php
/**
 * utilities.php common used functions defined here
 *
 * @package   VerifyCoupon
 * @category  VerifyCoupon
 * @author    everpointer zhangfeng@laicheap.com
 * @version   v1.000
 * @history   v1.000 first release 2012-3-12 下午6:23
 */
require_once('CodeMessages.php');

/**
* genenrate response for clients in json stype
*
* @param    class  code or exception to response
* @param    int  code or exception to response
* @param    int  code or exception to response
*
* @return   string response in json
*
* @throws    
*/
function gen_response_json($codeMsg, $result, $msg = "")
{
    // success or fail
    if (empty($codeMsg) || !class_exists($codeMsg))
    {
       $code = CommonCodeMsg::CODEMSG_CLASS_NOT_EXIST; 
       $msg  = CommonCodeMsg::get_message($code); 
    } else {
        $code = $result;
        if ($msg == "")
        {
            // msg from $codeMsg class 
            $codeMsgInstance = new $codeMsg();
            $msg = $codeMsgInstance->get_message($code);

            // gen_response_json exception
            if (empty($msg))
            {
                $code =  CommonCodeMsg::INVALID_ARGUMENT_ERROR;
                $msg  =  CommonCodeMsg::get_message($code);  
            }
        }
    }

    $response['code'] = $code;
    $response['msg']  = $msg;
    $response['dateTime'] = date("Y-m-d, h:i:s");

    return json_encode($response);
}

/**
** cal total page nums by row nums and page's nums
**
** @param    int  $rowNums  total row nums to cal
** @param    int  $rowNumsEachPage  defined row nums on each page
** @return   int  total pages
** @throws    
**/
function cal_totalPages($rowNums, $rowNumsEachPage)
{
    if ($rowNums > 0)
    {
        $totalPages = ceil($rowNums/$rowNumsEachPage);
    } else {
        $totalPages = 0;
    }
    return $totalPages;
}
?>
