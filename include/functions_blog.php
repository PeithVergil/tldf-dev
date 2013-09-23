<?php
	require('./blog/wp-config.php');
	
	//db parameters
	$db_hostname = DB_HOST;
	$db_username = DB_USER;
	$db_password = DB_PASSWORD;
	$db_database = DB_NAME;
	
	//connect to the database
	mysql_connect($db_hostname, $db_username, $db_password);
	@mysql_select_db($db_database) or die("Unable to select wordpress database");
	
	function GetBlogPosts($num)
	{
		//get data from database -- !IMPORTANT, the "LIMIT 5" means how many posts will appear. Change the 5 to any whole number.
		$query = "Select * FROM wp_posts WHERE post_type='post' AND post_status='publish' ORDER BY id DESC LIMIT $num"; 
		//echo $query;
		$res    = mysql_query($query);
		$numrow = mysql_num_rows($res);
		
		$data=array();
		
		if($numrow < 1)
		{
			return 0;
		}
		else
		{
			$count=1;
			while($row = mysql_fetch_array($res))
			{
				//fetching all the data in the array one by one
				//putting that object in the array
				$data[$count]=$row;
				$count++;
			}
			//print_r($data);
			return $data;
		}
		//close database connection
		mysql_close();
	}
?>