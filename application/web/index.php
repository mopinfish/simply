<?php

require '../bootstrap.php';

$req = new Request();
echo 'requestUri: ' . $req->getRequestUri() . '<br>';
echo 'baseUrl: ' . $req->getBaseUrl() . '<br>';
echo 'pathInfo: ' . $req->getPathInfo() . '<br>';
