<?php
/*
Copyright 2013 Patrick Smith

This content is released under the MIT License: http://opensource.org/licenses/MIT

*/

define ('GLAZE_TYPE_TEXT', 'text');
define ('GLAZE_TYPE_URL', 'URL');
define ('GLAZE_TYPE_NUMERIC_ENTITY_ENCODED', 'numericEntityEncoded');
define ('GLAZE_TYPE_SPACED_LIST_ATTRIBUTE', 'spacedListAttribute');
define ('GLAZE_TYPE_PREGLAZED', 'preglazed');


function glazeText($string)
{
	// Convert to UTF-8 if necessary.
	$string = mb_convert_encoding($string, 'UTF-8', mb_detect_encoding($string));
	// Encode quotes too; this function covers general and attribute text in one.
	return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function glazeURL($string)
{
	return glazeText($string);
}

function glazeNumericEntityEncodedText($string)
{
	// Based on http://github.com/mdesign/md.spam_me_not.ee_addon/blob/master/plugins/pi.md_spam_me_not.php
	$stringLength = strlen($string);
	$stringDisplay = '';
	for ($i = 0; $i < $stringLength; $i++) {
		$method = rand(1, 2);
		$characterOrdinal = ord($string[$i]);
		$encodedCharacter = '&#' .( ($method === 1)? $characterOrdinal : 'x' .dechex($characterOrdinal) ). ';';
		$stringDisplay .= $encodedCharacter;
	}
	return $stringDisplay;
}

/* Numbers */

function glazeNumberWithOrdinals($number)
{
	$lastUnit = $number % 10;
	$lastTen = $number % 100;
	if ($lastUnit === 0 || $lastUnit >= 4 || ($lastTen >= 11 && $lastTen <= 13))
		$suffix = 'th';
	else if ($lastUnit === 1)
		$suffix = 'st';
	else if ($lastUnit === 2)
		$suffix = 'nd';
	else
		$suffix = 'rd';
	
	return $number . $suffix;
}

/* Dates */

function glazeFullDateTime($time)
{
	return strftime('%A, %e %B %Y %l:%M:%S %p', $time);
}

function glazeDayTime($time)
{
	$dayWithOrdinal = glazeNumberWithOrdinals(strftime('%e', $time));
	return strftime('%A ' .$dayWithOrdinal. ', %l:%M:%S %p', $time);
}

function glazeShortDate($time, $showYear = true)
{
	if ($showYear)
		return strftime('%e %B %Y', $time);
	else
		return strftime('%e %B', $time);
}


function glazeValue($value, $valueType = null)
{
	if ($valueType === GLAZE_TYPE_PREGLAZED) {
		return $value;
	}
	else if ($valueType === GLAZE_TYPE_URL) {
		return glazeURL($value);
	}
	else if ($valueType === GLAZE_TYPE_NUMERIC_ENTITY_ENCODED) {
		return glazeNumericEntityEncodedText($value);
	}
	else if ($valueType === GLAZE_TYPE_SPACED_LIST_ATTRIBUTE && is_array($value)) {
		$value = implode(' ', $value);
	}
	
	
	return glazeText($value);
}

/* Attributes */

// Used to automatically process certain named attributes.
function glazeTypeForAttributeName($attributeName)
{
	$attributeName = strtolower($attributeName);
	
	if ($attributeName === 'class') {
		return GLAZE_TYPE_SPACED_LIST_ATTRIBUTE;
	}
	else if ($attributeName === 'href' || $attributeName === 'src') {
		return GLAZE_TYPE_URL;
	}
	
	return GLAZE_TYPE_TEXT;
}

function glazeAttribute($attributeName, $attributeValue, $valueType = null)
{
	if (empty($valueType)) {
		$valueType = glazeTypeForAttributeName($attributeName);
	}
	
	// Looks like | name="value"|
	return ' '.$attributeName.'="'.glazeValue($attributeValue, $valueType).'"';
}

function glazeAttributeCheck($attributeName, &$attributeValueToCheck, $attributeValueToUse = null, $valueType = null)
{
	if (empty($attributeValueToCheck))
		return '';
	
	return glazeAttribute($attributeName, isset($attributeValueToUse) ? $attributeValueToUse : $attributeValueToCheck, $valueType);
}
