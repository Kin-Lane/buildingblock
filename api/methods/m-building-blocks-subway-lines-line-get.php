<?php
$route = '/buildingblocks/subway/lines/:line/';
$app->get($route, function ($line)  use ($app,$contentType,$githuborg,$githubrepo){

  $ReturnObject = array();

	$request = $app->request();
 	$params = $request->params();

	if(isset($params['page'])){ $page = trim(mysql_real_escape_string($params['page'])); } else { $page = 0;}
  $thisline = trim(mysql_real_escape_string($line));

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
		$ReturnObject['rel'] = "urn:x-resource:schema:http://kin-lane.github.io/buildingblock/schemas/line.json";

    $SearchQuery = "SELECT DISTINCT bbc.Type,bbc.Image,bbc.Hex,bbc.Sort_Order_2 FROM building_block_category bbc";
    $SearchQuery .= " WHERE Sort_Order_2 > 0";
		$SearchQuery .= " ORDER BY Sort_Order_2 ASC";
		//echo $SearchQuery . "<br />";
		$DatabaseResult = mysql_query($SearchQuery) or die('Query failed: ' . mysql_error());

		// Properties
		$ReturnObject['properties'] = array();

		// Entities
		$ReturnObject['entities'] = new stdClass();

		// Actions
		//$ReturnObject['actions'] = new stdClass();

		// For Local Array
		$E = array();

    $previous = 0;
    $previous_line = "";

    $next = 0;
    $next_line = "";

		while ($Database = mysql_fetch_assoc($DatabaseResult))
			{

      $line = $Database['Type'];
			$sort_order = $Database['Sort_Order_2'];
			$hex = $Database['Hex'];
      $image = $Database['Image'];

      if($next == 1)
        {
        $line = $Database['Type'];
        $next_line = $line;
        }
      if(strtolower($thisline)==strtolower($line))
        {
  			$E['line'] = $line;
  			$E['sort_order'] = $sort_order;
        $E['hex'] = $hex;
        $E['image'] = $image;

        $next = 1;
        $previous = 1;
        }
      else
        {
        if($previous==0)
          {
          $previous_line = $line;
          }
        }

			}

		$ReturnObject['properties'] = $E;

    $Relationships = array();

    $R = array();
    $R['rel'] = new stdClass();
    $R['rel'] = "urn:x-resource:name:category";
    $R['href'] = 'http://' . $host . '/buildingblocks/subway/line/' . $line . '/areas';
    $R['class'] = new stdClass();
    $R['class'] = "areas";
    array_push($Relationships,$R);

    $ReturnObject['entities'] = $Relationships;

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
		$L['href'] = 'http://' . $host . '/buildingblocks/subway/line/' . $line;
		array_push($Links,$L);

    if($previous_line != "")
      {
        // Previous
    		$L = array();
    		$L['rel'] = new stdClass();
    		$L['rel'] = "self";
    		$L['href'] = 'http://' . $host . '/buildingblocks/subway/line/' . $previous_line;
    		array_push($Links,$L);
      }

      if($next_line != "")
        {
          // Previous
      		$L = array();
      		$L['rel'] = new stdClass();
      		$L['rel'] = "self";
      		$L['href'] = 'http://' . $host . '/buildingblocks/subway/line/' . $next_line;
      		array_push($Links,$L);
        }

		$ReturnObject['links'] = $Links;

		$app->response()->header("Content-Type", "application/vnd.siren+json");
		echo stripslashes(format_json(json_encode($ReturnObject)));

		}

	});
?>
