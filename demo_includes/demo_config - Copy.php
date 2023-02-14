<?php

//define where files are stored
$file_path = "files/";

//define database access values
$db_addr = "sleezypete.powwebmysql.com";
$db_user = "ticketsuser1";
$db_pass = "T1ck3tZpass1!";
$db_name = "demo_tickets";

//determine if password required
$password_protect = false;
$password = "";


//connect to database ( PHP 5 )
/*
if(!mysql_connect("$db_addr","$db_user","$db_pass"))
{
	echo "Database Connection Error.";
	exit();
}
if(!mysql_select_db("$db_name"))
{
	echo "Error Seleccionando La Base De Datos!.";
	exit();
}
*/
//connect to database ( PHP 7 )

$conn = new mysqli($db_addr, $db_user, $db_pass);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$conn -> select_db($db_name);


function sys_query($query, $single = false /*, $version = 5 , $conn = null */){
		GLOBAL $conn, $php_version;
		
		if(!$query)die("Query empty : sys_query");
		$temp = Array();
		
		if($php_version == 5){
			$res = mysql_query($query);
			while($row = mysql_fetch_assoc($res))
			$temp[] = $row;
			
			
		}
		
		if($php_version == 7){
			$result = mysqli_query($conn, $query);
			while($row = mysqli_fetch_assoc($result)){
					$temp[] = $row;
			}
		}
		
		if($single)$temp = $temp[0];
		return $temp;

/*
if (!defined('PHP_VERSION_ID')) {
    $version = explode('.', PHP_VERSION);

    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}
*/
		
}
	
//get all post variables


if(isset($_POST))
foreach ($_POST as $var => $value) {
$nowval= "$$var = '$value';";
//echo $nowval."<br>";
eval($nowval);

if(is_array($value)){
foreach ($value as $var2 => $value2){
$nowval="$".$var."[".$var2."] = '$value2';";
//echo $nowval."<br>";
eval($nowval);}
}
} 

if(isset($_GET))
foreach ($_GET as $var => $value) {
$nowval="$$var = '$value';";
//echo $nowval."<br>";
eval($nowval);

if(is_array($value)){
foreach ($value as $var2 => $value2){
$nowval="$".$var."[".$var2."] = '$value2';";
//echo $nowval."<br>";
eval($nowval);}
}

}

?>