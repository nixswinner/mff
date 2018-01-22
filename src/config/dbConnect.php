<?php
/*dbname=b5_21456488_mffapp
user= b5_21456488
pass= tergech48574
host = sql106.byethost5.com*/
 
 define('HOST','sql106.byethost5.com');
 define('USER','b5_21456488');
 define('PASS','tergech48574');
 define('DB','b5_21456488_mffapp');


 $con = mysqli_connect(HOST,USER,PASS,DB) or die('Unable to Connect');
 if ($con) {
 	//echo "1";
 }else
 {
 	//echo "0";
 }

 ?>