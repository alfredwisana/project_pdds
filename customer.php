<?php

require "connectmysql.php";
require "header.php";

$sql = "SELECT CustomerID, SUM(TotalPrice) as total_purchase, CASE WHEN SUM(TotalPrice) > 80000 THEN 'High-Value' WHEN SUM(TotalPrice) > 20000 THEN 'Valuable' ELSE 'Low-Value' END AS customer_segment FROM orders GROUP BY CustomerID";
$stmt = $con->prepare($sql);
$stmt->execute();
$res = $stmt->get_result();

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

$labels = [];
$data = [];
while ($row = $result->fetch_assoc()) {
    array_push($labels, $row['customer_segment']);
    array_push($data, $row['customer_count']);
}

$labels_js = json_encode($labels);
$data_js = json_encode($data);

$col_title = [];
while ($row = $res->fetch_assoc()) {
    $cursor = $customer->find(
        [
            'CustomerID' => $row['CustomerID']
        ],
        [
            'projection' => ['_id' => 0]
        ]
    );

    foreach ($cursor as $doc) {
        $temp_title = [];
        foreach ($doc as $key => $value) {
            if (is_string($value)) {
                array_push($temp_title, $key);
            }
        }

        if (count($temp_title) > count($col_title)) {
            $col_title = $temp_title;
        }
    }
};
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
        overflow-x: auto;

    }

    #wrapper {
        margin: 0 auto;
        position: relative;
        width: 75%;
        overflow-x: auto;
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
        overflow-x: auto;
    }

    #table_title,
    #period {
        font-weight: bold;
        text-align: center;
        font-size: larger;
        background-color: lightgray;
    }
</style>

<body>
<div class="w3-sidebar w3-bar-block w3-border-right bg-amber-100" style="display:none" id="mySidebar">
        <button onclick="w3_close()" class="w3-bar-item w3-large"> &times;</button>
        <a href="index.php" class="w3-bar-item w3-button">Revenue</a>
        <a href="product.php" class="w3-bar-item w3-button">Product</a>
        <a href="customer.php" class="w3-bar-item w3-button">Customer</a>
    </div>

    <div class="w3-khaki p-3 shadow-2xl flex items-center">
        <div class="w-16">
            <button class="text-khaki text-3xl" onclick="w3_open()">☰</button>
        </div>
        <h1 class="text-3xl font-bold flex-1 text-center ml-4 ">Customer Segmentation</h1>
    </div>
    <div id="wrapper">

        <label for="start_period">Start Date</label>
        <input type="date" class="form-control datepicker" id="start_period" name="period" min="1996-01-01" max="1998-12-31">
        <label for="end_period">End Date</label>
        <input type="date" class="form-control datepicker" id="end_period" name="period" min="1996-01-01" max="1998-12-31">

        <input type="button" id="butt-filter" value="Filter">
        <div class="card p-2">
            <select class="form-select mr-4 " id="country" name="country">
                <option value="All">All</option>
                <!-- Populate options dynamically from database -->
                <?php
                $country_query = "SELECT DISTINCT Country FROM customers";
                $stmt = $con->prepare($country_query);
                $stmt->execute();
                $res = $stmt->get_result();
                while ($row = $res->fetch_assoc()) {
                    echo "<option value=" . $row['Country'] . " $selected>" . $row['Country'] . "</option>";
                }
                ?>
            </select>
            <button type="submit" id="country_filter" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4  mt-1">Select Country</button>
        </div>
        <h4 id="period"></h4>
        <div id="table_wrapper" class="table-responsive">
            <table>
                <thead>
                    <h4 class="table_title">Customer Segmentation Count</h4>
                    <tr>
                        <?php
                        foreach ($labels as $label) {

                            // while($labels_js){
                            echo '<th scope="col">' . $label . '</th>';
                        };
                        ?>
                    </tr>


                </thead>
                <tbody id="cust_cout">
                    <tr>
                        <?php
                        foreach ($data as $data) {

                            // while($labels_js){
                            echo '<th scope="row">' . $data . '</th>';
                        };
                        ?>
                    </tr>
                </tbody>
            </table>
            <table>
                <thead>
                    <h4 class="table_title">Customer Segmentation</h4>
                    <tr>
                        <th>No</th>
                        <?php
                        foreach ($col_title as $doc) {

                            echo '<th scope="col">' . $doc . '</th>';
                        };
                        ?>
                        <th scope="col"> Amount of Spending </th>
                        <th scope="col"> Customer Segmentation </th>
                    </tr>
                </thead>

                <tbody id="customer_data">

                    <?php
                    $sql = "SELECT CustomerID, SUM(TotalPrice) as total_purchase, CASE WHEN SUM(TotalPrice) >= 80000 THEN 'High-Value' WHEN SUM(TotalPrice) >= 20000 THEN 'Valuable' ELSE 'Low-Value' END AS customer_segment FROM orders GROUP BY CustomerID";
                    $stmt = $con->prepare($sql);
                    $stmt->execute();
                    $res = $stmt->get_result();

                    $i = 1;
                    while ($rows = $res->fetch_assoc()) { ?>
                        <tr>
                            <th scope='row'><?php echo $i; ?> </th>
                            <?php
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
                            ?>

                                        <th><?php echo $values; ?></th>
                            <?php
                                    }
                                }
                            } ?>
                            <th><?php echo $rows['total_purchase']; ?></th>
                            <th><?php echo $rows['customer_segment']; ?> </th>
                        </tr>
                    <?php $i = $i + 1;
                    }

                    ?>

                </tbody>
            </table>
        </div>

    </div>
</body>

</html>


<script>
    $(document).ready(function() {
        $('#butt-filter').on('click', function() {
            var v_start_date = $("#start_period").val();
            var v_end_date = $("#end_period").val();

            $.ajax({
                type: 'POST',
                url: "customer_filter.php",

                data: {
                    start_date: v_start_date,
                    end_date: v_end_date,

                },
                success: function(result) {
                    // $("#cust_cout").html(result);
                    $("#period").html("Period: " + v_start_date + " - " + v_end_date)

                    $("#customer_data").html(result);

                    console.log(result);
                }
            })
        });

        $('#country_filter').on('click', function() {
            var v_country = $("#country").val();


            $.ajax({
                type: 'POST',
                url: "customer_filter_country.php",

                data: {
                    country: v_country


                },
                success: function(result) {
                    // $("#cust_cout").html(result);
                    $("#period").html("Country: " + v_country);
                    $("#customer_data").html(result);

                    console.log(result);
                }
            })
        })
    });


    function w3_open() {
        document.getElementById("mySidebar").style.display = "block";
    }

    function w3_close() {
        document.getElementById("mySidebar").style.display = "none";
    }
</script>



<!-- <script>
    var ctx = document.getElementById('customer_segment').getContext('2d');
    var labels = <?= $labels_js ?>;
    var data = <?= $data_js ?>;

    var customersegment = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                label: labels,
                data: data,
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 205, 86)'
                ],
                hoverOffset: 4
            }]
        },


    });
</script> -->