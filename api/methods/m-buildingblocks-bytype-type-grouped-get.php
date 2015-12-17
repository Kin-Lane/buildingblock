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
			$building_block_id_out = prepareIdOut($building_block_id,$host);
			$building_block_category_id_out = prepareIdOut($building_block_category_id,$host);

			$F = array();
			$F['building_block_id'] = $building_block_id_out;
			$F['building_block_category_id'] = $building_block_category_id_out;
			$F['name'] = $name;
			$F['about'] = $about;
			$F['category_id'] = $category_id;
			$F['category'] = $category;
			$F['category_image'] = $category_image;
			$F['category_hex'] = $category_hex;
			$F['image'] = $image_path;
			$F['image_width'] = $image_width;
			$F['sort_order'] = $sort_order;

			$F['organizations'] = array();
			$orgquery = "SELECT c.Name, c.URL,cbbp.Display_Text FROM company c";
			$orgquery .= " JOIN company_building_block_pivot cbbp ON c.Company_ID = cbbp.Company_ID";
			$orgquery .= " WHERE cbbp.Building_Block_ID = " . $building_block_id;
			//echo $orgquery . "<br />";
			$orgresults = mysql_query($orgquery) or die('Query failed: ' . mysql_error());

			if($orgresults && mysql_num_rows($orgresults))
				{
				while ($orgs = mysql_fetch_assoc($orgresults))
					{
					$org_name = $orgs['Name'];
					$org_url = $orgs['URL'];
					$org_text = $orgs['Display_Text'];
					$O = array();
					$O['name'] = $org_name;
					$O['url'] = $org_url;
					$O['text'] = $org_text;
					array_push($F['organizations'], $O);
					}
				}

			$F['apis'] = array();
			$apiquery = "SELECT a.Name, a.URL, abbp.Display_Text FROM api a";
			$apiquery .= " JOIN api_building_block_pivot abbp ON a.API_ID = abbp.API_ID";
			$apiquery .= " WHERE abbp.Building_Block_ID = " . $building_block_id;
			//echo $orgquery . "<br />";
			$apiresults = mysql_query($apiquery) or die('Query failed: ' . mysql_error());

			if($apiresults && mysql_num_rows($apiresults))
				{
				while ($apis = mysql_fetch_assoc($apiresults))
					{
					$api_name = $apis['Name'];
					$api_url = $apis['URL'];
					$api_text = $apis['Display_Text'];
					$A = array();
					$A['name'] = $api_name;
					$A['url'] = $api_url;
					$A['text'] = api_text;
					array_push($F['apis'], $A);
					}
				}

			$F['links'] = array();
			$linkquery = "SELECT bbu.Name, bbu.URL, bbu.Display_Text FROM building_block_url bbu";
			$linkquery .= " WHERE bbu.Building_Block_ID = " . $building_block_id;
			//echo $linkquery . "<br />";
			$linkresults = mysql_query($linkquery) or die('Query failed: ' . mysql_error());

			if($linkresults && mysql_num_rows($linkresults))
				{
				while ($links = mysql_fetch_assoc($linkresults))
					{
					$link_name = $links['Name'];
					$link_url = $links['URL'];
					$link_text = $links['Display_Text'];
					$L = array();
					$L['name'] = $link_name;
					$L['url'] = $link_url;
					$L['text'] = api_text;
					array_push($F['links'], $L);
					}
				}

			$F['tools'] = array();
			$toolquery = "SELECT t.Name, t.URL, bbtp.Display_Text FROM tools t";
			$toolquery .= " JOIN building_block_tools_pivot bbtp ON t.Tools_ID = bbtp.Tools_ID";
			$toolquery .= " WHERE bbtp.Building_Block_ID = " . $building_block_id;
			//echo $toolquery . "<br />";
			$toolresults = mysql_query($toolquery) or die('Query failed: ' . mysql_error());

			if($toolresults && mysql_num_rows($toolresults))
				{
				while ($tools = mysql_fetch_assoc($toolresults))
					{
					$tool_name = $tools['Name'];
					$tool_url = $tools['URL'];
					$tool_text = $tools['Display_Text'];
					$T = array();
					$T['name'] = $tool_name;
					$T['url'] = $tool_url;
					$T['text'] = $tool_text;
					array_push($F['tools'], $T);
					}
				}

			$F['questions'] = array();
			$questionquery = "SELECT q.Title as Name, '' as URL, bbqp.Display_Text FROM `stack_network_kinlane_questionapi`.`question` q";
			$questionquery .= " JOIN `apievangelist`.`building_block_questions_pivot` bbqp ON q.question_id = bbqp.Question_ID";
			$questionquery .= " WHERE bbqp.Building_Block_ID = " . $building_block_id;
			//echo $questionquery . "<br />";
			$questionresults = mysql_query($questionquery) or die('Query failed: ' . mysql_error());

			if($questionresults && mysql_num_rows($questionresults))
				{
				while ($questions = mysql_fetch_assoc($questionresults))
					{
					$question_name = $questions['Name'];
					$question_url = $questions['URL'];
					$question_text = $questions['Display_Text'];
					$T = array();
					$T['name'] = $question_name;
					$T['url'] = $question_url;
					$T['text'] = $question_text;
					array_push($F['questions'], $T);
					}
				}

			array_push($ReturnObject[$Building_Block_Category_Name], $F);
			}

		}

		$app->response()->header("Content-Type", "application/json");
		echo format_json(json_encode($ReturnObject));
	});
?>
