<?php

require_once 'restApi.php';

$URIComponents=parse_url($_SERVER['REQUEST_URI']);

if (isset($URIComponents['query'])) { 
    if(method_exists('API', $URIComponents['query'])&& (isset($_POST['data']))) {
       API::instance()->{$URIComponents['query']}($_POST['data']);
    }
}
else{
    include_once 'main.php';
}