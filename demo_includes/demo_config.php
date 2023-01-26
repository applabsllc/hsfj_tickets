<?php

//define where files are stored
$file_path = "files/";

//define database access values
$db_addr = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "demo_tickets";

//determine if password required
$password_protect = false;
$password = "";


//connect to database ( PHP 5 )

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


?>