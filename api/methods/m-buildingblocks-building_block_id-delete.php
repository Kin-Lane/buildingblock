<?php			
$route = '/buildingblocks/:building_block_id/';	
$app->delete($route, function ($building_block_id) use ($app){
	
	$Add = 1;
	$ReturnObject = array();
	
 	$request = $app->request(); 
 	$_POST = $request->params();	

	$query = "DELETE FROM building_block WHERE ID = " . $building_block_id;
	//echo $query . "<br />";
	mysql_query($query) or die('Query failed: ' . mysql_error());	

	});			
?>