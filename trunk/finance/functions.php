<?php
require_once 'common.php';
/**
 * 获得指定产品消费信息
 * @param  string $platform      [description]
 * @param  array $productTitles  返回指定标题的产品，null表示返回所有产品
 * @param  string $beginDate     指定开始时间，格式:"2012-07-02 11:11:11"
 * @param  string $endDate       指定结束时间:"2012-07-02 11:11:11"
 * @return array                 消费列表信息
 */
function get_expense_list($platform, $productTitles, $beginDate = "", $endDate = "")
{
    $productList = get_product_list($platform, $productTitles);
    // var_dump($productList);
    // if ($productTitle !== "")
    // {
    //     $matchedProduct = null;
    //     foreach($productList as $product)
    //     {
    //         if($product['title'] === $productTitle)
    //         {
    //             $matchedProduct = $product;
    //             break;
    //         }
    //     }
    //     if ($matchedProduct === null)
    //     {
    //         return array();
    //     } else {
    //         $productList = array($matchedProduct);
    //     }
    // }
    for ($pCount = 0; $pCount < count($productList); $pCount++)
    {
        $product = &$productList[$pCount];
        // 需要统计的金额
        $productOrderMoney = 0;    // 产品消费金额
        $productExpenseMoney = 0;    // 产品消费金额
        $productRefundMonry  = 0;    // 产品退款金额
        $productExpenseNums = 0;    // 产品消费份数

        $productRefundRecordTimes = 0; // 退款总份数
        $productJuwuyouRefundRecordTimes = 0; // 聚无忧退款总份数
        $productManualRefundRecordTimes = 0; // 人工退款总份数

        $productRefundRecordNums = 0;        // 退款总比数 (即凭证管理中的退款记录数)
        $productJuwuyouRefundRecordNums = 0; // 聚无忧退款总比数
        $productManualRefundRecordNums =  0; // 人工退款总比数
        $productJuwuyouRefundMoney = 0;      // 聚无忧退款总金额 
        $productManualRefundMoney = 0;       // 人工退款总金额 
        $productIncomingAfter7 = 0;          // 7天后退款总收入

        $productOrderNums = 0; // 总订单数
        $productOrderTimes = 0; // 总订单份数

        $productId = $product['platform_record_id'];
        $orderList = get_order_list($platform, $productId);
        $productOrderNums = count($orderList);
        for ($oCount = 0; $oCount < count($orderList); $oCount++)
        {
            $order = &$orderList[$oCount];

            $productOrderTimes += $order['purchase_nums'];

            $orderMoney = 0;    // 订单购买金额
            $orderExpenseMoney = 0;         // 订单消费金额
            $orderManualRefundMoney = 0;    // 订单手工退款金额
            $orderJuwuyouRefundMoney = 0;   // 订单聚无忧退款金额
            $orderExpenseNums = 0;          // 消费笔数(不是总数)
            $orderRefundRecordNums = 0;        // 退款总比数 (即凭证管理中的退款记录数)
            $orderJuwuyouRefundRecordNums = 0; // 聚无忧退款总比数
            $orderManualRefundRecordNums =  0; // 人工退款总比数
            $orderIncomingAfter7 = 0;          // 7天后退款总收入

            $orderRefundRecordTimes = 0; // 退款总份数
            $orderJuwuyouRefundRecordTimes = 0; // 聚无忧退款总份数
            $orderManualRefundRecordTimes = 0; // 人工退款总份数

            $orderId = $order['platform_record_id'];
            $taobaoId = $order['taobao_id'];
            // 订单单价 = 交易金额/购买数量
            $buy_nums = intval($order['purchase_nums']);
            // 转换交易金额,交易金额格式￥2,999.00 (美国风格,需要去掉逗号)
            // $paymentStr = substr($order['payment'],3); 
            $payment = doubleval($order['payment']);
            $order_unit_price = $payment/$buy_nums;
            $order['unit_price'] = $order_unit_price;
            // 根据订单编号获得指定日期的使用记录
            $order_used_list = get_order_used_list($platform, $orderId, $beginDate, $endDate);
            for ($i=0; $i < count($order_used_list); $i++)
            {
                $order_used = $order_used_list[$i];
                $operationMoney = $order_unit_price*intval($order_used['consume_times']);
                if ($order_used['operation_type'] === "兑换")
                {
                    // 增加消费金额
                    $orderExpenseMoney += $operationMoney;
                    $orderExpenseNums += $order_used['consume_times'];
                } if ($order_used['operation_type'] === "恢复")
                {
                    // 恢复操作,目前只允许对兑换操作进行恢复，因为码商平台无法区分兑换
                    // 恢复还是作废恢复。总之尽量避免回复操作。
                    $orderExpenseMoney -= $operationMoney;
                    $orderExpenseNums -= $order_used['consume_times'];
                } 
                else if ($order_used['operation_type'] === "作废"){
                    // 计算退款金额,以7天为期限
                    $orderRefundRecordTimes += $order_used['consume_times'];
                    $order_used_refund = cal_order_used_refund($order_used, $order_unit_price, $orderIncomingAfter7);
                    if ($order_used['source'] === "聚无忧")
                    {
                        $orderJuwuyouRefundRecordTimes += $order_used['consume_times'];
                        // 增加聚无忧退款
                        $orderJuwuyouRefundMoney += $order_used_refund;
                        $orderJuwuyouRefundRecordNums++;
                    } elseif ($order_used['source'] === "管理平台") {
                        $orderManualRefundRecordTimes += $order_used['consume_times'];
                        // 增加聚无忧手工退款
                        $orderManualRefundMoney += $order_used_refund;
                        $orderManualRefundRecordNums++;
                    }
                }
            } 
            // 将统计金额回写到订单列表中
            // Todo: 为了比对数据，到时可以将订单列表记录到文件中,
            // 再根据各种条件导出Excel，进行比对
            $order['expense_money'] = $orderExpenseMoney;
            $order['total_refund'] = $orderJuwuyouRefundMoney + $orderManualRefundMoney;
            $order['juwuyou_refund'] = $orderJuwuyouRefundMoney;
            $order['manual_refund'] = $orderManualRefundMoney;
            $order['expense_nums'] = $orderExpenseNums;
            $order['juwuyou_refund_record_nums'] = $orderJuwuyouRefundRecordNums;
            $order['manual_refund_record_nums'] = $orderManualRefundRecordNums;
            $order['refund_record_nums'] = $orderRefundRecordNums = $orderJuwuyouRefundRecordNums + $orderManualRefundRecordNums;
            $order['incoming_after_7'] = $orderIncomingAfter7;
            $order['refund_record_times'] = $orderRefundRecordTimes;
            $order['juwuyou_refund_record_times'] = $orderJuwuyouRefundRecordTimes;
            $order['manual_refund_record_times'] = $orderManualRefundRecordTimes;
            // 回加到产品数据上
            $productOrderMoney += doubleval($order['payment']);
            $productExpenseMoney += $order['expense_money'];
            $productRefundMonry  +=  $order['total_refund'];
            $productExpenseNums += $orderExpenseNums;    // 产品消费份数
            $productRefundRecordNums += $order['refund_record_nums'];        // 退款总比数 (即凭证管理中的退款记录数)
            $productJuwuyouRefundRecordNums += $order['juwuyou_refund_record_nums']; // 聚无忧退款总比数
            $productManualRefundRecordNums +=   $order['manual_refund_record_nums']; // 人工退款总比数
            $productJuwuyouRefundMoney += $order['juwuyou_refund'];
            $productManualRefundMoney +=  $order['manual_refund'];
            $productIncomingAfter7 +=  $order['incoming_after_7'];          // 7天后退款总收入
            $productRefundRecordTimes += $order['refund_record_times'];
            $productJuwuyouRefundRecordTimes += $order['juwuyou_refund_record_times'];
            $productManualRefundRecordTimes += $order['manual_refund_record_times'];
        }
        // 将产品统计记录回写到产品列表中
        $product['order_nums'] = $productOrderNums;
        $product['order_times'] = $productOrderTimes;
        $product['order_money'] = $productOrderMoney;
        $product['expense_money'] = $productExpenseMoney;
        $product['total_refund'] = $productRefundMonry;
        $product['expense_nums'] = $productExpenseNums;
        $product['juwuyou_refund'] = $productJuwuyouRefundMoney;
        $product['manual_refund'] = $productManualRefundMoney;
        $product['juwuyou_refund_record_nums'] = $productJuwuyouRefundRecordNums;
        $product['manual_refund_record_nums'] = $productManualRefundRecordNums;
        $product['refund_record_nums'] = $productRefundRecordNums;
        $product['incoming_after_7'] = $productIncomingAfter7;
        
        $product['begin_date'] = $beginDate;
        $product['end_date'] = $endDate;
        $product['refund_record_times'] = $productRefundRecordTimes;
        $product['juwuyou_refund_record_times'] = $productJuwuyouRefundRecordTimes;
        $product['manual_refund_record_times'] = $productManualRefundRecordTimes;
        //$product_info = gen_product_info($product);
        //dump_array($product_info);
        //exit;
        // Todo: 展示订单详细列表
        unset($orderList);
    }
    return $productList;
}

function cal_order_used_refund($order_used, $order_unit_price,&$incomingAfter7)
{
    $refund = 0;
    //$useDate = new DateTime($order_used['useDate']);
    // $str_createDate = get_createdate($order_used['usingId']);
    //$createDate = new DateTime($str_createDate);
    $useDateSecs = $order_used['consume_time'];
    $createDateSecs = get_createdate($order_used['platform_order_id']);
    // 由于DateTime的diff方法只有5.3以上的版本支持,所以只能手动计算了
    $intervalDays = round(abs($useDateSecs - $createDateSecs) / (60*60*24));
    // 退款从下订单开始,超过7天要扣%10的钱,再减去1元码费
    if ( $intervalDays <= 7)
    {
        $refund = $order_unit_price*intval($order_used['consume_times']) - 1;
    } else {
        $refund = $order_unit_price*intval($order_used['consume_times'])*0.9 - 1;
        $incomingAfter7 += $order_unit_price*intval($order_used['consume_times'])*0.1;
    }
    return $refund;
}

function get_product_list($platform, $productTitles)
{
    $mysql = new MyTable();
    $selectSql = "SELECT * FROM ".$mysql->get_dbTableName('team')." WHERE title in (";
    foreach ($productTitles as $titles)
    {
        $selectSql .= "'".$titles."',";
    }
    $selectSql = substr($selectSql, 0, -1);
    $selectSql .= ") AND platform_key='".$platform."'";
    $result = $mysql->query($selectSql);
    return mysql_fetch_all_result($result);
}

function get_order_list($platform, $productId, $beginDate = "", $endDate = "")
{
    $mysql = new MyTable();
    $selectSql = "SELECT * FROM ".$mysql->get_dbTableName('order');
    $selectSql .= " WHERE platform_key='".$platform."' AND platform_product_id='".$productId."'";
    if ($beginDate !== "")
    {
        $beginDateSec = strtotime($beginDate);
        $selectSql .= " AND order_create_date >= ".$beginDateSec;
    }
    if ($endDate !== "")
    {
        $endDateSec = strtotime($endDate);
        $selectSql .= " AND order_create_date <= ".$endDateSec;
    }     
    $result = $mysql->query($selectSql);
    return mysql_fetch_all_result($result);

    return $orderList;
}

// yearmonth的格式必须是 "yyyy-mm"
function get_order_used_list($platform, $orderId, $beginDate = "", $endDate = "")
{
    $mysql = new MyTable();
    $selectSql = "SELECT * FROM ".$mysql->get_dbTableName('coupon');
    $selectSql .= " WHERE platform_key='".$platform."' AND platform_order_id='".$orderId."'";
    if ($beginDate !== "")
    {
        $beginDateSec = strtotime($beginDate);
        $selectSql .= " AND consume_time >= ".$beginDateSec;
    }
    if ($endDate !== "")
    {
        $endDateSec = strtotime($endDate);
        $selectSql .= " AND consume_time <= ".$endDateSec;
    }     
    $result = $mysql->query($selectSql);
    return mysql_fetch_all_result($result);
}

// 获得订单购买数量
function get_order_buy_nums($orderId)
{
    $url = "http://59.151.29.121/order/detail.do?20120614150439";
    $params['model.orderId'] = $orderId;
    $method = "POST";
    $details = fetch_detail_info($url, $params, $method);
    $buyNums = intval($details['购买数量']);
    return $buyNums;
}
// 获得创建日期
function get_createdate($orderId)
{
    // $url = "http://59.151.29.121/orderUsed/detail.do";
    // $params['model.usingId'] = $usingId;
    // $method = "POST";
    // $details = fetch_detail_info($url, $params, $method);
    // $str_createDate = $details['创建日期'];
    // return $str_createDate;
    $mysql = new MyTable('order');
    $params = array("platform_record_id"=>$orderId);
    $row = $mysql->get_row($params);
    return $row['order_create_date'];
}

function gen_product_info($product)
{
    $product_keys = array(
        "title" => "产品名称",
        "begin_date" => "统计开始时间",
        "end_date" => "统计结束时间",
        "order_nums" => "总订单数",
        "order_times" => "总订单份数",
        "expense_nums" => "总消费份数",
        "refund_record_times" => "退款总份数",
        "juwuyou_refund_record_times" => "聚无优退款份数",
        "manual_refund_record_times" => "人工退款份数",
        "refund_record_nums" => "退款总比数",
        "juwuyou_refund_record_nums" => "聚无优退款比数",
        "manual_refund_record_nums" => "人工退款比数",
        "order_money" => "总交易金额",
        "expense_money" => "总消费金额",
        "juwuyou_refund" => "聚无忧退款金额",
        "manual_refund" => "人工退款金额",
        "incoming_after_7" => "7天后退款总收入",
    );
    $product_info = array();
    foreach ($product_keys as $key=>$value)
    {
        $product_info[$value] = $product[$key];
    }
    return $product_info;
}

function dump_array($data)
{
    foreach($data as $key => $value)
    {
        echo $key.":&nbsp;".$value."<br />";
    }
}
function gen_dump_content($data)
{
    $content = "";
    foreach($data as $key => $value)
    {
         $content .= $key.":&nbsp;".$value."<br />";
    }
    return $content;
}
/**
 * [parse_titles description]
 * @param  string $str_titles 多个标题,用','隔开
 * @return string title 数组
 */
function parse_titles($str_titles)
{
    if ($str_titles === "")
    {
        return array();
    }
    return split(",", $str_titles);
}
?>
