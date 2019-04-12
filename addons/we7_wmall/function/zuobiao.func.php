<?php  defined("IN_IA") or exit( "Access Denied" );
function is_in_two_point($point1, $point2, $point3, $quadrant = false) 
{
	$diff_lng = $point1[0] - $point2[0];
	$diff_lat = $point1[1] - $point2[1];
	if( $quadrant && 0 < $diff_lng * ($point3[0] - $point2[0]) && 0 < $diff_lat * ($point3[1] - $point2[1]) ) 
	{
		return true;
	}
	$lng_in = $lat_in = false;
	if( 0 < $diff_lng && $point2[0] <= $point3[0] && $point3[0] <= $point1[0] ) 
	{
		$lng_in = true;
	}
	else 
	{
		if( $diff_lng < 0 && $point1[0] <= $point3[0] && $point3[0] <= $point2[0] ) 
		{
			$lng_in = true;
		}
	}
	if( 0 < $diff_lat && $point2[1] <= $point3[1] && $point3[1] <= $point1[1] ) 
	{
		$lat_in = true;
	}
	else 
	{
		if( $diff_lat < 0 && $point1[1] <= $point3[1] && $point3[1] <= $point2[1] ) 
		{
			$lat_in = true;
		}
	}
	if( $lat_in && $lng_in || $diff_lat == 0 && $lng_in || $diff_lng == 0 && $lat_in ) 
	{
		return true;
	}
	return false;
}
function is_points_in_identical_side($point1, $point2, $point3, $point4, $vector = false) 
{
	$slope = ($point1[0] - $point2[0]) / ($point1[1] - $point2[1]);
	$same_direction = true;
	if( $vector ) 
	{
		$same_direction = 0 <= ($point1[0] - $point2[0]) * ($point3[0] - $point4[0]);
	}
	if( 0 < ($slope * $point3[1] - $point3[0]) * ($slope * $point4[1] - $point4[0]) && $same_direction ) 
	{
		return true;
	}
	return false;
}
function is_in_identical_direction($reference, $judged) 
{
	$in_quadrant_accept = is_in_two_point($reference["destination"], $reference["origin"], $judged["destination"], true);
	$in_quadrant_origin = is_in_two_point($reference["destination"], $reference["origin"], $judged["origin"]);
	if( $in_quadrant_accept && $in_quadrant_origin ) 
	{
		$in_identical_direction = is_points_in_identical_side($reference["destination"], $reference["origin"], $judged["destination"], $judged["origin"], true);
		if( $in_identical_direction ) 
		{
			return true;
		}
	}
	return false;
}
?>