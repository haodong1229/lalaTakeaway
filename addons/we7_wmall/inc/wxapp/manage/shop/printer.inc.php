<?php 








defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
mload()->model("print");
$ta = (trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list");
if( $ta == "list" ) 
{
    $data = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_printer") . " WHERE uniacid = :uniacid AND sid = :sid", array( ":uniacid" => $_W["uniacid"], ":sid" => $sid ));
    if( !empty($data) ) 
    {
        $types = print_printer_types();
        foreach( $data as &$da ) 
        {
            $da["type_cn"] = $types[$da["type"]]["text"];
            if( !empty($da["print_no"]) ) 
            {
                if( in_array($da["type"], array( "feie", "feie_new", "365", "365_s2" )) ) 
                {
                    $da["status_cn"] = print_query_printer_status($da);
                }
                else
                {
                    $da["status_cn"] = "打印机不支持查询状态";
                }

            }
            else
            {
                $da["status_cn"] = "未知";
            }

        }
    }

    $result = array( "records" => $data );
    imessage(error(0, $result), "", "ajax");
}

if( $ta == "post" ) 
{
    $id = intval($_GPC["id"]);
    if( 0 < $id ) 
    {
        $item = pdo_fetch("SELECT * FROM " . tablename("tiny_wmall_printer") . " WHERE uniacid = :uniacid AND id = :id", array( ":uniacid" => $_W["uniacid"], ":id" => $id ));
    }

    if( !empty($item) ) 
    {
        if( !empty($item["print_label"]) ) 
        {
            $item["print_label"] = explode(",", $item["print_label"]);
            if( is_array($item["print_label"]) ) 
            {
                $item["print_label_type"] = 1;
            }
            else
            {
                $item["print_label_type"] = 0;
            }

        }
        else
        {
            $item["print_label"] = array(  );
        }

        $item["data"] = iunserializer($item["data"]);
    }

    if( $_W["ispost"] ) 
    {
        $printer = $_GPC["data"];
        $data["uniacid"] = $_W["uniacid"];
        $data["sid"] = $sid;
        $data["type"] = trim($printer["type"]);
        $data["status"] = intval($printer["status"]);
        $data["name"] = trim($printer["name"]);
        $data["print_no"] = trim($printer["print_no"]);
        $data["key"] = trim($printer["key"]);
        $data["api_key"] = trim($printer["api_key"]);
        $data["member_code"] = trim($printer["member_code"]);
        if( $printer["type"] == "yilianyun" || $printer["type"] == "qiyun" ) 
        {
            $data["member_code"] = trim($printer["userid"]);
        }

        $data["print_nums"] = (intval($printer["print_nums"]) ? intval($printer["print_nums"]) : 1);
        $data["qrcode_type"] = trim($printer["qrcode_type"]);
        $data["qrcode_link"] = "";
        if( !empty($printer["qrcode_link"]) && (strexists($printer["qrcode_link"], "http://") || strexists($printer["qrcode_link"], "https://")) ) 
        {
            $data["qrcode_link"] = trim($printer["qrcode_link"]);
        }

        $data["print_header"] = trim($printer["print_header"]);
        $data["print_footer"] = trim($printer["print_footer"]);
        $data["is_print_all"] = intval($printer["is_print_all"]);
        $data["print_label"] = 0;
        if( $printer["print_label_type"] == 1 && !empty($printer["print_label"]) ) 
        {
            $print_label = array(  );
            foreach( $printer["print_label"] as $label ) 
            {
                if( 0 < $label ) 
                {
                    $print_label[] = $label;
                }

            }
            if( !empty($print_label) ) 
            {
                $data["print_label"] = implode(",", $print_label);
            }

        }

        $data["data"] = array(  );
        if( !empty($item) && !empty($item["data"]) ) 
        {
            $data["data"] = $item["data"];
        }

        if( check_plugin_exist("svip") ) 
        {
            $data["data"]["format"] = array( "goods_title" => array( "font_size" => trim($printer["data"]["format"]["goods_title"]["font_size"]) ), "note" => array( "font_size" => trim($printer["data"]["format"]["note"]["font_size"]) ) );
        }

        $data["data"] = iserializer($data["data"]);
        if( !empty($item) && $id ) 
        {
            pdo_update("tiny_wmall_printer", $data, array( "uniacid" => $_W["uniacid"], "id" => $id ));
        }
        else
        {
            pdo_insert("tiny_wmall_printer", $data);
        }

        imessage(error(0, "更新打印机设置成功"), "", "ajax");
    }

    $print_labels = pdo_fetchall("select * from " . tablename("tiny_wmall_printer_label") . " where uniacid = :uniacid and sid = :sid order by displayorder desc, id asc", array( ":uniacid" => $_W["uniacid"], ":sid" => $sid ));
    $result = array( "print_labels" => $print_labels, "data" => $item );
    imessage(error(0, $result), "", "ajax");
}

if( $ta == "del" ) 
{
    $id = intval($_GPC["id"]);
    pdo_delete("tiny_wmall_printer", array( "uniacid" => $_W["uniacid"], "id" => $id ));
    imessage(error(0, "删除打印机成功"), "", "ajax");
}
?>