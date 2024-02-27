<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>calculator</title>
    <link rel="stylesheet" type="text/css" href="calculator.css">
</head>

<!-- <body background="background.jpg"> -->
<body>
    <h1>Powerful Calculator Made by Fiona Xu</h1>
    <form class="cal" method="GET">
        <div class="grid-container">
            <div class="grid-item">
                <p>Please input the 1st number:</p>
                <input type="text" name="num1" placeholder="Input valid number" required>
            </div>
            <div class="grid-item">
                <p>Please input the 2nd number:</p>
                <input type="text" name="num2" placeholder="Input valid number" required>
            </div>
            <div class="grid-item">
                <p>Choose the operators:</p>
                <p><input type ="radio" name="op" value = "+" required> +</p>
                <p><input type ="radio" name="op" value = "-" required> -</p>
                <p><input type ="radio" name="op" value = "*" required> *</p>
                <p><input type ="radio" name="op" value = "/" required> /</p>
            </div>
            <div class="grid-item">
                <input type="submit" value="GO!">
            </div>
        </div>
        <?php
            function calculate($a, $b, $op){
                switch($op){
                    case '+':
                        return $a + $b;
                        break;
                    case '-':
                        return $a - $b;
                        break;
                    case '*':
                        return $a * $b;
                        break;
                    case '/':
                        if($b == 0){
                            return "invaild";
                            break;
                        }
                        else{
                            return $a / $b;
                            break;
                        }
                        
                }
            }
            if(isset($_GET['num1']) && isset($_GET['num2']) && isset($_GET['op'])){
                $num1 = $_GET['num1'];
                $num2 = $_GET['num2'];
                $operator = $_GET['op'];
                if(is_numeric($num1) && is_numeric($num2)){
                    $result = calculate($num1, $num2, $operator);
                    if($result == "invalid"){
                        echo "<p>Divisor can not be zero!</p>";
                    }
                    else{
                        echo "<div>$num1 $operator $num2 = $result</div>";
                    }
                }
                else{
                    echo "<p>Please input valid number!</p>";
                }
            }
        ?>
    </form>
</body>
</html>