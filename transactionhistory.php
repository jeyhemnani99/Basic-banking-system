<?php
require_once('database.php');

// `page` starts with 0
function get_transactions($page) {
    global $db;
    $page_size = 100;
    $page_offset = $page * $page_size;

    $query = "SELECT a.transaction_id, (SELECT account_number from ACCOUNTS WHERE account_id=a.account_id) as debit_account_number, (SELECT account_number from ACCOUNTS WHERE account_id=a.beneficiary_account_id) as beneficiary_account_number, a.amount, a.added FROM TRANSACTIONS AS a ORDER BY a.transaction_id DESC LIMIT $page_offset,$page_size";

    $transactions = array();
    if ($result = mysqli_query($db, $query)) {
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($transactions, $row);
        }
    } else {
        echo mysqli_error($db);
    }


    return $transactions;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History</title>
    <link rel="stylesheet" type="text/css" href="homepage.css">
    <style>
        body {
                background-image: url('gradient.png');
                background-repeat: no-repeat;
                background-size: cover;
            }
        .b4{
            margin-top: 10px;
            color: aliceblue;
            font-size: large;
            text-align: center;
        }
        .b5{
            color: white;
            background-color: transparent;
            border: none;
        }

    </style>
</head>
<body>
    <a href="index.php">
    <button class="b5"> Home </button></a>

    <div class="b4">
    <table style="width:100%" >
      <tr>
        <th>Transaction Id</th>
        <th>Debit Account Number</th>
        <th>Beneficiary Account Number</th>
        <th>Amount</th>
        <th>Date</th>
      </tr>
    </div>
    <div class="b6">
      <?php
        $transactions = get_transactions(0);
        foreach ($transactions as $transaction) {
            echo '
            <tr>
                <td>'.$transaction["transaction_id"].'</td>
                <td>'.$transaction["debit_account_number"].'</td>
                <td>'.$transaction["beneficiary_account_number"].'</td>
                <td>'.$transaction["amount"].'</td>
                <td>'.$transaction["added"].'</td>
            </tr>';
        }
        ?>
        </div>
    </table>
        </div>
</body>
</html>
