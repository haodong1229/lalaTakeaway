<?php 
defined("IN_IA") or exit( "Access Denied" );

/*淘宝柠檬鱼科技 https://shop486845690.taobao.com*/

abstract class TyAccount
{
    public static function create($acidOrAccount = "", $type = "wechat")
    {
        global $_W;
        if( $type != "wxapp" ) 
        {
            mload()->classs("wxaccount");
            $acc = new WxAccount($acidOrAccount);
            return $acc;
        }

        if( empty($acidOrAccount) ) 
        {
            $acidOrAccount = $_W["acid"];
        }

        if( is_array($acidOrAccount) ) 
        {
            $account = $acidOrAccount;
        }
        else
        {
            $wxapp = get_plugin_config("wxapp.basic");
            $account = array( "key" => $wxapp["key"], "secret" => $wxapp["secret"] );
        }

        mload()->classs("wxapp");
        return new Wxapp($account);
    }

}


