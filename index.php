<?php

/* php program to practice with databes handleing
   written by Derek Mauldin
   April 27, 2016 */

require_once("style.css");
require_once("derek.php");
require_once("user.php");

// setup dsn and options
$dsn = 'mysql:host=' . $config["hostname"] . ';dbname=' . $config["database"];
$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");

// set up pdo connection with database and set error attributes
$pdo = new PDO($dsn, $config["username"], $config["password"], $options);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



$ourUsers = User::getAllUsers($pdo);

$ourUsers->rewind();


foreach($ourUsers as $user) {

	if($user->getUserName() === "Flash") {
		echo "Flase Email is " . $user->getUserEmail();
	}


}











 ?>
