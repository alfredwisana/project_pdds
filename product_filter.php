<?php

require "connectmysql.php";

if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $start_date = date("Y-m-d", strtotime($start_date));
    $end_date = date("Y-m-d", strtotime($end_date));


    $sql = "SELECT ProductID,SUM(Quantity) as total_purchase FROM orders WHERE OrderDate >= '$start_date' AND OrderDate <= '$end_date' group by ProductID ORDER BY total_purchase DESC LIMIT 5";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    $res = $stmt->get_result();



    $i = 1;
    while ($rows = $res->fetch_assoc()) {
        echo "<tr>";
        echo "<th scope='row'>$i</th>";
        $cursors = $product->find(
            [
                'ProductID' => $rows['ProductID']
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
        echo "</tr>";
        $i = $i + 1;
    }
}
