<?php
//Servidor, usuario, password, bd
$mysqli = new mysqli("127.0.0.1", "root", "1234","optica");
if(mysqli_connect_errno()){
    echo "ERORR";
}
?>