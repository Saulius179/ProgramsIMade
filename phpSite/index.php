<html> 
	<body>
		<link rel="stylesheet" href= "style.css">
		<script src="jquery.1.js"></script>
		
		
		<h1> Welcome to index file !
    		<form method="get" action="process_sql_query.php">
        		Enter SEARCH SQL Query Here <br/>
        		<input type="text" name="User_Input_SQL_Query" onkeyup="loadTextPHP(this.value)" ><br/>
        		<input type="submit" value=" Search SQL Database " /><br/>
    		</form>
		</h1>
		
		
		<div class="style_class_one" id="test">
			<p id="ajax_field"> First content </p>
		</div>

		<script>
			// create event listener, listen when button is clicked
			document.getElementById('btn').addEventListener('click', loadTextPHP)
			
			function loadTextPHP(str){
				// Create XHR Object
				var xhr = new XMLHttpRequest();
				xhr.open('GET','process_sql_query.php?User_Input_SQL_Query='+str, true)
				xhr.onload = function(){
					if(this.status == 200){
						//SET ID FIELD TO xhr.open RESPONSE
						jsvar = "";
						jsvar = this.responseText;
						document.getElementById("ajax_field").innerHTML = jsvar;
						}
					}
				xhr.send();
				}
		</script>
	
	<script> 
		$(document).ready(function(){
			alert("OK");
			});
		</script>
	</body>
</html>

