<?php  defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
$ta = (trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list");
if( $ta == "list" ) 
{
	$condition = " where a.uniacid = :uniacid and credittype = :credittype";
	$params = array( ":uniacid" => $_W["uniacid"], ":credittype" => "credit1" );
	$keyword = trim($_GPC["keyword"]);
	if( !empty($keyword) ) 
	{
		$condition .= " and (a.uid = :uid or b.realname like :keyword)";
		$params[":uid"] = $keyword;
		$params[":keyword"] = "%" . $keyword . "%";
	}
	$page = max(1, intval($_GPC["page"]));
	$psize = (intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 15);
	$records = pdo_fetchall("select a.*, b.avatar,b.nickname,b.realname,b.mobile from " . tablename("mc_credits_record") . " as a left join " . tablename("tiny_wmall_members") . " as b on a.uid = b.uid " . $condition . " order by a.id desc LIMIT " . ($page - 1) * $psize . "," . $psize, $params);
	if( !empty($records) ) 
	{
		foreach( $records as &$val ) 
		{
			$val["createtime_cn"] = date("Y-m-d H:i", $val["createtime"]);
			$val["avatar"] = tomedia($val["avatar"]);
		}
	}
	$result = array( "records" => $records );
	imessage(error(0, $result), "", "ajax");
}
?>