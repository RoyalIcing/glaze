<?php
/*
Copyright 2013, 2014: Patrick Smith

This content is released under the MIT License: http://opensource.org/licenses/MIT
*/

define('BASE_PATH', dirname(__FILE__));
require_once(BASE_PATH. '/../glaze-old.php');

if ( !function_exists( 'microtime_float' )):
function microtime_float()
{
	list($usec, $sec) = explode(' ', microtime());
	return ((float)$usec + (float)$sec);
}
endif;

$startTime = microtime_float();

ob_start();
require(BASE_PATH. '/example.php');
$generatedHTML = ob_get_clean();

$endTime = microtime_float();
$duration = $endTime - $startTime;

echo "Took {$duration}ms to display\n";


$referenceHTML = file_get_contents(BASE_PATH. '/example.html');

if ($generatedHTML === $referenceHTML):
	echo 'Generated HTML matches reference file.';
else:
	echo 'Oops! Generated HTML does not match reference file!';
endif;
echo "\n";