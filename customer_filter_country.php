<?php

require "connectmysql.php";

if (isset($_POST['country'])) {
    
    $country = $_POST['country'];
    

    if($country === 'All'){
    $sql = "SELECT o.CustomerID, SUM(TotalPrice) as total_purchase, CASE WHEN SUM(TotalPrice) >= 80000 THEN 'High-Value' WHEN SUM(TotalPrice) >= 20000 THEN 'Valuable' ELSE 'Low-Value' END AS customer_segment FROM orders o  GROUP BY o.CustomerID";
    }else{
        $sql = "SELECT o.CustomerID, SUM(TotalPrice) as total_purchase, CASE WHEN SUM(TotalPrice) >= 80000 THEN 'High-Value' WHEN SUM(TotalPrice) >= 20000 THEN 'Valuable' ELSE 'Low-Value' END AS customer_segment FROM orders o join customers c ON o.CustomerID = c.CustomerID WHERE c.Country = '$country' GROUP BY o.CustomerID";

    }
    $stmt = $con->prepare($sql);
    $stmt->execute();
    $res = $stmt->get_result();

    // count


    $sql = "SELECT customer_segment, COUNT(CustomerID) AS customer_count
FROM (
    SELECT
        CustomerID,
        SUM(TotalPrice) AS total_purchase,
        CASE
            WHEN SUM(TotalPrice) > 80000 THEN 'High-Value'
            WHEN SUM(TotalPrice) > 20000 THEN 'Valuable'
            ELSE 'Low-Value'
        END AS customer_segment
    FROM orders
    GROUP BY CustomerID
) AS subquery
GROUP BY customer_segment
ORDER BY customer_count DESC
";


    $statment = $con->prepare($sql);
    $statment->execute();
    $result = $statment->get_result();

    // while($rows = $result -> fetch_assoc()){

    //     echo '<th scope="row">' . $rows['customer_count'] . '</th>';
    // }




    $i = 1;
    while ($rows = $res->fetch_assoc()) {
        echo "<tr>";
        echo "<th scope='row'>$i</th>";
        $cursors = $customer->find(
            [
                'CustomerID' => $rows['CustomerID'],
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
