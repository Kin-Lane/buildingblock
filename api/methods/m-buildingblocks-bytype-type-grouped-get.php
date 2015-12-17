<?php
$route = '/buildingblocks/bytype/:type/grouped/';
$app->get($route, function ($type)  use ($app){

	$ReturnObject = array();

 	$request = $app->request();
 	$params = $request->params();

	$type = trim(mysql_real_escape_string($type));

	$Query = "SELECT bbc.BuildingBlockCategory_ID AS Building_Block_Category_ID,bbc.Name,bbc.Sort_Order FROM building_block_category bbc";
	$Query .= " WHERE bbc.Type ='" . $type . "'";
	$Query .= " ORDER BY Sort_Order";
	//echo $Query . "<br />";

	$CategoryResult = mysql_query($Query) or die('Query failed: ' . mysql_error());

	while ($Category = mysql_fetch_assoc($CategoryResult))
		{

		$Building_Block_Category_ID = $Category['Building_Block_Category_ID'];
		$Building_Block_Category_Name = $Category['Name'];
		$Building_Block_Category_Sort_Order = $Category['Sort_Order'];

		$ReturnObject[$Building_Block_Category_Name] = array();

		$Query = "SELECT b.Building_Block_ID,b.Building_Block_Category_ID,b.Name,b.About,b.Sort_Order,bbc.Name AS Category,bbc.Type as Type,bbc.Image,bbc.Hex FROM building_block b";
		$Query .= " JOIN building_block_category bbc ON b.Building_Block_Category_ID = bbc.BuildingBlockCategory_ID";
		$Query .= " WHERE bbc.BuildingBlockCategory_ID = " . $Building_Block_Category_ID;
		$Query .= " ORDER BY b.Sort_Order";
		//echo $Query . "<br />";

		$DatabaseResult = mysql_query($Query) or die('Query failed: ' . mysql_error());

		while ($Database = mysql_fetch_assoc($DatabaseResult))
			{

			$building_block_id = $Database['Building_Block_ID'];
			$building_block_category_id = $Database['Building_Block_Category_ID'];
			$name = $Database['Name'];
			$about = $Database['About'];
			$category = $Database['Category'];
			$category_id = $Database['Building_Block_Category_ID'];
			$sort_order = $Database['Sort_Order'];

			$category_image = $Database['Image'];
			$category_hex = $Database['Hex'];

			$image_path = "";
			$image_width = "100";
			$query = "SELECT Building_Block_Image_ID,Building_Block_ID,Image_Name,Image_Path,Width FROM building_block_image WHERE Image_Path <> '' AND Building_Block_ID = " . $building_block_id . " ORDER BY Building_Block_Image_ID DESC LIMIT 1";
			//echo $query;
			$imageResult = mysql_query($query) or die('Query failed: ' . mysql_error());
			$rowcount = 1;
			while ($row = mysql_fetch_assoc($imageResult))
				{
				$image_path = $row['Image_Path'];
				$image_width = $row['Width'];
				if($image_width==''){ $image_width = 100; }
				}

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
			$F['category_image'] = $category_image;
			$F['category_hex'] = $category_hex;
			$F['image'] = $image_path;
			$F['image_width'] = $image_width;
			$F['sort_order'] = $sort_order;

			array_push($ReturnObject[$Building_Block_Category_Name], $F);
			}

		}

		$app->response()->header("Content-Type", "application/json");
		echo format_json(json_encode($ReturnObject));
	});
?>
