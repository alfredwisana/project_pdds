<?php

require "connect.php";
require "header.php";

$sql = "SELECT CustomerID, SUM(TotalPrice) as total_purchase, CASE WHEN SUM(TotalPrice) > 80000 THEN 'High-Value' WHEN SUM(TotalPrice) > 20000 THEN 'Valuable' ELSE 'Low-Value' END AS customer_segment FROM orders GROUP BY CustomerID";
$stmt = $con->prepare($sql);
$stmt->execute();
$res = $stmt->get_result();


while ($row = $res->fetch_assoc()) {
    $cursor = $customer->find(
        [
            'CustomerID' => $row['CustomerID']
        ],
        [
            'projection' => ['_id' => 0]
        ]
    );
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Segmentation Report</title>
</head>

<style>
    #table_wrapper {
        margin: 0 auto;
        position: relative;
    }

    #wrapper {
        margin: 0 auto;
        position: relative;
        width: 75%;
    }

    /* #upload {} */

    table,
    th,
    td {
        border: 1px solid black;
    }

    .table {
        margin: 0 auto;
        width: 100%;
        margin-right: 10rem;
    }

    #table_title,
    #status {
        font-weight: bold;
        text-align: center;
        font-size: larger;
        background-color: lightgray;
    }
</style>

<body>
    <div id="wrapper">

        <label for="period">Start Date</label>
        <input type="date" id="start_period" name="period" min="1996-01-01" max="1998-12-31">
        <label for="period">End Date</label>
        <input type="date" id="end_period" name="period" min="1996-01-01" max="1998-12-31">

        <input type="button" value="Filter">
    </div>

    <h4 id="status"></h4>
    <div id="table_wrapper">
        <table>
            <thead>
                <h4 id="table_title">Customer Segmentation</h4>
                <tr>
                    <?php
                    foreach ($cursor as $doc) {
                        foreach ($doc as $key => $value) {
                            if (is_string($key)) {
                                echo '<th scope="col">' . $key . '</th>';
                            }
                        }
                    };

                    ?>
                </tr>
            </thead>
            
            <div id="customer_data">

            </div>
        </table>
    </div>
</body>

</html>