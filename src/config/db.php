<?php

/*dbname=b5_21456488_mffapp
user= b5_21456488
pass= tergech48574
host = sql106.byethost5.com*/
	class db{
		//Properties
		 private $dbhost="b5_21456488_mffapp";	
		 private $dbuser="b5_21456488";
		 private $dbpass="tergech48574";
		 private $dbname="sql106.byethost5.com";

		 //connecting to the db
		 public function connect()
		 {
		 	$mysql_connect_str="mysql:host=$this->dbhost;dbname=$this->dbname;";
		 	$dbConnection=new PDO($mysql_connect_str,$this->dbuser,$this->dbpass);
		 	//set attributes
		 	$dbConnection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		 	return $dbConnection;
		 }

	}


?>