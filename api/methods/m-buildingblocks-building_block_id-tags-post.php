<?php
$route = '/buildingblocks/:building_block_id/tags/';
$app->post($route, function ($building_block_id)  use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$building_block_id = prepareIdIn($building_block_id,$host);

	$ReturnObject = array();
		
 	$request = $app->request(); 
 	$param = $request->params();	
	
	if(isset($param['tag']))
		{
		$tag = trim(mysql_real_escape_string($param['tag']));
			
		$CheckTagQuery = "SELECT Tag_ID FROM tags where Tag = '" . $tag . "'";
		$CheckTagResults = mysql_query($CheckTagQuery) or die('Query failed: ' . mysql_error());		
		if($CheckTagResults && mysql_num_rows($CheckTagResults))
			{
			$Tag = mysql_fetch_assoc($CheckTagResults);		
			$tag_id = $Tag['Tag_ID'];
			}
		else
			{

			$query = "INSERT INTO tags(Tag) VALUES('" . trim($_POST['Tag']) . "'); ";
			mysql_query($query) or die('Query failed: ' . mysql_error());	
			$tag_id = mysql_insert_id();			
			}

		$CheckTagPivotQuery = "SELECT * FROM building_block_tag_pivot where Tag_ID = " . $tag_id . " AND BuildingBlock_ID = " . trim($building_block_id);
		$CheckTagPivotResult = mysql_query($CheckTagPivotQuery) or die('Query failed: ' . mysql_error());
		
		if($CheckTagPivotResult && mysql_num_rows($CheckTagPivotResult))
			{
			$CheckTagPivot = mysql_fetch_assoc($CheckTagPivotResult);		
			}
		else
			{
			$query = "INSERT INTO buildingblock_tag_pivot(Tag_ID,BuildingBlock_ID) VALUES(" . $tag_id . "," . $building_block_id . "); ";
			mysql_query($query) or die('Query failed: ' . mysql_error());					
			}

		$building_block_id = prepareIdOut($building_block_id,$host);
		$tag_id = prepareIdOut($tag_id,$host);

		$F = array();
		$F['building_block_id'] = $building_block_id;
		$F['tag_id'] = $tag_id;
		$F['tag'] = $tag;
		$F['buildingblock_count'] = 0;
		
		array_push($ReturnObject, $F);

		}		

		$app->response()->header("Content-Type", "application/json");
		echo format_json(json_encode($ReturnObject));
	});
?>