<?php

if (isset()){



require "connect.php";

#MYSQL
$where_stat = "";
$sql = "SELECT ProductID,SUM(Quantity) as total_purchase FROM orders group by ProductID ORDER BY total_purchase DESC LIMIT 5";
$stmt = $con->prepare($sql);
$stmt->execute();
$res = $stmt->get_result();


// MONGO DB
while ($row = $res->fetch_assoc()) {
    $cursor = $product->find(['ProductID' => $row['total_purchase']]);
}
}
?>