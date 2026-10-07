<?php
session_start();
session_destroy();
header('Location: /billetterie/login.php');
exit();
