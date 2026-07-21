<?php

require_once __DIR__ . "/../../includes/db.php";

if(isset($_POST['save_price'])){

    $movie_id = $_POST['movie_id'];
    $price = $_POST['price'];


    $query = "INSERT INTO ticket_prices 
              (movie_id, price)
              VALUES
              ('$movie_id','$price')";


    mysqli_query($conn,$query);


    header("Location: ticket_pricing.php");
    exit();

}

?>