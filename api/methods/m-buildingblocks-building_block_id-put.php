<?php
$route = '/buildingblocks/:building_block_id/';	
$app->put($route, function ($building_block_id) use ($app){
		
	$ReturnObject = array();
	
 	$request = $app->request(); 
 	$params = $request->params();		
	
	if(isset($params['building_block_category_id'])){ $building_block_category_id = mysql_real_escape_string($params['building_block_category_id']); } else { $building_block_category_id = ""; }
	if(isset($params['name'])){ $name = mysql_real_escape_string($params['name']); }
	if(isset($params['about'])){ $about = mysql_real_escape_string($params['about']); } else { $about = ''; }
	if(isset($params['sort_order'])){ $sort_order = mysql_real_escape_string($params['sort_order']); }

  	$Query = "SELECT * FROM building_block WHERE ID = " . $building_block_id;
	//echo $Query . "<br />";
	$Database = mysql_query($Query) or die('Query failed: ' . mysql_error());
	
	if($Database && mysql_num_rows($Database))
		{	
		$query = "UPDATE buildingblock SET";

		$query .= " Building_Block_Category_ID = '" . mysql_real_escape_string($building_block_category_id) . "'";
		$query .= ", Name = '" . mysql_real_escape_string($name) . "',";
		
		if($post_date!='') { $query .= ", About = '" . $about . "',"; }
		if($author!='') { $query .= ", Sort_Order = '" . $sort_order . "',"; }
		
		$query .= ", Closing = 'nothing'";
		
		$query .= " WHERE Building_Block_ID = '" . $building_block_id . "'";
		
		//echo $query . "<br />";
		mysql_query($query) or die('Query failed: ' . mysql_error());	
		}

	$F = array();
	$F['building_block_id'] = $building_block_id;
	$F['building_block_category_id'] = $building_block_category_id;
	$F['name'] = $name;
	$F['about'] = $about;
	$F['sort_order'] = $sort_order;
	
	array_push($ReturnObject, $F);
		
	$app->response()->header("Content-Type", "application/json");
	echo stripslashes(format_json(json_encode($ReturnObject)));

	});
?>