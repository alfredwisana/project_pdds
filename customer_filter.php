<?php

require "connect.php";

if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $start_date = date("Y-m-d", strtotime($start_date));
    $end_date = date("Y-m-d", strtotime($end_date));


    $sql = "SELECT CustomerID, SUM(TotalPrice) as total_purchase, CASE WHEN SUM(TotalPrice) >= 80000 THEN 'High-Value' WHEN SUM(TotalPrice) >= 20000 THEN 'Valuable' ELSE 'Low-Value' END AS customer_segment FROM orders WHERE OrderDate >= '$start_date' AND OrderDate <= '$end_date' GROUP BY CustomerID";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    $res = $stmt->get_result();



    $i = 1;
    while($rows = $res->fetch_assoc()) {
        echo "<tr>";
        echo "<th scope='row'>$i</th>";
        $cursors = $customer->find(
            [
                'CustomerID' => $rows['CustomerID']
            ],
            [
                'projection' => ['_id' => 0]
            ]
        );
        foreach ($cursors as $docs) {
            foreach ($docs as $keys => $values) {
                if (is_string($values)) {

                    echo "<th>$values</th>";
                }
            }
        }
        echo "<th>" . $rows['total_purchase'] . "</th>";
        echo "<th>" . $rows['customer_segment'] . "</th>";
        echo "</tr>";
        $i = $i + 1;
    }
}
