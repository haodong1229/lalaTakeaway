<?php 
defined("IN_IA") or exit( "Access Denied" );
pload()->classs("dada");

/*淘宝柠檬鱼科技 https://shop486845690.taobao.com*/

class subscribe extends DaDa
{
    public $notice = "";

    public function buildSign($param)
    {
        $params = array( "client_id" => "", "order_id" => $param["order_id"], "update_time" => $param["update_time"] );
        $val = array_values($params);
        sort($val);
        $str = "";
        foreach( $val as $v ) 
        {
            $str .= $v;
        }
        $sign = md5($str);
        return $sign;
    }

    public function checkSign($param)
    {
        $signature = $param["signature"];
        unset($param["signature"]);
        if( $signature != $this->buildSign($param) ) 
        {
            return false;
        }

        return true;
    }

    public function start()
    {
        if( strtolower($_SERVER["REQUEST_METHOD"]) == "post" ) 
        {
            $postStr = file_get_contents("php://input");
            $postStr = json_decode($postStr, true);
            if( !$this->checkSign($postStr) ) 
            {
                exit( "Check Sign Fail." );
            }

            $this->notice = $postStr;
            $this->parse();
        }

    }

    public function parse()
    {
        global $_W;
        $ordersn = $this->notice["order_id"];
        $order = pdo_get("tiny_wmall_order", array( "ordersn" => $ordersn ), array( "id", "sid" ));
        if( empty($order) ) 
        {
            exit( "order is not exit" );
        }

        $statusDd = $this->notice["order_status"];
        if( $statusDd == 2 ) 
        {
            $deliveryer = array( "id" => 0, "title" => $this->notice["dm_name"], "mobile" => $this->notice["dm_mobile"] );
            order_deliveryer_update_status($order["id"], "delivery_assign", array( "role" => "dada", "deliveryer" => $deliveryer ));
        }
        else
        {
            if( $statusDd == 3 ) 
            {
                order_deliveryer_update_status($order["id"], "delivery_takegoods", array( "role" => "dada" ));
            }
            else
            {
                if( $statusDd == 4 ) 
                {
                    order_status_update($order["id"], "end", array( "role" => "dada" ));
                }
                else
                {
                    if( $statusDd == 5 ) 
                    {
                        order_status_update($order["id"], "cancel", array( "role" => "dada", "note" => $this->notice["cancel_reason"] ));
                    }
                    else
                    {
                        if( $statusDd == 7 ) 
                        {
                        }
                        else
                        {
                            if( $statusDd == 8 ) 
                            {
                            }
                            else
                            {
                                if( $statusDd == 9 ) 
                                {
                                }
                                else
                                {
                                    if( $statusDd == 10 ) 
                                    {
                                    }
                                    else
                                    {
                                        if( $statusDd == 1000 ) 
                                        {
                                        }

                                    }

                                }

                            }

                        }

                    }

                }

            }

        }

        echo "{\"message\":\"ok\"}";
        exit();
    }

}


