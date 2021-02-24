<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utd-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="homepage.css">
    <link href="homepage.js">
    <title>Basic Banking System</title>
    <style>
        body {
                background-image: url('bank2.png');
                background-repeat: no-repeat;
                background-size: cover;

            }

        .a1{
            color: white;
            margin-top: 3%;
            font-size: Large;
            text-align: left;
            padding-left: 5%;
        }
        button{
            background-color: transparent;
            color: white;
            border: none;
            font-size: large;
        }

        button-hover{
            color: #3292cc;
        }

        .a2{
            text-align: right;
            color: white;
            margin-right: 4%;
        }

        footer {
            color: aliceblue;
            text-align: left;
            padding-top: 50%;
            padding-left: 5%;
        }
    </style>
</head>

<body>

        <div class="a1">
            <h1>Welcome to Sparks Bank</h1>
        </div>
        <div class="a2">
            <a href="transfermoney.php">
                <button>make transaction</button></a>

            <a href="transactionhistory.php"><button>history</button></a>
        </div>

        <footer >
            <div class="b1">
                <p>&copy 2021. Made by <b>Jay Hemnani</b> <br> The Sparks Foundation</p>
            </div>
        </footer>

</body>

</html>