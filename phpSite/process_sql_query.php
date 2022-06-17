<?php 
	echo "Passed in SQL query: ";
	$passed_sql = $_GET['User_Input_SQL_Query'];
	echo $passed_sql;
?>
<br/>
<html> 
	<link rel="stylesheet" href= "style.css">
	<body>

		<?php 
		
			$dbhost = 'localhost';
			$dbuser = 'root';
			$dbpass = 'Razor/15';
			$dbname= 'crm_db1';
			$connection = mysqli_connect($dbhost, $dbuser, $dbpass,$dbname);
			if(! $connection ) {
				die('Could not connect: ' . mysqli_error());
			 }
			 
			 $sql_query = $passed_sql;
			 $sql_query_result = mysqli_query($connection, $sql_query);
			 if(!$sql_query_result)
			 {
				 die("Invalid Query");
			 }
			 
			 $row_variable='';
			 echo $row_variable . '<br /><hr /><br />';
			$bool_variable=false;
			if (strpos($passed_sql, 'INSERT') !== false) {
				$bool_variable=true;
			}
			if(!$bool_variable){
				while ($row = mysqli_fetch_row($sql_query_result))
				 {
					 foreach ($row as $row_value){
						 $row_variable = $row_variable . " - ". $row_value;
					 }
					 echo $row_variable . '<br /><hr /><br />';
					 $row_variable='';
				 }	
			}
		?>
		
		
		
		<?php mysqli_close($connection);?>
	</body>
</html>