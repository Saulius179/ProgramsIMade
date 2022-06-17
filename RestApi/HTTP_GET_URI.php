<html>
<head>
	<link rel="stylesheet" href= "style.css">
	<title> Send HTTP GET </title>
	<script src="https://code.jquery.com/jquery-latest.js"></script>
	<script>
		function submit_soap(){
		var id = $("#input_id").val();
		$.get("http://127.0.0.1/RestApi/v1/single-user-by-get-method.php",{id:id},
				function(data){
					const obj = JSON.parse(data);
					var print_str="";
					for (var key in obj.data) {
					    print_str=print_str.concat(key+": "+ obj.data[key] + "\n");
					    console.log(print_str);
					}
					$("#json_response").html(print_str);
			});
		}
	</script>
	
</head>

<body>
	<center>
		<h3> Send HTTP Get request </h3>
		<form>id : <br/><input name="input_id" id="input_id" type = "text"/><br/><input type="button" value="Submit" onclick="submit_soap()"/>
		</form>
		<div id="json_response"></div>
	</center>
</body>

</html>