<?php

$query_str = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
parse_str($query_str, $params);

include "functions.php";

if (isset($_GET['pages']) && $_GET['pages'] == $params['pages']) {
    require "partials/{$params['pages']}.inc.php";
} else {
    require 'partials/home.inc.php';
}
