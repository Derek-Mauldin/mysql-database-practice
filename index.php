<?php

/* php program to practice with databes handleing
   written by Derek Mauldin
   April 27, 2016 */

require_once("style.css");
require_once("derek.php");

try {

// setup dsn and options
$dsn = 'mysql:host=' . $config["hostname"] . ';dbname=' . $config["database"];
$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");

// set up pdo connection with database and set error attributes
$pdo = new PDO($dsn, $config["username"], $config["password"], $options);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// setup query and execute
$query = 'SELECT userId, username, userEmail, dateCreated FROM user';
$statement = $pdo->prepare($query);
$statement->execute();
$statement->setFetchMode(PDO::FETCH_ASSOC);

// setup table and table headers
echo "<table>";
echo "<tr>";
echo "<th>User ID</th>";
echo "<th>User Name</th>";
echo "<th>User Email</th>";
echo "<th>Date Created</th>";
echo "</tr>";

while(($row = $statement->fetch()) !== false) {
  echo "<tr>";
  echo ("<td>" . $row["userId"] . "</td>");
  echo ("<td>" . $row["username"] . "</td>");
  echo ("<td>" . $row["userEmail"] . "</td>");
  echo ("<td>" . $row["dateCreated"] . "</td>");
  echo "</tr>";
}
echo "</table>";  // end table

$pdo = null;

} catch (PDOException $e){
    echo $e->getMessage();
}

 ?>
