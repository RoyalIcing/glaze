<?php
/*
Copyright 2013 Patrick Smith

This content is released under the MIT License: http://opensource.org/licenses/MIT

*/

function glazeText($string)
{
	// Convert to UTF-8 if necessary.
	$string = mb_convert_encoding($string, 'UTF-8', mb_detect_encoding($string));
	return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function glazeURL($string)
{
	return glazeText($string);
}

function glazeEncodedText($string)
{
	// From http://github.com/mdesign/md.spam_me_not.ee_addon/blob/master/plugins/pi.md_spam_me_not.php
	$stringLength = strlen($string);
	$stringDisplay = '';
	for ($i = 0; $i < $stringLength; $i++) {
		$method = rand(1, 2);
		$characterOrdinal = ord($string[$i]);
		$encodedCharacter = '&#' .( ($method == 1)? $characterOrdinal : 'x' .dechex($characterOrdinal) ). ';';
		$stringDisplay .= $encodedCharacter;
	}
	return $stringDisplay;
}

function glazeValue($string, $valueType = null)
{
	if ($valueType === 'URL' || $valueType === 'url') {
		return glazeURL($string);
	}
	else if ($valueType === 'glazed') {
		return $string;
	}
	else {
		return glazeText($string);
	}
}

function glazeAttribute($attributeName, $attributeValue, $valueType = null)
{
	// Looks like | attributeName="attributeValue"|
	return ' '.$attributeName.'="'.glazeValue($attributeValue, $valueType).'"';
}

function glazeAttributeCheck($attributeName, &$attributeValueToCheck, $attributeValueToUse = null, $valueType = null)
{
	if (empty($attributeValueToCheck))
		return;
	
	return glazeAttribute($attributeName, isset($attributeValueToUse) ? $attributeValueToUse : $attributeValueToCheck, $valueType);
}
