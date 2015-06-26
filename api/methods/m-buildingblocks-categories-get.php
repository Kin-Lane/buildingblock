<?php
$route = '/buildingblocks/categories/';
$app->get($route, function ()  use ($app){

	$ReturnObject = array();
	
 	$request = $app->request(); 
 	$params = $request->params();	

	$Query = "SELECT t.Tag_ID, t.Tag, count(*) AS API_Count from categories t";
	$Query .= " INNER JOIN buildingblock_category_pivot btp ON t.Tag_ID = btp.Tag_ID";
	$Query .= " GROUP BY t.Tag ORDER BY count(*) DESC";

	$DatabaseResult = mysql_query($Query) or die('Query failed: ' . mysql_error());
	  
	while ($Database = mysql_fetch_assoc($DatabaseResult))
		{

		$category_id = $Database['Tag_ID'];
		$category = $Database['Tag'];
		$buildingblock_count = $Database['BuildingBlock_Count'];

		$F = array();
		$F['category_id'] = $category_id;
		$F['category'] = $category;
		$F['buildingblock_count'] = $buildingblock_count;
		
		array_push($ReturnObject, $F);
		}

		$app->response()->header("Content-Type", "application/json");
		echo format_json(json_encode($ReturnObject));
	});
?>