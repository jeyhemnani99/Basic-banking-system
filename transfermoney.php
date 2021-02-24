<?php
require_once('database.php');

// Requires PHP >= 8.1
/*
enum TransactionResult {
    INVALID_AMOUNT;
    INVALID_DEBIT_ACCOUNT;
    INVALID_BENEFICIARY_ACCOUNT;
    INSUFFICIENT_BALANCE;
    SUCCESS;
    DATABASE_FAILURE;
}*/
const INVALID_AMOUNT = 0;
const INVALID_DEBIT_ACCOUNT = 1;
const INVALID_BENEFICIARY_ACCOUNT = 2;
const INSUFFICIENT_BALANCE = 3;
const SUCCESS = 4;
const DATABASE_FAILURE = 5;

// Minimum account balance
const MIN_BALANCE = 1000;
    

function make_transaction($account_number, $beneficiary_account_number, $amount) {
    global $db, $INVALID_AMOUNT, $INVALID_DEBIT_ACCOUNT, $INVALID_BENEFICIARY_ACCOUNT, $INSUFFICIENT_BALANCE, $SUCCESS, $DATABASE_FAILURE;
    $float_amount = floatval($amount);
    
    if ($float_amount == 0) {
        // Invalid amount
        return INVALID_AMOUNT;
    }
    
    $balance_query = "SELECT (account_balance - $float_amount) AS remainder FROM ACCOUNTS WHERE account_number=$account_number LIMIT 1";
        
    if (!($result = mysqli_query($db, $balance_query)) || !($row = mysqli_fetch_assoc($result)) || intval($row['remainder']) < MIN_BALANCE) {
        // Insufficient balance
        return $row ? INSUFFICIENT_BALANCE : INVALID_DEBIT_ACCOUNT;
    } 
    
    $query = "INSERT INTO TRANSACTIONS(account_id, beneficiary_account_id, amount) VALUES((SELECT account_id FROM ACCOUNTS WHERE account_number=".$account_number." LIMIT 1), (SELECT account_id FROM ACCOUNTS WHERE account_number=".$beneficiary_account_number." LIMIT 1), ".$float_amount.")";

    if (mysqli_query($db, $query)) {
        $balance_update_query = "UPDATE ACCOUNTS SET account_balance=account_balance-$float_amount WHERE account_number=$account_number";
        $beneficiary_balance_update_query = "UPDATE ACCOUNTS SET account_balance=account_balance+$float_amount WHERE account_number=$beneficiary_account_number";
        
        mysqli_query($db, $balance_update_query);
        if (mysqli_affected_rows($db)) {
            if (mysqli_query($db, $beneficiary_balance_update_query)) {
                return mysqli_affected_rows($db) ? SUCCESS : DATABASE_FAILURE;
            } else {
                echo mysqli_error($db);
                return DATABASE_FAILURE;
            }
        } else {
            echo mysqli_error($db);
            return DATABASE_FAILURE;
        }
    } else {
        if (mysqli_errno($db) == 1048) {
            if (mysqli_error($db) === "Column 'beneficiary_account_id' cannot be null") {
                return INVALID_BENEFICIARY_ACCOUNT;
            } else {
                return INVALID_DEBIT_ACCOUNT;
            }
        }
        return DATABASE_FAILURE;
    }
}

$debit_account_error = $beneficiary_account_error = $balance_error = $amount_error = false;

$debit_account_number = '';
$beneficiary_account_number = '';
$amount = '';
$transaction_succeed = false;

function render_page() {
    global $debit_account_error, $beneficiary_account_error, $balance_error, $amount_error, $debit_account_number, 
    $beneficiary_account_number, $amount, $transaction_succeed;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Money</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="homepage.css">
    <link rel="stylesheet" href="homepage.js">
    <style>
        body {
                background-image: url('gradient.png');
                background-repeat: no-repeat;
                background-size: cover;
            
            }
        
        form{
            color: whitesmoke;
        }
        
        button{
            background-color: transparent;
            color: white;
            border: none;
            font-size: large;
        }
        
        .b2{
            background-color: transparent;
            color: white;
            border: none;
            font-size: large;
        }
        
        .b3{
            text-align: right;
            padding-right: 40%;
            padding-top: 2%;
            font-size: large;
        }
        .b4{
            text-align: center;
        }
        .b5{
            text-align: center;
        }
    </style>
</head>
<body>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <div class="b3">
        Debit Account Number:
        <input  type="number" name="debit_account_number" value="<?php echo $debit_account_number; ?>"/>
            
        <?php
        if ($debit_account_error) {
            echo '<small class="text-danger">Invalid account number!</small>';
        }
        ?>
        
        </br></br>
        Beneficiary Account number:
        <input type="number" name="beneficiary_account_number" value="<?php echo $beneficiary_account_number; ?>"/>
        
        <?php
        if ($beneficiary_account_error) {
            echo '<small class="text-danger">Invalid account number!</small>';
        }
        ?>
    
        </br></br>
        Amount:    
        <input type="number" name="amount" value="<?php echo $amount; ?>"/>
        <?php
        if ($amount_error) {
            echo '<small class="text-danger">Invalid Amount!</small>';
        }
        ?>
        </br></br>
        </div>
        <?php
        if($transaction_succeed) {
            echo '<h2>Transaction was successfull!</h2>';
        }
        ?>
        <div class="b4">
        <input class="b2" type="submit">
        </div>
    </form>
    <div  class="b5">
    <a href="index.php"><button>Home</button></a>
        </div>
        <script>
        function goBack()
        {
            window.history.back();
        }   
    </script>
        
    </form>
</body>
</html>

<?php
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $debit_account_error = $beneficiary_account_error = $balance_error = $amount_error = false;
    
    $debit_account_number = $_POST["debit_account_number"];
    $beneficiary_account_number = $_POST["beneficiary_account_number"];
    $amount = $_POST["amount"];
    $result = make_transaction($debit_account_number, $beneficiary_account_number, $amount);
    if ($result == SUCCESS) {
        $transaction_succeed = true;
        $debit_account_number = '';
        $beneficiary_account_number = '';
        $amount = '';
    } else {
        $transaction_succeed = false;
        switch ($result) {
            case INVALID_AMOUNT:
                $amount_error = true;
                break;
            case INVALID_DEBIT_ACCOUNT:
                $debit_account_error = true;
                break;
            case INVALID_BENEFICIARY_ACCOUNT:
                $beneficiary_account_error = true;
                break;
            case INSUFFICIENT_BALANCE:
                $balance_error = true;
                break;
            default:
                echo '<script>alert("Transaction failed! Internal error occurred")</script>';
                break;
        }
    }
} 
render_page();
?>