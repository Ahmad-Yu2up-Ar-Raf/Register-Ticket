<?php 

$db =  mysqli_connect("localhost", "root", "", "ujiansts");



if($db->connect_error){
    echo"database error";
}