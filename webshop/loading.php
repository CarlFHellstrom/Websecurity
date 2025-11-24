<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Processing...</title>
    <meta http-equiv="refresh" content="2;url=receipt.php">
    <style>
    @keyframes spin { 
        from {transform:rotate(0deg);} 
        to {transform:rotate(360deg);} 
    }
    .loader {
        margin: 100px auto;
        border: 10px solid #f3f3f3;
        border-top: 10px solid #3498db;
        border-radius: 50%;
        width: 80px;
        height: 80px;
        animation: spin 1s linear infinite;
    }
    </style>
</head>
<body>
    <h2>Processing your order...</h2>
    <div class="loader"></div>
</body>
</html>
