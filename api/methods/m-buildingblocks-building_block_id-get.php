<?php
$route = '/buildingblocks/:building_block_id/';
$app->get($route, function ($building_block_id)  use ($app,$contentType,$githuborg,$githubrepo){

	$host = $_SERVER['HTTP_HOST'];
	$building_block_id = prepareIdIn($building_block_id,$host);

	$ReturnObject = array();
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

			$ReturnObject['rel'] = new stdClass();
			$ReturnObject['rel'] = "urn:x-resource:schema:http://kin-lane.github.io/buildingblock/schemas/buildingblocks.json";

			// Just Count
			$CountQuery = "SELECT b.Building_Block_ID FROM building_block b WHERE b.Building_Block_ID = " . $building_block_id;
			$CountResult = mysql_query($CountQuery) or die('Query failed: ' . mysql_error());

			$SearchQuery = "SELECT * FROM building_block b";
			$SearchQuery .= " JOIN building_block_category bbc ON b.Building_Block_ID = bbc.BuildingBlock_ID";
			$SearchQuery .= " WHERE b.Building_Block_ID = " . $building_block_id;
			//echo $SearchQuery . "<br />";
			$DatabaseResult = mysql_query($SearchQuery) or die('Query failed: ' . mysql_error());

			// Properties
			$ReturnObject['properties'] = array();

			// Actions
			$ReturnObject['actions'] = new stdClass();

			// For Local Array
			$E = array();

			while ($Database = mysql_fetch_assoc($DatabaseResult))
				{

				$building_block_id = $Database['Building_Block_ID'];
				$building_block_category_id = $Database['Building_Block_Category_ID'];
				$name = $Database['Name'];
				$about = strip_tags($Database['About']);
				$about = str_replace(chr(34),"",$about);
				$about = str_replace(chr(39),"",$about);
				$sort_order = $Database['Sort_Order'];
				$sort_order_2 = $Database['Sort_Order_2'];
				$sort_order_3 = $Database['Sort_Order_3'];
				$category = $Database['Category'];
				$type = $Database['Type'];
				$category_image = $Database['Category_Image'];
				$hex = $Database['Hex'];

				$ReturnObject['properties']['id'] = $return_building_block_id;
				$ReturnObject['properties']['name'] = $name;
				$ReturnObject['properties']['about'] = $about;
				$ReturnObject['properties']['sort_order'] = $sort_order;
				$ReturnObject['properties']['url'] = "";

				$ReturnObject['properties']['category'] = array();
				$ReturnObject['properties']['category']['name'] = $category;
				$ReturnObject['properties']['category']['sort_order'] = $sort_order_2;
				$ReturnObject['properties']['category']['sort_order_2'] = $sort_order_2;
				$ReturnObject['properties']['category']['type'] = $type;
				$ReturnObject['properties']['category']['image'] = $category_image;
				$ReturnObject['properties']['category']['hex'] = $hex;

				// Images
				$ReturnObject['properties']['images'] = array();
				$ImageQuery = "SELECT Image_Name,Image_Path,Type,Width FROM building_block_image";
				$ImageQuery .= " WHERE Building_Block_ID = " . $building_block_id;
				$ImageResult = mysql_query($ImageQuery) or die('Query failed: ' . mysql_error());
				while ($Images = mysql_fetch_assoc($ImageResult))
					{
					$I = array();
					$I['name'] = $Images['Image_Name'];
					$I['path'] = $Images['Image_Path'];
					$I['type'] = $Images['Type'];
					$I['width'] = $Images['Width'];
					array_push($ReturnObject['properties']['images'],$I);
					}

				// URLs
				$ReturnObject['properties']['urls'] = array();
				$URLQuery = "SELECT Name,Type,URL FROM building_block_url";
				$URLQuery .= " WHERE Building_Block_ID = " . $building_block_id;
				$URLResult = mysql_query($URLQuery) or die('Query failed: ' . mysql_error());
				while ($URLs = mysql_fetch_assoc($URLResult))
					{
					$U = array();
					$U['name'] = $URLs['Name'];
					$U['type'] = $URLs['Type'];
					$U['url'] = $URLs['URL'];
					array_push($ReturnObject['properties']['urls'],$U);
					}

				// Tag
				$ReturnObject['properties']['tags'] = array();
				$TagQuery = "SELECT t.Tag FROM building_block_tag_pivot bbtp";
				$TagQuery .= " JOIN tags t ON bbtp.Tag_ID = t.Tag_ID";
				$TagQuery .= " WHERE bbtp.Building_Block_ID = " . $building_block_id;
				$TagResult = mysql_query($TagQuery) or die('Query failed: ' . mysql_error());
				while ($Tags = mysql_fetch_assoc($TagResult))
					{
					$T = array();
					$T['tag'] = $Tags['Tag'];
					array_push($ReturnObject['properties']['tags'],$T);
					}

				$ReturnObject['properties']['entities'] = new stdClass();

				$Relationships = array();

				$R = array();
				$R['rel'] = new stdClass();
				$R['rel'] = "urn:x-resource:name:category";
				$R['href'] = 'http://' . $host . '/buildingblocks/' . $building_block_id . '/category/';
				$R['class'] = new stdClass();
				$R['class'] = "category";
				array_push($Relationships,$R);

				$R = array();
				$R['rel'] = new stdClass();
				$R['rel'] = "urn:x-resource:name:organizations";
				$R['href'] = 'http://' . $host . '/buildingblocks/' . $building_block_id . '/organizations/';
				$R['class'] = new stdClass();
				$R['class'] = "organizations";
				array_push($Relationships,$R);

				$R = array();
				$R['rel'] = new stdClass();
				$R['rel'] = "urn:x-resource:name:tools";
				$R['href'] = 'http://' . $host . '/buildingblocks/' . $building_block_id . '/tools/';
				$R['class'] = new stdClass();
				$R['class'] = "tools";
				array_push($Relationships,$R);

				$ReturnObject['properties']['entities'] = $Relationships;
				}

			// Actions
			$ReturnObject['actions'] = new stdclass();
			$Actions = array();

			$A = array();
			$A['name'] = "add-buildingblock";
			$A['href'] = 'http://' . $host . '/buildingblocks/';
			$A['title'] = "Add a new building block";
			$A['method'] = "POST";
			$A['fields'] = array();

			$F = array();
			$F['name'] = "building_block_category_id";
			$F['type'] = "integer";
			array_push($A['fields'],$F);

			$F = array();
			$F['name'] = "name";
			$F['type'] = "string";
			array_push($A['fields'],$F);

			$F = array();
			$F['name'] = "about";
			$F['type'] = "string";
			array_push($A['fields'],$F);

			$F = array();
			$F['name'] = "sort_order";
			$F['type'] = "integer";
			array_push($A['fields'],$F);

			array_push($Actions,$A);

			$ReturnObject['actions'] = $Actions;

			// Links
			$ReturnObject['links'] = new stdclass();
			$Links = array();

			// Self
			$L = array();
			$L['rel'] = new stdClass();
			$L['rel'] = "self";
			$L['href'] = 'http://' . $host . '/buildingblocks/' . $building_block_id . '/';
			array_push($Links,$L);

			// Previous
			if($page!=0)
				{
				$page = $page - 250;
				$L = array();
				$L['rel'] = new stdClass();
				$L['rel'] = "previous";
				$href = 'http://' . $host . '/buildingblocks/' . $building_block_id . '/?page=' . $page;
				$L['href'] = $href;
				array_push($Links,$L);
				}

			// Next
			$page = $page + 250;
			$L = array();
			$L['rel'] = new stdClass();
			$L['rel'] = "next";
			$href = 'http://' . $host . '/buildingblocks/' . $building_block_id . '/?page=' . $page;
			$L['href'] = $href;
			array_push($Links,$L);

			// Category ?? aka AREA
			$L = array();
			$L['rel'] = new stdClass();
			$L['rel'] = "up";
			$L['href'] = 'http://' . $host . '/buildingblocks/' . $building_block_id . '/';
			array_push($Links,$L);

			// TYpe ?? aka Line
			$L = array();
			$L['rel'] = new stdClass();
			$L['rel'] = "up";
			$L['href'] = 'http://' . $host . '/buildingblocks/' . $building_block_id . '/';
			array_push($Links,$L);

			$ReturnObject['links'] = $Links;

			$app->response()->header("Content-Type", "application/vnd.siren+json");
			echo stripslashes(format_json(json_encode($ReturnObject)));

		}
	else
		{
		$Query = "SELECT * FROM building_block WHERE Building_Block_ID = " . $building_block_id;

		$DatabaseResult = mysql_query($Query) or die('Query failed: ' . mysql_error());

		while ($Database = mysql_fetch_assoc($DatabaseResult))
			{

			$building_block_id = $Database['Building_Block_ID'];
			$building_block_category_id = $Database['Building_Block_Category_ID'];
			$name = $Database['Name'];
			$about = $Database['About'];
			$sort_order = $Database['Sort_Order'];

			// manipulation zone

			$building_block_id = prepareIdOut($building_block_id,$host);
			$building_block_category_id = prepareIdOut($building_block_category_id,$host);

			$F = array();
			$F['building_block_id'] = $building_block_id;
			$F['building_block_category_id'] = $building_block_category_id;
			$F['name'] = $name;
			$F['about'] = $about;
			$F['sort_order'] = $sort_order;

			$ReturnObject = $F;
			}

			$app->response()->header("Content-Type", "application/json");
			echo format_json(json_encode($ReturnObject));
		}
	});
?>
