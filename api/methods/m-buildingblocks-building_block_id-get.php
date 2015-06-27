<?php
$route = '/buildingblocks/:building_block_id/';
$app->get($route, function ($building_block_id)  use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$building_block_id = prepareIdIn($building_block_id,$host);

	$ReturnObject = array();
		
	$Query = "SELECT * FROM building_block WHERE Building_Block_ID = " . $building_block_id;
	
	$DatabaseResult = mysql_query($Query) or die('Query failed: ' . mysql_error());
	  
	while ($Database = mysql_fetch_assoc($DatabaseResult))
		{
						
		$building_block_id = $Database['Building_Block_ID'];
		$building_block_category_id = $Database['Building_Block_Category_ID'];
		$name = $Database['Name'];
		$about = $Database['About'];
		$sort_order = $Database['Sort_Order'];	
				
		// manipulation zone

		$building_block_id = prepareIdOut($building_block_id,$host);
		$building_block_category_id = prepareIdOut($building_block_category_id,$host);
		
		$F = array();
		$F['building_block_id'] = $building_block_id;
		$F['building_block_category_id'] = $building_block_category_id;
		$F['name'] = $name;
		$F['about'] = $about;
		$F['sort_order'] = $sort_order;
		
		$ReturnObject = $F;
		}

		$app->response()->header("Content-Type", "application/json");
		echo format_json(json_encode($ReturnObject));
	});
?>