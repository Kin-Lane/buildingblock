<?php
$route = '/buildingblocks/tags/:tag/';
$app->get($route, function ($tag)  use ($app){

	$ReturnObject = array();

 	$request = $app->request();
 	$params = $request->params();

	$tag = trim(mysql_real_escape_string($tag));

	$SearchQuery = "SELECT b.Building_Block_ID,b.Building_Block_Category_ID,b.Name,b.About,b.Sort_Order,bbc.Name AS Category,bbc.Type as Type FROM building_block b";
	$SearchQuery .= " JOIN building_block_category bbc ON b.Building_Block_Category_ID = bbc.BuildingBlockCategory_ID";
	$SearchQuery .= " JOIN building_block_tag_pivot bbtp ON b.Building_Block_ID = bbtp.Building_Block_ID";
	$SearchQuery .= " JOIN tags t ON bbtp.Tag_ID = t.Tag_ID";
	$SearchQuery .= " WHERE t.Tag = '" . $tag . "'";
	$SearchQuery .= " ORDER BY b.Sort_Order ASC";
	echo $SearchQuery . "<br />";

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
		$F['image'] = $image_path;
		$F['image_width'] = $image_width;
		$F['sort_order'] = $sort_order;

		array_push($ReturnObject, $F);
		}

		$app->response()->header("Content-Type", "application/json");
		echo format_json(json_encode($ReturnObject));
	});
?>
