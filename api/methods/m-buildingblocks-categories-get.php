<?php
$route = '/buildingblocks/categories/';
$app->get($route, function ()  use ($app){

	$ReturnObject = array();
	
 	$request = $app->request(); 
 	$params = $request->params();	

	$Query = "SELECT * from categories t";
	$Query .= " INNER JOIN buildingblock_category_pivot btp ON t.Tag_ID = btp.Tag_ID";
	$Query .= " GROUP BY t.Tag ORDER BY count(*) DESC";

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