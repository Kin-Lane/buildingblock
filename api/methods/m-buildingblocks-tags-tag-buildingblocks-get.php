<?php
$route = '/buildingblocks/tags/:tag/buildingblocks/';
$app->get($route, function ($tag)  use ($app){

	$ReturnObject = array();
	
 	$request = $app->request(); 
 	$params = $request->params();		

	if(isset($_REQUEST['week'])){ $week = $params['week']; } else { $week = date('W'); }
	if(isset($_REQUEST['year'])){ $year = $params['year']; } else { $year = date('Y'); }	

	$Query = "SELECT b.* from tags t";
	$Query .= " JOIN buildingblock_tag_pivot btp ON t.Tag_ID = btp.Tag_ID";
	$Query .= " JOIN buildingblock b ON btp.BuildingBlock_ID = b.ID";
	$Query .= " WHERE WEEK(b.Post_Date) = " . $week . " AND YEAR(b.Post_Date) = " . $year . " AND Tag = '" . $tag . "'";

	$DatabaseResult = mysql_query($Query) or die('Query failed: ' . mysql_error());
	  
	while ($Database = mysql_fetch_assoc($DatabaseResult))
		{

		$building_block_id = $Database['Building_Block_ID'];
		$building_block_category_id = $Database['Building_Block_Category_ID'];
		$name = $Database['Name'];
		$about = $Database['About'];
		$sort_order = $Database['Sort_Order'];	
				
		// manipulation zone

		$host = $_SERVER['HTTP_HOST'];
		$building_block_id = prepareIdOut($building_block_id,$host);
		$building_block_category_id = prepareIdOut($building_block_category_id,$host);
		
		$F = array();
		$F['building_block_id'] = $building_block_id;
		$F['building_block_category_id'] = $building_block_category_id;
		$F['name'] = $name;
		$F['about'] = $about;
		$F['sort_order'] = $sort_order;
		
		array_push($ReturnObject, $F);
		}

		$app->response()->header("Content-Type", "application/json");
		echo format_json(json_encode($ReturnObject));
	});
?>