<?php
$host = '172.31.22.43';
$username = 'Arjun200551705';
$password = '9T8bgMMQsI';
$database = 'Arjun200551705';

$db = mysqli_connect($host, $username, $password, $database);

if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
