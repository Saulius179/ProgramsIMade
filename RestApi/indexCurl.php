<?php 

// Define function
function print_recursive($arr){
    foreach ($arr as $key => $val) {
        if (is_array($val)) {
            print_recursive($val);
        } else {
            echo("$key = $val <br/>");
        }
    }
    return;
}

?>

<html>
<head>
    <link rel="stylesheet" href= "style.css">
		<script src="https://code.jquery.com/jquery-latest.js"></script>
    	<script>
    		function list_users(){
				<?php 
    				$ch= curl_init();
                    $url="http://127.0.0.1/RestApi/v1/list-all.php";
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
                    $resp=curl_exec($ch);
                    if($e = curl_error($ch)){
                        echo $e;
                    }
                    else{
                    	/*
                            $decoded = json_decode($resp);
                            #print_r($decoded->status);
                            #print_r($decoded->data[1]->name);
                            foreach ($decoded->data as $data_element){
                                print_recursive($data_element);
                            }
                    	*/
                    }
                    curl_close($ch);
				?>
				
				var unparcedJSON = <?php echo json_encode($resp, JSON_HEX_TAG); ?>; 
				const obj = JSON.parse(unparcedJSON);
				var print_str="";
				for (var arr_element in obj.data){
    				for (var key in obj.data[arr_element]) {
    					    print_str=print_str.concat(key+": "+ obj.data[arr_element][key] + "\n");
    					    console.log(print_str);
    					}
				}
				$("#json_response").html(print_str);
			}

		function add_user(){
    		<?php 

$data = array(
	'name'=>'Johan'
);
$ch = curl_init();
$url = "http://127.0.0.1/RestApi/v1/create.php";
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CAURLOPT_POSTFIELDS, $data);
    		?>;
		}
		
    	</script>

        <title> Send HTTP GET </title>
</head>
<body>
    <center>
        <h3> Send HTTP Get request </h3>
            <form><br/>id : <br/><input name="input_id" id="input_id" type = "text"/><br/>
<br/>name : <br/><input name="input_name" id="input_name" type = "text"/><br/>
<br/>age : <br/><input name="input_age" id="input_age" type = "text"/><br/>
<input type="button" value="List all users" onclick="list_users()"/>
            </form>
        <div id="json_response"></div>
    </center>
</body>
</html>
