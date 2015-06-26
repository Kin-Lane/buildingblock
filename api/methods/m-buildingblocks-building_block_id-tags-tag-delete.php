<?php
$route = '/buildingblocks/:building_block_id/tags/:tag/';
$app->delete($route, function ($building_block_id,$tag)  use ($app){

	$ReturnObject = array();
		
 	$request = $app->request(); 
 	$param = $request->params();	
	
	if($tag != '')
		{
	
		$building_block_id = trim(mysql_real_escape_string($building_block_id));
		$tag = trim(mysql_real_escape_string($tag));
	
		$CheckTagQuery = "SELECT Tag_ID FROM tags where Tag = '" . $tag . "'";
		$CheckTagResults = mysql_query($CheckTagQuery) or die('Query failed: ' . mysql_error());		
		if($CheckTagResults && mysql_num_rows($CheckTagResults))
			{
			$Tag = mysql_fetch_assoc($CheckTagResults);		
			$tag_id = $Tag['Tag_ID'];

			$DeleteQuery = "DELETE FROM building_block_tag_pivot where Tag_ID = " . trim($tag_id) . " AND BuildingBlock_ID = " . trim($building_block_id);
			$DeleteResult = mysql_query($DeleteQuery) or die('Query failed: ' . mysql_error());
			}

		$F = array();
		$F['tag_id'] = $tag_id;
		$F['tag'] = $tag;
		$F['buildingblock_count'] = 0;
		
		array_push($ReturnObject, $F);

		}		

		$app->response()->header("Content-Type", "application/json");
		echo format_json(json_encode($ReturnObject));
	});
?>