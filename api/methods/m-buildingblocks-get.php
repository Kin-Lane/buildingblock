<?php
$route = '/buildingblocks/';
$app->get($route, function ()  use ($app,$contentType,$githuborg,$githubrepo){

	$ReturnObject = array();

 	$request = $app->request();
 	$params = $request->params();
	echo $contentType . "<br />";
	if($contentType == 'application/apis+json')
		{
		$app->response()->header("Content-Type", "application/json");

		$apis_json_url = "http://" . $githuborg . ".github.io/" . $githubrepo . "/apis.json";
		$apis_json = file_get_contents($apis_json_url);
		echo stripslashes(format_json($apis_json));
		}
	elseif($contentType == 'application/vnd.siren+json')
		{
		$app->response()->header("Content-Type", "application/vnd.siren+json");

		$ReturnObject['rel'] = new stdClass();
		$ReturnObject['rel'] = "urn:x-resource:schema:https://kin-lane.github.io/buildingblock/schemas/buildingblocks.json";

		// Properties
		$ReturnObject['properties'] = array();
		$ReturnObject['properties']['totalItems'] = 0;
		$ReturnObject['properties']['currentCount'] = 0;
		$ReturnObject['properties']['nextMaxId'] = 0;

		// Entities
		$ReturnObject['entities'] = new stdClass();

		$E = array();
		$Entities = array();
		$Entities['rel'] = new stdClass();

		$Entities_rel = array();
		$Entities_rel[0] = "properties:https://kin-lane.github.io/buildingblock/schemas/buildingblocks.json";
		$Entities_rel[0] = "urn:x-resource:name:buildingblock";
		$Entities['rel'] = $Entities_rel;

		$Entities['class'] = new stdClass();
		$Entities['class'] = "buildingblock";

		$Entities['properties'] = array();

		$Entities['properties']['id'] = 0;
		$Entities['properties']['name'] = "";
		$Entities['properties']['about'] = "";
		$Entities['properties']['post_date'] = "";
		$Entities['properties']['sort_order'] = 0;
		$Entities['properties']['url'] = "";

		$Entities['properties']['category'] = array();
		$Entities['properties']['category']['id'] = 0;
		$Entities['properties']['category']['name'] = "";
		$Entities['properties']['category']['sort_order'] = 0;
		$Entities['properties']['category']['sort_order_2'] = 0;
		$Entities['properties']['category']['type'] = "";
		$Entities['properties']['category']['image'] = "";
		$Entities['properties']['category']['hex'] = "";

		$Entities['properties']['images'] = array();
		$Entities['properties']['images']['id'] = 0;
		$Entities['properties']['images']['name'] = "";
		$Entities['properties']['images']['path'] = "";
		$Entities['properties']['images']['type'] = "";
		$Entities['properties']['images']['width'] = 0;

		$Entities['properties']['urls'] = array();
		$Entities['properties']['urls']['id'] = 0;
		$Entities['properties']['urls']['name'] = "";
		$Entities['properties']['urls']['type'] = "";
		$Entities['properties']['urls']['url'] = "";

		$Entities['properties']['tags'] = array();
		$Entities['properties']['tags']['tag'] = "";

		$Entities['properties']['entities'] = new stdClass();

		$Relationships = array();

		$R = array();
		$R['rel'] = new stdClass();
		$R['rel'] = "urn:x-resource:name:category";
		$R['href'] = "https://{host}/api/{id}/category";
		$R['class'] = new stdClass();
		$R['class'] = "category";
		array_push($Relationships,$R);

		$R = array();
		$R['rel'] = new stdClass();
		$R['rel'] = "urn:x-resource:name:organizations";
		$R['href'] = "https://{host}/api/{id}/organizations";
		$R['class'] = new stdClass();
		$R['class'] = "organizations";
		array_push($Relationships,$R);

		$R = array();
		$R['rel'] = new stdClass();
		$R['rel'] = "urn:x-resource:name:tools";
		$R['href'] = "https://{host}/api/{id}/tools";
		$R['class'] = new stdClass();
		$R['class'] = "tools";
		array_push($Relationships,$R);

		$Entities['properties']['entities'] = $Relationships;

		$Entities['links'] = new stdclass();
		$Links = array();
		$Links['rel'] = new stdclass();
		$Links['rel'] = "self";
		$Links['href'] = "https://{host}/api/buildingblock/{buildingblock_id}";
		$Entities['links'] = $Links;

		array_push($E,$Entities);
		$ReturnObject['entities'] = $Entities;

		$ReturnObject['links'] = new stdclass();

		$Links = array();

		$L = array();
		$L['rel'] = new stdClass();
		$L['rel'] = "self";
		$L['href'] = "";
		array_push($Links,$L);

		$L = array();
		$L['rel'] = new stdClass();
		$L['rel'] = "next";
		$L['href'] = "";
		array_push($Links,$L);

		$L = array();
		$L['rel'] = new stdClass();
		$L['rel'] = "up";
		$L['href'] = "";
		array_push($Links,$L);

		$ReturnObject['links'] = $Links;

		var_dump($ReturnObject);

		}
	else
		{

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

	    	$host = $_SERVER['HTTP_HOST'];
	    	$building_block_id = prepareIdOut($building_block_id,$host);
			$building_block_category_id = prepareIdOut($building_block_category_id,$host);

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
		}

	});
?>
