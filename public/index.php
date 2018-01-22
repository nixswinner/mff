<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
//include db connection
require '../src/config/db.php';
require '../src/config/dbConnect.php';
$app = new \Slim\App;
$app->get('/hello/{name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $response->getBody()->write("Hello I am, $name");

    return $response;
});


$app->get('/api/getDrivers',function(Request $request,Response $response){
	$sql="SELECT * FROM drivers";
	try {
	
		$result = mysqli_query($con,$sql);
		$response = array();
		while($row = mysqli_fetch_array($result)){
		array_push($result,array(
			"name"=>$row['name'],
			"email"=>$row['email'], 

		));
	}
	
	echo json_encode($result);

	} catch (Exception $e) {
		//displaying error in json format
		echo '{"error:"{"text ":'.$e->getMessage().'}';

	}


});

//registering new passengers
$app->post('/api/registerPass',function(Request $request,Response $response){
	//hashing passwords
	 $name=$request->getParam('name');
	 $phone=$request->getParam('phone');
	 $email=$request->getParam('email');
	 $password_hash=$request->getParam('password_hash');
	 $response = array("error" => FALSE);

	 //$hashed_password = PassHash::hash($password_hash);
	 $hashed_password = md5($password_hash);//hashing
	//inserting into passengers table
	$sql="INSERT INTO pass(name,phone,email,password_hash)VALUES(:name,:phone,:email,:password_hash)";

	try {
		//get DB object
		$db=new db();
		//connect
		$db=$db->connect();

		$stmt=$db->prepare($sql);
		$stmt->bindParam(':name',$name);
		$stmt->bindParam(':phone',$phone);
		$stmt->bindParam(':email',$email);
		$stmt->bindParam(':password_hash',$hashed_password);
	
		$stmt->execute();

		//echo '{"notice":{"text":"Joined us Successfully"}';
		$response= "Joined Successfully";
		
	} catch (Exception $e) {
		//displaying error in json format
		echo '{"error:"{"text ":'.$e->getMessage().'}';
		  // user failed to store
            $response["error"] = TRUE;
            $response["error_msg"] = "Error occurred!Please try again";
            echo json_encode($response);

	}


});

//registering new drivers(transporters)
$app->post('/api/registerDrivers',function(Request $request,Response $response){
	//hashing passwords
	 $name=$request->getParam('name');
	 $phone=$request->getParam('phone');
	 $id_no=$request->getParam('id_no');
	 $email=$request->getParam('email');
	 $password_hash=$request->getParam('password_hash');

	  $response = array("error" => FALSE);
	 //$hashed_password = PassHash::hash($password_hash);
	 $hashed_password = md5($password_hash);
	 
	//inserting into drivers  table
	$sql="INSERT INTO drivers(name,phone,id_no,email,password_hash)VALUES(:name,:phone,:id_no,:email,:password_hash)";

	try {
		//get DB object
		$db=new db();
		//connect
		$db=$db->connect();
 
		$stmt=$db->prepare($sql);
		$stmt->bindParam(':name',$name);
		$stmt->bindParam(':phone',$phone);
		$stmt->bindParam(':id_no',$id_no);
		$stmt->bindParam(':email',$email);
		$stmt->bindParam(':password_hash',$hashed_password);
	
		$stmt->execute();

		echo '{"notice":{"text":"Joined us Successfully"}';
		echo"Joined us Successfully";
		
	} catch (Exception $e) {
		//displaying error in json format
		echo '{"error:"{"text ":'.$e->getMessage().'}';
		 $response["error"] = TRUE;
            $response["error_msg"] = "Error occurred!Please try again";
            echo json_encode($response);

	}


});
//login
$app->post('/api/loginUser',function(Request $request,Response $response){
	//hashing passwords
	 $email=$request->getParam('email');
	 $password=$request->getParam('password_hash');
	 $hashed_password = md5($password);
	 $ninani=$request->getParam('who');
	 // json response array
		$response = array("error" => FALSE);
	 //$hashed_password = PassHash::hash($password);
	 //driver login
	if ($ninani=="D") {
		$sql="SELECT password_hash FROM drivers WHERE email= '$email'";
	}else
	{
		//passenger login
		$sql="SELECT password_hash FROM pass WHERE email= '$email'";


	}
	

	try {
		//get DB object
		$db=new db();
		//connect
		$db=$db->connect();

		$stmt=$db->query($sql);
		$pass1=$stmt->fetch();
		$pass=$pass1['password_hash'];
		//echo'</br>Pass2'.$pass;
		
		if($pass==$hashed_password)
		{
			//return user id;

			if ($ninani=="D") {
				# driver
			$sql2="SELECT `id`, `name`, `phone`, `email` FROM `drivers` WHERE  email= '$email'";
			$stmt=$db->query($sql2);
			$user=$stmt->fetchAll(PDO::FETCH_OBJ);
			//return user json details
			echo json_encode(array('result'=>$user));
			}
			else
			{
				//passengers
				//echo '{"notice":{"text":"Login  Successfully"}';
			$sql2="SELECT `id`, `name`, `phone`, `email` FROM `pass` WHERE  email= '$email'";
			$stmt=$db->query($sql2);
			$user=$stmt->fetchAll(PDO::FETCH_OBJ);
			//return user json details
			echo json_encode(array('result'=>$user));
			}
		}else {
			 $response["error"] = TRUE;
       		 $response["error_msg"] = "Login credentials are wrong. Please try again!";
        	echo json_encode($response);
			//echo '{"notice":{"text":"Invalid  login;Check credentials"}';
		}
		$db=null;
		
		
	} catch (Exception $e) {
		//displaying error in json format
		echo '{"error:"{"text ":'.$e->getMessage().'}';

	}


});


//adding  drivers(transporters) car details
$app->post('/api/addCarDetails',function(Request $request,Response $response){
	//picking details
	 $driver_id=$request->getParam('driver_id');
	 $type=$request->getParam('type');
	 $no_plate=$request->getParam('no_plate');
	 $color=$request->getParam('color');

	
	 
	//inserting into car details  table
	$sql="INSERT INTO car_details(driver_id,type,no_plate,color)VALUES(:driver_id,:type,:no_plate,:color)";

	try {
		//get DB object
		$db=new db();
		//connect
		$db=$db->connect();

		$stmt=$db->prepare($sql);
		$stmt->bindParam(':driver_id',$driver_id);
		$stmt->bindParam(':type',$type);
		$stmt->bindParam(':no_plate',$no_plate);
		$stmt->bindParam(':color',$color);
	
	
		$stmt->execute();

		echo '{"notice":{"text":"Joined us Successfully"}';
		$response["result"] = "Joined us Successfully";
		echo json_encode($response);
		
	} catch (Exception $e) {
		//displaying error in json format
		echo '{"error:"{"text ":'.$e->getMessage().'}';

            $response["result"] = "Error occurred!Please try again";
            echo json_encode($response);

	}


});


//adding services to provide as in transport including routes
$app->post('/api/addMyService',function(Request $request,Response $response){
	//picking details
	 $driver_id=$request->getParam('driver_id');
	 $route_id=$request->getParam('route_name');
	 $cost=$request->getParam('cost');
	 $no_of_pass=$request->getParam('no_of_pass');
	 $status=$request->getParam('status');
	 $ddate=$request->getParam('date');
	 $ttime=$request->getParam('time');

	 
	//inserting into car details  table
	$sql="INSERT INTO trips(driver_id, route_name, cost, no_of_pass, status, _date, _time)VALUES(:driver_id,:route_id,:cost,:no_of_pass,:status,:ddate,:ttime)";

	try {
		//get DB object
		$db=new db();
		//connect
		$db=$db->connect();

		$stmt=$db->prepare($sql);
		$stmt->bindParam(':driver_id',$driver_id);
		$stmt->bindParam(':route_id',$route_id);
		$stmt->bindParam(':cost',$cost);
		$stmt->bindParam(':no_of_pass',$no_of_pass);
		$stmt->bindParam(':status',$status);
		$stmt->bindParam(':ddate',$ddate);
		$stmt->bindParam(':ttime',$ttime);
	
	
		$stmt->execute();

			// $response["result"] = "Added Successfully";
            //echo json_encode($response);

		echo '{"notice":{"text":"Added Successfully"}';
		
	} catch (Exception $e) {
		//displaying error in json format
		echo '{"error:"{"text ":'.$e->getMessage().'}';

	}


}); 

//make bookings
$app->post('/api/makebookings',function(Request $request,Response $response){
	//picking details
	 $driver_id=$request->getParam('driver_id');
	 $pass_id=$request->getParam('pass_id');
	 $status=$request->getParam('status');

	//inserting into car details  table
	$sql="INSERT INTO bookings(driver_id, pass_id,status)VALUES(:driver_id,:pass_id,:status)";

	try {
		//get DB object
		$db=new db();
		//connect
		$db=$db->connect();

		$stmt=$db->prepare($sql);
		$stmt->bindParam(':driver_id',$driver_id);
		$stmt->bindParam(':pass_id',$pass_id);
		$stmt->bindParam(':status',$status);
	
	
	
		$stmt->execute();

		echo '{"notice":{"text":"Booked Successfully"}';
		
	} catch (Exception $e) {
		//displaying error in json format
		echo '{"error:"{"text ":'.$e->getMessage().'}';

	}
}); 

//add routes
$app->post('/api/addRoute',function(Request $request,Response $response){
	//picking details
	 $name=$request->getParam('name');
	 
	 	 
	//inserting into car details  table
	$sql="INSERT INTO routes(name)VALUES(:name)";

	try {
		//get DB object
		$db=new db();
		//connect
		$db=$db->connect();

		$stmt=$db->prepare($sql);
		$stmt->bindParam(':name',$name);

		$stmt->execute();

		echo '{"notice":{"text":"Added Successfully"}';
		
	} catch (Exception $e) {
		//displaying error in json format
		echo '{"error:"{"text ":'.$e->getMessage().'}';

	}
}); 



//getting orders by delivers
$app->get('/api/getBookings/{id}',function(Request $request,Response $response){
	// $id=$request->getAttribute('id');
	//echo "customers Display";
	$driver_id = $request->getAttribute('id');
	$sql="SELECT * FROM bookings WHERE driver_id='$driver_id'";

	try {
		//get DB object
		$db=new db();
		//connect
		$db=$db->connect();

		$stmt=$db->query($sql);
		$orders=$stmt->fetchAll(PDO::FETCH_OBJ);
		$db=null;
		//echo json_encode($customers);
		echo json_encode(array('result'=>$orders));
		
	} catch (Exception $e) {
		//displaying error in json format
		echo '{"error:"{"text ":'.$e->getMessage().'}';

	}


});

$app->get('/api/getTrips/{route_name}',function(Request $request,Response $response){
	// $id=$request->getAttribute('id');
	//echo "customers Display";
	$route_name = $request->getAttribute('route_name');
	
	//$sql="SELECT * FROM bookings WHERE driver_id='$driver_id'";
	$sql="SELECT * FROM trips WHERE route_name='$route_name'";

	try {
		//get DB object
		$db=new db();
		//connect
		$db=$db->connect();

		$stmt=$db->query($sql);
		$trips=$stmt->fetchAll(PDO::FETCH_OBJ);
		$db=null;
		//echo json_encode($customers);
		echo json_encode(array('result'=>$trips));
		
	} catch (Exception $e) {
		//displaying error in json format
		echo '{"error:"{"text ":'.$e->getMessage().'}';

	}


});

//get an order by a deliverers
$app->post('/api/getAnOrdersToDeliver',function(Request $request,Response $response){
	// order_id`, `deliverer_id`
	 $order_id=$request->getParam('order_id');
	 $deliverer_id=$request->getParam('deliverer_id');
	 
	$sql="INSERT INTO deliveries(order_id, deliverer_id)VALUES (:order_id,:deliverer_id)";

	try {
		//get DB object
		$db=new db();
		//connect
		$db=$db->connect();

		$stmt=$db->prepare($sql);
		$stmt->bindParam(':order_id',$order_id);
		$stmt->bindParam(':deliverer_id',$deliverer_id);
	
	
		$stmt->execute();

		echo '{"notice":{"text":"You can deliver the order"}';
		//update delivery status to pending
		$sqlUpdate="UPDATE orders SET delivery_status=1 WHERE id=$order_id";
		$stmt=$db->prepare($sqlUpdate);
		if($stmt->execute())
		{
			echo"Updated Successfully";
		}else {
			# code...
			echo"Error updating";
		}
		
	} catch (Exception $e) {
		//displaying error in json format
		echo '{"error:"{"text ":'.$e->getMessage().'}';

	}


});




///............................... End Deliveries................................

$app->run();