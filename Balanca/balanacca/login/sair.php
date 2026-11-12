<?php
session_start();
$_SESSION = [];
session_destroy();
header('Location: index.html', true, 303);
exit;
