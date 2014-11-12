<?php
/*
Copyright 2013, 2014: Patrick Smith

This content is released under the MIT License: http://opensource.org/licenses/MIT
*/

define('BASE_PATH', dirname(__FILE__));
require_once(BASE_PATH. '/../glaze.php');

ob_start();
require(BASE_PATH. '/example.php');
$generatedHTML = ob_get_clean();

file_put_contents(BASE_PATH. '/example.html', $generatedHTML);
