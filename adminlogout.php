<?php
session_start();
session_destroy();
header("Location: adminlogin.html");
exit();
?>