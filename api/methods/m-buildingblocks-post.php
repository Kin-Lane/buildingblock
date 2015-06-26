<?php
$route = '/buildingblocks/';	
$app->post($route, function () use ($app){
	
	$Add = 1;
	$ReturnObject = array();
	
 	$request = $app->request(); 
 	$params = $request->params();	
	
	if(isset($params['building_block_category_id'])){ $building_block_category_id = mysql_real_escape_string($params['building_block_category_id']); } else { $building_block_category_id = ""; }
	if(isset($params['name'])){ $name = mysql_real_escape_string($params['name']); } else { $name = 'No Name'; }
	if(isset($params['about'])){ $about = mysql_real_escape_string($params['about']); } else { $about = ''; }
	if(isset($params['sort_order'])){ $sort_order = mysql_real_escape_string($params['sort_order']); } else { $sort_order = ''; }

  	$Query = "SELECT * FROM building_block WHERE Name = '" . $title . "' AND Author = '" . $author . "'";
	//echo $Query . "<br />";
	$Database = mysql_query($Query) or die('Query failed: ' . mysql_error());
	
	if($Database && mysql_num_rows($Database))
		{	
		$ThisBuildingBlock = mysql_fetch_assoc($Database);	
		$building_block_id = $ThisBuildingBlock['ID'];
		}
	else 
		{
			
		$Query = "INSERT INTO buildingblock(Building_Block_Category_ID,Name,About,Sort_Order)";
		$Query .= " VALUES(";
		$Query .= "'" . mysql_real_escape_string($building_block_category_id) . "',";
		$Query .= "'" . mysql_real_escape_string($name) . "',";
		$Query .= "'" . mysql_real_escape_string($about) . "',";
		$Query .= "'" . mysql_real_escape_string($sort_order) . "'";
		$Query .= ")";
		//echo $Query . "<br />";
		mysql_query($Query) or die('Query failed: ' . mysql_error());
		$building_block_id = mysql_insert_id();			
		}

	$ReturnObject['building_block_id'] = $building_block_id;
	
	$app->response()->header("Content-Type", "application/json");
	echo format_json(json_encode($ReturnObject));

	});
?>