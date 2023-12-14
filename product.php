<?php
require "./connect.php";
require "./header.php";

// get top 5 product from all
$sql = "SELECT ProductID,SUM(Quantity) as total_purchase FROM orders group by ProductID ORDER BY total_purchase DESC LIMIT 5";
$stmt = $con->prepare($sql);
$stmt->execute();
$res = $stmt->get_result();

// get the product from mongo db
$col_title = [];
while ($row = $res->fetch_assoc()) {
    $cursor = $product->find(
        [
            'ProductID' => $row['ProductID']
        ],
        [
            'projection' => ['_id' => 0]
        ]
    );

    foreach ($cursor as $doc) {
        $temp_title = [];
        foreach ($doc as $key => $value) {
            if (is_string($value) || is_int($value) || is_double($value)||is_float($value)) {
                array_push($temp_title, $key);
            }
        }

        if (count($temp_title) > count($col_title)) {
            $col_title = $temp_title;
        }
    }
};

?>

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
    #period {
        font-weight: bold;
        text-align: center;
        font-size: larger;
        background-color: lightgray;
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

        <label for="start_period">Start Date</label>
        <input type="date" class="form-control datepicker" id="start_period" name="period" min="1996-01-01" max="1998-12-31">
        <label for="end_period">End Date</label>
        <input type="date" class="form-control datepicker" id="end_period" name="period" min="1996-01-01" max="1998-12-31">

        <input type="button" id="butt-filter" value="Filter">
        <center>
            <h4 id="period"></h4>

            <div id="table_wrapper" class="table-responsive">
                <table>
                    <thead>
                        <h4 id="table_title">Most Bought Product</h4>
                        <tr>
                            <th>No</th>
                            <?php
                            foreach ($col_title as $doc) {

                                echo '<th scope="col">' . $doc . '</th>';
                            };
                            ?>
                            <th scope="col"> Number of Saled </th>
                        </tr>
                    </thead>

                    <tbody id="product_data">

                        <?php
                        $sql = "SELECT ProductID,SUM(Quantity) as total_purchase FROM orders group by ProductID ORDER BY total_purchase DESC LIMIT 5";
                        $stmt = $con->prepare($sql);
                        $stmt->execute();
                        $res = $stmt->get_result();

                        $i = 1;
                        while ($rows = $res->fetch_assoc()) { ?>
                            <tr>
                                <th scope='row'><?php echo $i; ?> </th>
                                <?php
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
                                        if (is_string($value) || is_int($value) || is_double($value)) {
                                ?>

                                            <th><?php echo $values; ?></th>
                                <?php
                                        }
                                    }
                                } ?>
                                <th><?php echo $rows['total_purchase']; ?></th>
                            </tr>
                        <?php $i = $i + 1;
                        }

                        ?>

                    </tbody>
                </table>
            </div>
        </center>
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
                url: "product_filter.php",

                data: {
                    start_date: v_start_date,
                    end_date: v_end_date,

                },
                success: function(result) {
                    $("#period").html("Period: " v_start_date +" - "+ v_end_date)
                    $("#product_data").html(result);

                    console.log(result);
                }
            })
        })
    });
</script>