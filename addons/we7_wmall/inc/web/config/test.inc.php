<?php 



defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
mload()->classs("wxpay");
$wxpay = new WxPay();
if( 1 ) 
{
    $params = array( "partner_trade_no" => 201801091224 );
    $result = $wxpay->mktQueryBank($params);
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    exit();
}

$params = array( "partner_trade_no" => "201801091906", "enc_bank_no" => "6227000262110145108", "enc_true_name" => "李垚儒", "bank_code" => "1003", "amount" => 1, "desc" => "微信转账到银行卡测试" );
$result = $wxpay->mktPayBank($params);
echo "<pre>";
print_r($result);
echo "</pre>";
?>