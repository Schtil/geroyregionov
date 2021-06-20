<?php

$requestUri = $_SERVER["REQUEST_URI"];
$requestUri = explode("?", $requestUri);
$reqAddr = $requestUri[0];
echo $reqAddr;