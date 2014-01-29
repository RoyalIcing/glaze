<?php
/*
Author 2013: Patrick Smith

This content is released under the MIT License: http://opensource.org/licenses/MIT

*/

define ('GLAZE_VERSION', '1.5');

define ('GLAZE_TYPE_TEXT', 'text');
define ('GLAZE_TYPE_URL', 'URL');
define ('GLAZE_TYPE_EMAIL_ADDRESS', 'emailAddress');
define ('GLAZE_TYPE_EMAIL_ADDRESS_MAILTO_URL', 'emailAddressMailtoURL');
define ('GLAZE_TYPE_SPACED_LIST_ATTRIBUTE', 'spacedListAttribute');
define ('GLAZE_TYPE_PREGLAZED', 'preglazed');


function glazeText($string)
{
	// Convert to UTF-8 if it's not already.
	$string = mb_convert_encoding($string, 'UTF-8', mb_detect_encoding($string));
	// Encode quotes too: this function covers general and attribute text in one.
	return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function glazeURL($stringURL)
{
	return glazeText($stringURL);
}

/* private */ function glazeNumericallyEncodeString($string)
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

function glazeEmailAddress($emailAddress)
{
	return glazeNumericallyEncodeString($emailAddress);
}

function glazeEmailAddressMailtoURL($emailAddress)
{
	$emailAddressParts = explode('@', $emailAddress);
	$emailAddressURL = 'mailto:' .rawurlencode($emailAddressParts[0]). '@' .rawurlencode($emailAddressParts[1]);
	
	return glazeNumericallyEncodeString($emailAddressURL);
}

function glazeValue($value, $valueType = null)
{
	if ($valueType === GLAZE_TYPE_PREGLAZED) {
		return $value;
	}
	else if ($valueType === GLAZE_TYPE_URL) {
		return glazeURL($value);
	}
	else if ($valueType === GLAZE_TYPE_EMAIL_ADDRESS) {
		return glazeEmailAddress($value);
	}
	else if ($valueType === GLAZE_TYPE_EMAIL_ADDRESS_MAILTO_URL) {
		return glazeEmailAddressMailtoURL($value);
	}
	else if ($valueType === GLAZE_TYPE_SPACED_LIST_ATTRIBUTE && is_array($value)) {
		$value = implode(' ', $value);
	}
	
	
	return glazeText($value);
}

/* Garnish */

function garnishNumberWithOrdinals($number)
{
	$lastUnit = $number % 10;
	$tens = $number % 100;
	if ($lastUnit === 0 || $lastUnit >= 4 || ($tens >= 11 && $tens <= 13))
		$suffix = 'th';
	else if ($lastUnit === 1)
		$suffix = 'st';
	else if ($lastUnit === 2)
		$suffix = 'nd';
	else
		$suffix = 'rd';
	
	return $number . $suffix;
}

/* Attributes */

// Used to automatically pick out the type of certain attributes.
/* private */ function glazeTypeForAttributeName($attributeName)
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

/* private */ function glazeAttribute($attributeName, $attributeValue, $valueType = null)
{
	if (empty($valueType)) {
		$valueType = glazeTypeForAttributeName($attributeName);
	}
	
	// Looks like | $name="$value"|
	return ' ' .$attributeName. '="' .glazeValue($attributeValue, $valueType). '"';
}

/* private */ function glazeAttributeCheck($attributeName, &$attributeValueToCheck, $attributeValueToUse = null, $valueType = null)
{
	if (empty($attributeValueToCheck))
		return '';
	
	return glazeAttribute($attributeName, isset($attributeValueToUse) ? $attributeValueToUse : $attributeValueToCheck, $valueType);
}



function glazeElementTagNameIsSelfClosing($tagName)
{
	$tagName = strtolower($tagName);
	
	switch ($tagName):
		case 'area':
		case 'base':
		case 'br':
		case 'col':
		case 'command':
		case 'embed':
		case 'hr':
		case 'img':
		case 'input':
		case 'keygen':
		case 'link':
		case 'meta':
		case 'param':
		case 'source':
		case 'track':
		case 'wbr':
			return true;
		default:
			return false;
	endswitch;
}

function glazeElementTagNameIsBlockLevel($tagName)
{	
	$tagName = strtolower($tagName);
	
	// https://developer.mozilla.org/en-US/docs/HTML/Block-level_elements
	switch ($tagName):
		case 'address':
		case 'article':
		case 'aside':
		case 'audio':
		case 'blockquote':
		case 'canvas':
		case 'dd':
		case 'div':
		case 'dl':
		case 'fieldset':
		case 'figcaption':
		case 'figure':
		case 'footer':
		case 'form':
		case 'h1':
		case 'h2':
		case 'h3':
		case 'h4':
		case 'h5':
		case 'h6':
		case 'header':
		case 'hgroup':
		case 'hr':
		case 'noscript':
		case 'ol':
		case 'output':
		case 'p':
		case 'pre':
		case 'section':
		case 'table':
		case 'tfoot':
		case 'ul':
		case 'video':
			return true;
		default:
			return false;
	endswitch;
}

function glazeElementTagNameBelongsInHead($tagName)
{
	$tagName = strtolower($tagName);
	
	switch ($tagName):
		case 'title':
		case 'meta':
		case 'link':
			return true;
		default:
			return false;
	endswitch;
}


/* Feeling Glazy? */

function &glazyGetOpenElements($begin = false)
{
	global $glazyOpenElements;
	
	if ($begin and !isset($glazyOpenElements)) {
		$glazyOpenElements = array();
	}
	
	return $glazyOpenElements;
}

function &glazyGetElementsBuffer($options = null)
{
	global $glazyElementsBuffer;
	
	if (isset($options['clean'])) {
		$glazyElementsBuffer = null; // See http://au1.php.net/unset
	}
	
	if (isset($options['begin'])) {
		if (!isset($glazyElementsBuffer)) {
			$glazyElementsBuffer = '';
		}
	}
	
	return $glazyElementsBuffer;
}

function glazyAddToElementsBuffer($string)
{
	$glazyElementsBuffer = &glazyGetElementsBuffer();
	
	if (isset($glazyElementsBuffer)) {
		$glazyElementsBuffer .= $string;
	}
	else {
		echo $string;
	}
}

function glazyCopyAndCleanElementsBuffer()
{
	$glazyElementsBuffer = glazyGetElementsBuffer();
	$copy = $glazyElementsBuffer;
	
	glazyGetElementsBuffer(array('clean' => true));
	
	return $copy;
}

/* Glazy Attributes */
// Can be used by themselves.

function glazyAttribute($attributeName, $attributeValue, $valueType = null)
{
	$string = glazeAttribute($attributeName, $attributeValue, $valueType);
	
	glazyAddToElementsBuffer($string);
}

function glazyAttributeCheck($attributeName, &$attributeValueToCheck, $attributeValueToUse = null, $valueType = null)
{
	$string = glazeAttributeCheck($attributeName, $attributeValueToCheck, $attributeValueToUse, $valueType);
	
	glazyAddToElementsBuffer($string);
}

function glazyAttributesArray($attributes)
{
	if (empty($attributes)):
		return;
	endif;
	
	foreach ($attributes as $attributeName => $attributeValue):
		glazyAttribute($attributeName, $attributeValue);
	endforeach;
}


function glazyElement($elementInfo, $contentsValue = null, $valueType = null)
{
	$attributes = array();
	
	if (is_string($elementInfo)):
		// Classes
		$parts = explode('.', $elementInfo);
		
		$elementInfo = $parts[0];
		
		if (count($parts) > 1):
			$elementClassNameParts = array_slice($parts, 1);
			$attributes['class'] = $elementClassNameParts;
		endif;
		
		// ID
		$parts = explode('#', $elementInfo);
		
		$tagName = $parts[0];
		
		if (count($parts) > 1):
			$elementID = $parts[1];
			$attributes['id'] = $elementID;
		endif;
	endif;
	
	echo "<$tagName";
	
	if (!empty($attributes)):
		glazyAttributesArray($attributes);
	endif;
	
	echo '>';
	
	if (!is_null($contentsValue)):
		echo glazeValue($contentsValue, $valueType);
	endif;
	
	if (!glazeElementTagNameIsSelfClosing($tagName)):
		echo "</$tagName>";
	endif;
}


/* private */ function glazyEnsureLatestElementOpenTagIsDisplayed()
{
	$glazyOpenElements = &glazyGetOpenElements();
	if (empty($glazyOpenElements)) {
		return;
	}
	
	$latestOpenElement = &$glazyOpenElements[count($glazyOpenElements) - 1];
	
	if (!$latestOpenElement['openTagDone']) {
		echo glazyCopyAndCleanElementsBuffer();
		echo '>';
		
		$latestOpenElement['openTagDone'] = true;
	}
	
	$latestOpenElement2 = &$glazyOpenElements[count($glazyOpenElements) - 1];
}

function glazyBegin($tagName) // $glazeType = GLAZE_TYPE_PREGLAZED
{
	glazyEnsureLatestElementOpenTagIsDisplayed();
	
	echo "<$tagName";
	
	$glazyElementsBuffer = &glazyGetElementsBuffer(array('begin' => true));
	$glazyOpenElements = &glazyGetOpenElements(true);
	
	if (empty($glazyOpenElements)) {
		ob_start();
	}
	else {
		ob_start();
	}
	
	$glazyOpenElements[] = array('tagName' => $tagName, 'openTagDone' => false);
}

function glazyClose()
{
	$outputtedString = ob_get_clean();
	//$outputtedString = ob_get_contents();
	//ob_clean();
	
	glazyEnsureLatestElementOpenTagIsDisplayed();
	
	echo $outputtedString;
	
	$glazyOpenElements = &glazyGetOpenElements();
	$elementInfo = array_pop($glazyOpenElements);
	$tagName = $elementInfo['tagName'];
	
	if (!glazeElementTagNameIsSelfClosing($tagName)) {
		echo "</$tagName>";
	}
	
	if (glazeElementTagNameIsBlockLevel($tagName) || glazeElementTagNameBelongsInHead($tagName)) {
		echo "\n";
	}
	
	//ob_end_flush();
	
	//$glazyOpenElements = glazyGetOpenElements();
}



function glazyPrintR($object)
{
	glazyElement('pre', print_r($object, true));
}
