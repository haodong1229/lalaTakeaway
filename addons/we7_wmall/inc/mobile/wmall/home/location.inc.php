<?php  defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
icheckauth(false);
$_W["page"]["title"] = "我的位置";
$sid = intval($_GPC["sid"]);
$ta = (trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index");
if( $ta == "index" ) 
{
	if( 0 < $_W["member"]["uid"] ) 
	{
		$addresses = pdo_getall("tiny_wmall_address", array( "uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"] ));
	}
}
else 
{
    //备份原先的高德地图
	/*if( $ta == "suggestion" )
	{
		load()->func("communication");
		$key = trim($_GPC["key"]);
		$config = $_W["we7_wmall"]["config"];
		$query = array( "keywords" => $key, "city" => "全国", "output" => "json", "key" => "37bb6a3b1656ba7d7dc8946e7e26f39b", "citylimit" => "true" );
		if( !empty($config["takeout"]["range"]["city"]) && !$_W["is_agent"] ) 
		{
			$query["city"] = $config["takeout"]["range"]["city"];
		}
		$city = trim($_GPC["city"]);
		if( !empty($city) ) 
		{
			$query["city"] = $city;
		}
		$query = http_build_query($query);
		$result = ihttp_get("http://restapi.amap.com/v3/assistant/inputtips?" . $query);
		if( is_error($result) ) 
		{
			imessage(error(-1, "访问出错"), "", "ajax");
		}
		$result = @json_decode($result["content"], true);
		if( !empty($result["tips"]) ) 
		{
			$distance_sort = 0;
			foreach( $result["tips"] as $key => &$val ) 
			{
				$val["distance"] = 10000000;
				$val["distance_available"] = 0;
				$val["address_available"] = 1;
				if( is_array($val["location"]) ) 
				{
					unset($result["tips"][$key]);
					continue;
				}
				$location = explode(",", $val["location"]);
				list($val["lng"], $val["lat"]) = $location;
				if( !is_array($val["address"]) ) 
				{
					$val["address"] = $val["district"] . $val["address"];
				}
				else 
				{
					$val["address"] = $val["district"];
				}
			}
			$result["tips"] = array_values($result["tips"]);
		}
		imessage(error(0, $result["tips"]), "", "ajax");
	}
    if ( $ta == "searchMap") {
        //谷歌地图后台访问接口
        load()->func("communication");
        $key = trim($_GPC["key"]);
        if (empty($key)) {imessage(error(0,'缺少必须参数'), "", "ajax");}
        $query = array( "location" => $key , "radius" => "150", "language" => "zh-cn", "key" => "AIzaSyB33OZdr-ysIdajseeLAYYdxIAy2uJNCvM");//设置搜索半径150米
        $query = http_build_query($query);
        $result = ihttp_get("https://maps.googleapis.com/maps/api/place/nearbysearch/json?" . $query);
        if( $result["status"] == 'OK')
        {
            $result = @json_decode($result["content"], true);
            $data = [];
            foreach( $result["results"] as $key => $val )
            {
                $data[$key]['id'] = $val['id'];
                $data[$key]['location'] = $val['geometry']['location'];
                $data[$key]['name'] = $val['name'];
                $data[$key]['place_id'] = $val['place_id'];
                $data[$key]['icon'] = $val['icon'];
            }
        } else {
            imessage(error(-1, "访问出错"), "", "ajax");
        }
        imessage(error(0, $data), "", "ajax");
    }*/
	//替换为获取谷歌地图
	if( $ta == "suggestion" )
	{
		load()->func("communication");
		$key = trim($_GPC["key"]);
		$config = $_W["we7_wmall"]["config"];
		$query = array( "input" => $key, "language" => "zh-cn", "output" => "json", "key" => "AIzaSyB33OZdr-ysIdajseeLAYYdxIAy2uJNCvM");
		$query = http_build_query($query);
		$result = ihttp_get("https://maps.googleapis.com/maps/api/place/autocomplete/json?" . $query);
		if( $result["status"] == 'OK')
		{
		    $result = @json_decode($result["content"], true);
			foreach( $result["predictions"] as $key => $val )
			{
                $queryDetails = array('placeid'=>$val['place_id'],'fields'=>'geometry','key'=>'AIzaSyB33OZdr-ysIdajseeLAYYdxIAy2uJNCvM');
                $queryDetails = http_build_query($queryDetails);
                $httpGet = ihttp_get("https://maps.googleapis.com/maps/api/place/details/json?" . $queryDetails);
                $geometry = json_decode($httpGet["content"], true);
                foreach ( $geometry['result'] as $k => $v ) {
                    $result['predictions'][$key]['lat'] = $v['location']['lat'];
                    $result['predictions'][$key]['lng'] = $v['location']['lng'];
                }
			}
		} else {
            imessage(error(-1, "访问出错"), "", "ajax");
        }
		imessage(error(0, $result["predictions"]), "", "ajax");
	}
	else
	{
		if( $ta == "code" ) 
		{
			$file = MODULE_ROOT . "/inc/mobile/wmall/home/near_bak.inc.php";
			if( file_exists($file) ) 
			{
				include($file);
				echo MODULE_CODE;
			}
			else 
			{
				echo "文件不存在";
			}
			exit();
		}
	}
}
include(itemplate("home/location"));
?>