<?php

// mysql
$con = mysqli_connect('localhost', 'root', '', 'pdds');

// mongo db
$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");

require_once 'autoload.php';

$client = new MongoDB\Client();

$product = $client->project->products;
$customer = $client->project->customers;
?>