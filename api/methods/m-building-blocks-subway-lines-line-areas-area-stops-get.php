<?php
$route = '/buildingblocks/subway/lines/:line/areas/:area/stops/';
$app->get($route, function ($line,$area)  use ($app,$contentType,$githuborg,$githubrepo){

  $ReturnObject = array();

	$request = $app->request();
 	$params = $request->params();

	if(isset($params['page'])){ $page = trim(mysql_real_escape_string($params['page'])); } else { $page = 0;}
  $thisline = trim(mysql_real_escape_string($line));
  $thisarea = trim(mysql_real_escape_string($area));

	if($contentType == 'application/apis+json')
		{

		$app->response()->header("Content-Type", "application/json");

		$apis_json_url = "http://" . $githuborg . ".github.io/" . $githubrepo . "/apis.json";
		$apis_json = file_get_contents($apis_json_url);
		echo stripslashes(format_json($apis_json));

		}
	elseif($contentType == 'application/vnd.siren+json')
		{

		//var_dump($_SERVER);

		$host = $_SERVER['HTTP_HOST'];
		$remote_address = $_SERVER['REMOTE_ADDR'];

		$ReturnObject['rel'] = new stdClass();
		$ReturnObject['rel'] = "urn:x-resource:schema:http://kin-lane.github.io/buildingblock/schemas/stops.json";

    $SearchQuery = "SELECT bb.Building_Block_ID,bb.Name,bb.About FROM building_block bb";
    $SearchQuery .= " JOIN building_block_category bbc ON bb.Building_Block_Category_ID = bbc.BuildingBlockCategory_ID";
    $SearchQuery .= " WHERE bbc.Type = '" . $thisline . "' AND bbc.NAME = '" . $thisarea . "'";
		$SearchQuery .= " ORDER BY bb.Sort_Order ASC";
		//echo $SearchQuery . "<br />";
		$DatabaseResult = mysql_query($SearchQuery) or die('Query failed: ' . mysql_error());

    // Properties
		$ReturnObject['properties'] = array();
		$ReturnObject['properties']['totalItems'] = mysql_num_rows($DatabaseResult);
		$ReturnObject['properties']['currentCount'] = mysql_num_rows($DatabaseResult);

		// Entities
		$ReturnObject['entities'] = new stdClass();

		// Actions
		$ReturnObject['actions'] = new stdClass();

		// For Local Array
		$E = array();

		while ($Database = mysql_fetch_assoc($DatabaseResult))
			{
			$stop_id = $Database['Building_Block_ID'];
			$stop_name = $Database['Name'];
			$stop_about = strip_tags($Database['About']);
      $stop_about = str_replace(chr(34),"",$stop_about);
			$stop_about = str_replace(chr(39),"",$stop_about);

			$Entities = array();
			$Entities['rel'] = new stdClass();

			$Entities_rel = array();
			$Entities_rel[0] = "properties:http://kin-lane.github.io/buildingblock/schemas/stops.json";
			$Entities_rel[0] = "urn:x-resource:name:stops";
			$Entities['rel'] = $Entities_rel;

			$Entities['class'] = new stdClass();
			$Entities['class'] = "stops";

			$Entities['properties'] = array();

      $host = $_SERVER['HTTP_HOST'];
			$return_stop_id = prepareIdOut($stop_id,$host);

      $Entities['properties']['stop_id'] = $return_stop_id;
      $Entities['properties']['name'] = $stop_name;
			$Entities['properties']['about'] = $stop_about;

			$Entities['properties']['entities'] = new stdClass();

			$Relationships = array();

			$R = array();
			$R['rel'] = new stdClass();
			$R['rel'] = "urn:x-resource:name:line";
			$R['href'] = 'http://' . $host . '/buildingblocks/subway/line/' . $thisline . '/';
			$R['class'] = new stdClass();
			$R['class'] = "line";
			array_push($Relationships,$R);

      $R = array();
			$R['rel'] = new stdClass();
			$R['rel'] = "urn:x-resource:name:area";
			$R['href'] = 'http://' . $host . '/buildingblocks/subway/line/' . urlencode($thisline) . '/areas/' . urlencode($thisarea) . '/';
			$R['class'] = new stdClass();
			$R['class'] = "area";
			array_push($Relationships,$R);

			$Entities['properties']['entities'] = $Relationships;

			$Entities['links'] = new stdclass();
			$Links = array();
			$Links['rel'] = new stdclass();
			$Links['rel'] = "self";
			$Links['href'] = 'http://' . $host . '/buildingblocks/subway/' . urlencode($thisline) . '/areas/' . urlencode($thisarea) . '/stops/' . urlencode($stop_name) . '/';
			$Entities['links'] = $Links;

			array_push($E,$Entities);
			}

		$ReturnObject['entities'] = $E;

		// Actions
		// $ReturnObject['actions'] = new stdclass();
		// $Actions = array();

		// $A = array();
		// $A['name'] = "add-buildingblock";
		// $A['href'] = 'http://' . $host . '/buildingblocks/';
		// $A['title'] = "Add a new building block";
		// $A['method'] = "POST";
		// $A['fields'] = array();

		// $F = array();
		// $F['name'] = "building_block_category_id";
		// $F['type'] = "integer";
		// array_push($A['fields'],$F);

		// array_push($Actions,$A);

		// $ReturnObject['actions'] = $Actions;

		// Links
		$ReturnObject['links'] = new stdclass();
		$Links = array();

		// Self
		$L = array();
		$L['rel'] = new stdClass();
		$L['rel'] = "self";
		$L['href'] = 'http://' . $host . '/buildingblocks/subway/line/' . urlencode($thisline) . '/areas/' . urlencode($thisarea) . '/';
		array_push($Links,$L);

		$ReturnObject['links'] = $Links;

		$app->response()->header("Content-Type", "application/vnd.siren+json");
		echo stripslashes(format_json(json_encode($ReturnObject)));

		}

	});
?>
