<html>
<head>
	<link rel="stylesheet" href= "style.css">
	<title> Rest API Front End </title>
	<script src="https://code.jquery.com/jquery-latest.js"></script>
	<script>
		function update_user(){
		var id = $("#input_id").val();
		var name = $("#input_name").val();
		var age = $("#input_age").val();
		
		var xmlhttp = new XMLHttpRequest();   // new HttpRequest instance 
        var theUrl = "/RestApi/v1/update.php";
        xmlhttp.open("POST", theUrl);
        xmlhttp.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xmlhttp.send(
                JSON.stringify({ "id": id, "name": name, "age":age }));
		xmlhttp.onload = function(){
					if(this.status == 200){
						document.getElementById("json_response").innerHTML = this.responseText;
						}else{	document.getElementById("json_response").innerHTML = this.responseText;	}
					}

		}// function update_user()

		function add_user(){
		var id = $("#input_id").val();
		var name = $("#input_name").val();
		var age = $("#input_age").val();
		
		var xmlhttp = new XMLHttpRequest();   // new HttpRequest instance 
        var theUrl = "/RestApi/v1/create.php";
        xmlhttp.open("POST", theUrl);
        xmlhttp.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xmlhttp.send(
                JSON.stringify({ "id": id, "name": name, "age":age }));
		xmlhttp.onload = function(){
					if(this.status == 200){
						document.getElementById("json_response").innerHTML = this.responseText;
						}else{	document.getElementById("json_response").innerHTML = this.responseText;	}
					}

	
		}// function add_user()

		function list_users(){
				// Create XHR Object
				var xhr = new XMLHttpRequest();
				xhr.open('GET',"/RestApi/v1/list-all.php", true)
				xhr.onload = function(){
    					const obj = JSON.parse(this.responseText);
        				var print_str="";
        				for (var arr_element in obj.data){
                				for (var key in obj.data[arr_element]) {
                					    print_str=print_str.concat(key+": "+ obj.data[arr_element][key] + " - - - - -");
                					    console.log(print_str);
                					} print_str=print_str+"\n"
            				}
        				document.getElementById("json_response").innerHTML = print_str;
					}
				xhr.send();
				}// function list_users()
	</script>
	
</head>

<body>
	<center>
		<h3> Rest API Front End </h3>
		<form><br/>id : <br/><input name="input_id" id="input_id" type = "text"/><br/>
<br/>name : <br/><input name="input_name" id="input_name" type = "text"/><br/>
<br/>age : <br/><input name="input_age" id="input_age" type = "text"/><br/>
<input type="button" value="List all users" onclick="list_users()"/>
<input type="button" value="Add user" onclick="add_user()"/>
<input type="button" value="Update user" onclick="update_user()"/>
            </form>
		<div id="json_response"></div>
	</center>
</body>

</html>