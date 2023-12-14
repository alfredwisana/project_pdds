<?php
require "./connect.php";
require "./header.php";

// get top 5 product from all
$sql = "SELECT ProductID,SUM(Quantity) as total_purchase FROM orders group by ProductID ORDER BY total_purchase DESC LIMIT 5";
$stmt = $con->prepare($sql);
$stmt->execute();
$res = $stmt->get_result();

// get the product from mongo db
while ($row = $res->fetch_assoc()) {
    $cursor = $product->find(
        [
            'ProductID' => $row['ProductID']
        ],
        [
            'projection' => ['_id' => 0]
        ]
    );
}
foreach ($cursor as $doc) {
    foreach ($doc as $key => $value) {
        if (is_string($value)) {
            echo $key." ".$value;
        }
    }
}

?>

<style>
    #wrapper {
        margin: 0 auto;
        position: relative;

    }
</style>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Most Bought Product</title>
</head>

<body>
    <div id="wrapper">

        <label for="period">Start Date</label>
        <input type="date" id="start_period" name="period" min="1997-01-01" max="1998-12-31">
        <label for="period">End Date</label>
        <input type="date" id="end_period" name="period" min="1997-01-01" max="1998-12-31">

        <input type="button" value="Filter">
    </div>



</body>

</html>