<?php
$route = '/buildingblocks/';
$app->get($route, function ()  use ($app){

	$ReturnObject = array();

 	$request = $app->request(); 
 	$params = $request->params();	

	if(isset($params['query'])){ $query = trim(mysql_real_escape_string($params['query'])); } else { $query = '';}
	if(isset($params['page'])){ $page = trim(mysql_real_escape_string($params['page'])); } else { $page = 0;}
	if(isset($params['count'])){ $count = trim(mysql_real_escape_string($params['count'])); } else { $count = 250;}
	if(isset($params['sort'])){ $sort = trim(mysql_real_escape_string($params['sort'])); } else { $sort = 'Name';}
	if(isset($params['order'])){ $order = trim(mysql_real_escape_string($params['order'])); } else { $order = 'DESC';}	
			

	$SearchQuery = "SELECT b.Building_Block_ID,b.Building_Block_Category_ID,b.Name,b.About,b.Sort_Order,bbc.Name AS Category,bbc.Type as Type FROM building_block b";
	$SearchQuery .= " JOIN building_block_category bbc ON b.Building_Block_Category_ID = bbc.BuildingBlockCategory_ID";

	if($query!='')
		{
		$SearchQuery .= " WHERE b.Name LIKE '%" . $query . "%'";
		}
		
	$SearchQuery .= " ORDER BY " . $sort . " " . $order . " LIMIT " . $page . "," . $count;
	//echo $SearchQuery . "<br />";
	
	$DatabaseResult = mysql_query($SearchQuery) or die('Query failed: ' . mysql_error());
	  
	while ($Database = mysql_fetch_assoc($DatabaseResult))
		{

		$building_block_id = $Database['Building_Block_ID'];
		$building_block_category_id = $Database['Building_Block_Category_ID'];
		$name = $Database['Name'];
		$about = $Database['About'];
		$category = $Database['Category'];
		$category_id = $Database['Building_Block_Category_ID'];
		$sort_order = $Database['Sort_Order'];	
				
		// manipulation zone
		
		$F = array();
		$F['building_block_id'] = $building_block_id;
		$F['building_block_category_id'] = $building_block_category_id;
		$F['name'] = $name;
		$F['about'] = $about;
		$F['category_id'] = $category_id;
		$F['category'] = $category;
		$F['sort_order'] = $sort_order;
		
		array_push($ReturnObject, $F);
		}

		$app->response()->header("Content-Type", "application/json");
		echo format_json(json_encode($ReturnObject));
	});
?>