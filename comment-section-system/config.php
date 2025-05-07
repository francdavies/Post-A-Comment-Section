<?php
  $conn = new mysqli("localhost", "root", "", "comment_section");
  if($conn->connect_error){
    die("Failed to post!" .$conn->connect_error);
  }

?>