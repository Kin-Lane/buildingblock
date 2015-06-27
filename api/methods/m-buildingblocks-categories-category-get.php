<?php
$route = '/buildingblocks/categories/:category';
$app->delete($route, function ($category)  use ($app){

	$ReturnObject = array();
	
 	$request = $app->request(); 
 	$params = $request->params();	

	$Query = "SELECT * FROM building_blockcategory WHERE Name = '" . trim(mysql_real_escape_string($category)) . "'";

	$CategoryResult = mysql_query($Query) or die('Query failed: ' . mysql_error());
		
	if($CategoryResult && mysql_num_rows($CategoryResult))
		{	
		$Tag = mysql_fetch_assoc($TagResult);
		$building_block_category_id = $Tag['BuildngBlockCategory_ID'];
		$name = $Tag['Name'];
		$sort_order = $Tag['Sort_Order'];
		$type = $Tag['Type'];

		$host = $_SERVER['HTTP_HOST'];
		$building_block_category_id = prepareIdOut($building_block_category_id,$host);

		$F = array();
		$F['building_block_category_id'] = $building_block_category_id;
		$F['name'] = $name;
		$F['sort_order'] = $sort_order;
		$F['type'] = $type;
		
		array_push($ReturnObject, $F);
		}

		$app->response()->header("Content-Type", "application/json");
		echo format_json(json_encode($ReturnObject));
	});
?>