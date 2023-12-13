<?php
require "./connect.php";
require "./header.php";

// get top 5 product
$sql = "SELECT ProductID,SUM(Quantity) as total_purchase FROM orders group by ProductID ORDER BY total_purchase DESC LIMIT 5";
$stmt = $con->prepare($sql);
$stmt->execute();
$res = $stmt->get_result();

// get the product from mongo db
while($row = $res->fetch_assoc()){

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
</body>
</html>