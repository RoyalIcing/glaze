<?php
/*
Copyright 2013, 2014: Patrick Smith

This content is released under the MIT License: http://opensource.org/licenses/MIT
*/

define ('GLAZE_VERSION', '1.9.0');

define ('GLAZE_TYPE_TEXT', 'text');
define ('GLAZE_TYPE_URL', 'URL');
define ('GLAZE_TYPE_EMAIL_ADDRESS', 'emailAddress');
define ('GLAZE_TYPE_EMAIL_ADDRESS_MAILTO_URL', 'emailAddressMailtoURL');
define ('GLAZE_TYPE_SPACED_LIST_ATTRIBUTE', 'spacedListAttribute');
define ('GLAZE_TYPE_PREGLAZED', 'preglazed');

define ('GLAZE_PREPARE_CONTENT', 'prepareContent');
define ('GLAZE_PREPARE_ELEMENT', 'prepareElement');


if (!function_exists('burntCheck')):
	function burntCheck(&$valueToCheck, $default = null)
	{
		if (!empty($valueToCheck)) {
			return $valueToCheck;
		}
		else {
			return $default;
		}
	}
endif;


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

// PRIVATE
function glazeNumericallyEncodeString($string)
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

// Returns a number as 1st, 2nd, 3rd, 4th...
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
// PRIVATE
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

// PRIVATE
function glazeAttribute($attributeName, $attributeValue, $valueType = null)
{
	if (is_array($attributeValue) && isset($attributeValue['valueType'])) {
		$attributeOptions = $attributeValue;
		$attributeValue = $attributeOptions['value'];
		$valueType = $attributeOptions['valueType'];
	}
	
	if (empty($valueType)) {
		$valueType = glazeTypeForAttributeName($attributeName);
	}
	
	// Boolean false attribute (omitted)
	if ($attributeValue === false) {
		return '';
	}
	// Boolean true attribute (for HTML5 just the attribute name)
	else if ($attributeValue === true) {
		// Looks like | $name|
		return ' ' .$attributeName;
	}
	// Normal attribute with a text value
	else {
		// Looks like | $name="$value"|
		return ' ' .$attributeName. '="' .glazeValue($attributeValue, $valueType). '"';
	}
}

// PRIVATE
function glazeAttributeCheck($attributeName, &$attributeValueToCheck, $attributeValueToUse = null, $valueType = null)
{
	if (empty($attributeValueToCheck)) {
		return '';
	}
	
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
		// Also include list items
		case 'li':
		case 'nav':
			return true;
		case 'html':
		case 'head':
		case 'body':
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

function glazeElementTagAddNewLineAfterOpening($tagName)
{
	return glazeElementTagNameIsBlockLevel($tagName) || ($tagName === 'label');
}

function glazeElementTagAddNewLineAfterClosing($tagName)
{
	return glazeElementTagNameIsBlockLevel($tagName) || glazeElementTagNameBelongsInHead($tagName) || ($tagName === 'label');
}


/* Feeling Glazy? */

function glazyCheckContent(&$potentialContent)
{
	return burntCheck($potentialContent, false);
}

// PRIVATE
function glazyHasOpenElements()
{
	global $glazyOpenElements;
	
	return !empty($glazyOpenElements);
}

// PRIVATE
function glazyGetLatestOpenElementValueForKey($key)
{
	global $glazyOpenElements;
	
	$latestOpenElement = $glazyOpenElements[count($glazyOpenElements) - 1];
	
	$value = burntCheck($latestOpenElement[$key]);
	
	return $value;
}

// PRIVATE
function glazySetLatestOpenElementKeyReturningOldValue($key, $value)
{
	global $glazyOpenElements;
	
	$latestOpenElement = &$glazyOpenElements[count($glazyOpenElements) - 1];
	
	$oldValue = burntCheck($latestOpenElement[$key]);
	$latestOpenElement[$key] = $value;
	
	return $oldValue;
}

// ATTRIBUTES BUFFER

// PRIVATE
function glazyBeginAttributesBufferIfNeeded()
{
	global $glazyAttributesBuffer;
	
	if (!isset($glazyAttributesBuffer)) {
		$glazyAttributesBuffer = '';
	}
}

// PRIVATE
function glazyAddToAttributesBuffer($string)
{
	global $glazyAttributesBuffer;
	
	// If elements buffer is being used, append it there, otherwise just display as is.
	if (isset($glazyAttributesBuffer) && glazyHasOpenElements() && !glazyGetLatestOpenElementValueForKey('openingTagDone')) {
		$glazyAttributesBuffer .= $string;
	}
	else {
		echo $string;
	}
}

// PRIVATE
function glazyCopyAndCleanAttributesBuffer()
{
	global $glazyAttributesBuffer;
	
	$copy = $glazyAttributesBuffer;
	$glazyAttributesBuffer = '';
	
	return $copy;
}

function glazyEnsureOpeningTag()
{
	if (!glazyHasOpenElements()) {
		return;
	}
	
	// If opening tag hasn't been done yet
	if (!glazySetLatestOpenElementKeyReturningOldValue('openingTagDone', true)) {
		echo glazyCopyAndCleanAttributesBuffer();
		echo '>';
		
		if (glazeElementTagAddNewLineAfterOpening(glazyGetLatestOpenElementValueForKey('tagName'))) {
			echo "\n";
		}
	}
}

function glazyBeginContent($contentValueType = GLAZE_TYPE_PREGLAZED)
{
	glazyEnsureOpeningTag();
	
	glazySetLatestOpenElementKeyReturningOldValue('contentType', $contentValueType);
}

/* Glazy Attributes */
// Can be used by themselves.

function glazyAttribute($attributeName, $attributeValue, $valueType = null)
{
	$string = glazeAttribute($attributeName, $attributeValue, $valueType);
	
	glazyAddToAttributesBuffer($string);
}

function glazyAttributeCheck($attributeName, &$attributeValueToCheck, $attributeValueToUse = null, $valueType = null)
{
	$string = glazeAttributeCheck($attributeName, $attributeValueToCheck, $attributeValueToUse, $valueType);
	
	glazyAddToAttributesBuffer($string);
}

function glazyAttributesArray($attributes)
{
	if (empty($attributes)):
		return;
	endif;
	
	foreach ($attributes as $attributeName => $attributeValue):
		glazyAttributeCheck($attributeName, $attributeValue);
	endforeach;
}


function glazyContent($contentValue, $contentType = GLAZE_TYPE_TEXT)
{
	// FIXME: changing the content type midway is not supported.
//	if (false && glazyHasOpenElements()):
//		glazyBeginContent($contentType);
//		echo $contentValue;
//	else:
	echo glazeValue($contentValue, $contentType);
//	endif;
	
	// Do not escape twice, as currently this is
	// handled in glazyFinish() using ob_start() etc.
	///echo glazeValue($contentValue, $contentType);
}


// PRIVATE
function glazyElementInfoForPassedOptions($tagNameOrElementOptions)
{
	$attributes = array();
	
	// String of form: tagName#elementID.multiple.class.names
	// The ID and classes are optional.
	if (is_string($tagNameOrElementOptions)):
		// Extract classes
		$parts = explode('.', $tagNameOrElementOptions);
		$elementInfo = $parts[0];
		// Add found classes
		if (count($parts) > 1):
			$elementClassNameParts = array_slice($parts, 1);
		endif;
		
		// Extract ID
		$parts = explode('#', $elementInfo);
		// Tag name is the left over
		$tagName = $parts[0];
		// Add found ID
		if (count($parts) > 1):
			$elementID = $parts[1];
		endif;
		
		// Set attributes, in our preferred order.
		if (!empty($elementID)):
			$attributes['id'] = $elementID;
		endif;
		
		if (!empty($elementClassNameParts)):
			$attributes['class'] = $elementClassNameParts;
		endif;
	// Array with required tagName and optional attributes as extra keys.
	elseif (is_array($tagNameOrElementOptions)):
		// Tag name is specified in the dict
		$tagName = $tagNameOrElementOptions['tagName'];
		// Attributes are all the other keys.
		$attributes = $tagNameOrElementOptions;
		unset($attributes['tagName']);
	endif;
	
	return array(
		'tagName' => $tagName,
		'attributes' => $attributes
	);
}

function glazyPrepareContentJoinedBy($contentValue, $contentType = GLAZE_TYPE_TEXT, $spacingHTML = '')
{
	if (empty($contentValue)):
		return $contentValue;
	endif;
	
	return array(
		'glazyPrepare' => GLAZE_PREPARE_CONTENT,
		'contentValue' => $contentValue,
		'contentType' => $contentType,
		'spacingHTML' => $spacingHTML
	);
}

function glazyPrepareContent($contentValue, $contentType = GLAZE_TYPE_TEXT)
{
	return glazyPrepareContentJoinedBy($contentValue, $contentType, "\n");
}

function glazyPrepareContentJoinedByLineBreaks($contentValue, $contentType = GLAZE_TYPE_TEXT)
{
	return glazyPrepareContentJoinedBy($contentValue, $contentType, '<br>');
}

function glazyPrepareContentWithUnsafeHTML($contentValue)
{
	return glazyPrepareContentJoinedBy($contentValue, GLAZE_TYPE_PREGLAZED, '');
}

function glazyServeContent($preparedContent)
{
	$contentValue = $preparedContent['contentValue'];
	$contentType = $preparedContent['contentType'];
	$spacingHTML = $preparedContent['spacingHTML'];
	
	// If is ordered-array:
	if (is_array($contentValue)):
		$contentValueArray = (array)$contentValue;
		$count = count($contentValueArray);
		$i = 0;
		foreach ($contentValueArray as $contentValueItem):
			$i++;
			
			if (!empty($contentValueItem)):
				glazyServe($contentValueItem, $contentType);
		
				if ($i < $count):
					echo $spacingHTML;
				endif;
			endif;
		endforeach;
	// If is string:
	elseif (is_string($contentValue)):
		glazyContent($contentValue, $contentType);
	endif;
}

function glazyPrepareElement($tagNameOrElementOptions, $contentValue = null, $contentType = GLAZE_TYPE_TEXT)
{
	if ($contentValue === false):
		return false;
	endif;
	
	$elementInfo = glazyElementInfoForPassedOptions($tagNameOrElementOptions);
	
	if (!isset($contentValue)):
		$innerPreparedInfo = null;
	elseif (!empty($contentValue['glazyPrepare'])):
		$innerPreparedInfo = $contentValue;
	else:
		$innerPreparedInfo = glazyPrepareContentJoinedBy($contentValue, $contentType, '');
	endif;
	
	$elementInfo['glazyPrepare'] = GLAZE_PREPARE_ELEMENT;
	$elementInfo['innerPreparedInfo'] = $innerPreparedInfo;
	
	return $elementInfo;
}

// TODO work out where extra attributes go for this and document it.
function glazyPrepareLinkToURL($URL, $innerPreparedInfo = null)
{
	return glazyPrepareElement(array(
		'tagName' => 'a',
		'href' => $URL
	), $innerPreparedInfo);
}

function glazyServeElement($preparedElement)
{
	if (empty($preparedElement)):
		return;
	endif;
	
	$tagName = $preparedElement['tagName'];
	$attributes = $preparedElement['attributes'];
	
	glazyEnsureOpeningTag();
	echo "<$tagName";
	
	if (!empty($attributes)):
		glazyAttributesArray($attributes);
	endif;
	
	echo '>';
	
	if (glazeElementTagAddNewLineAfterOpening($tagName)):
		echo "\n";
	endif;
	
	$innerPreparedInfo = $preparedElement['innerPreparedInfo'];
	if (isset($innerPreparedInfo)):
		glazyServe($innerPreparedInfo);
	endif;
	
	if (!glazeElementTagNameIsSelfClosing($tagName)):
		echo "</$tagName>";
	endif;
	
	if (glazeElementTagAddNewLineAfterClosing($tagName)):
		echo "\n";
	endif;
}

function glazyServe($preparedInfoOrString, $contentType = GLAZE_TYPE_TEXT)
{
	if (is_string($preparedInfoOrString)):
		$contentValue = $preparedInfoOrString;
		glazyContent($contentValue, $contentType);
	elseif (is_array($preparedInfoOrString) && !empty($preparedInfoOrString['glazyPrepare'])):
		$prepareType = $preparedInfoOrString['glazyPrepare'];
		if ($prepareType == GLAZE_PREPARE_CONTENT):
			glazyServeContent($preparedInfoOrString);
		elseif ($prepareType == GLAZE_PREPARE_ELEMENT):
			glazyServeElement($preparedInfoOrString);
		endif;
	endif;
}

function glazyElement($tagNameOrElementOptions, $contentValue = null, $contentType = GLAZE_TYPE_TEXT)
{
	glazyServeElement(glazyPrepareElement($tagNameOrElementOptions, $contentValue, $contentType));
}

/* Convenience for lazy debugging */
function glazyPrintR($object)
{
	glazyElement('pre', print_r($object, true));
}

function glazyBegin($tagNameOrElementOptions, $contentType = GLAZE_TYPE_PREGLAZED)
// TODO: Possibly the tagName in glazyBegin() would be optional,
//       or another begin function could be created - glazyPrepare(), glazyServe()
//       allowing you to wrap a whole bunch of elements easily together
//       and ensure they are closed.
{
	global $glazyOpenElements;
	
	glazyEnsureOpeningTag();
	
	$elementInfo = glazyPrepareElement($tagNameOrElementOptions);
	$tagName = $elementInfo['tagName'];
	$attributes = $elementInfo['attributes'];
	
	
	echo "<$tagName";
	
	glazyBeginAttributesBufferIfNeeded();
	
	if (!isset($glazyOpenElements)) {
		$glazyOpenElements = array();
	}
	$openElementsCountBefore = count($glazyOpenElements);
	
	if (empty($glazyOpenElements)) {
		ob_start();
	}
	else {
		ob_start();
	}
	
	$glazyOpenElements[] = array(
		'tagName' => $tagName,
		'openingTagDone' => false,
		'contentType' => $contentType
	);
	
	if (!empty($attributes)):
		glazyAttributesArray($attributes);
	endif;
	
	// Return info for glazyFinish.
	return array(
		'_glazyBegin_' => true,
		'tagName' => $tagName,
		'previousOpenElementsCount' => $openElementsCountBefore
	);
}

function glazyIsOpenElement($value)
{
	return is_array($value) && !empty($value['_glazyBegin_']);
}

function glazyFinish($openedElementInfo = null)
{
	global $glazyOpenElements;
	
	$repeatCount = 1;
	
	if (!empty($openedElementInfo['previousOpenElementsCount'])) {
		$previousCount = $openedElementInfo['previousOpenElementsCount'];
		$currentCount = count($glazyOpenElements);
		$repeatCount = $currentCount - $previousCount;
	}
	
	$outputtedString = ob_get_clean();
	//$outputtedString = ob_get_contents();
	//ob_clean();
	
	glazyEnsureOpeningTag();
	
	while ($repeatCount--):
		$elementInfo = array_pop($glazyOpenElements);
		
		$tagName = $elementInfo['tagName'];
		$contentType = $elementInfo['contentType'];
		
		if (!empty($outputtedString)) {
			if ($contentType === GLAZE_TYPE_PREGLAZED) {
				echo $outputtedString;
			}
			else {
				// Automatic glazing (escaping) of anything echoed.
				// Might be completely useless?
				// Is useful for WordPress the_title(), etc?
				// But that is already escaped.
				echo glazeValue($outputtedString, $contentType);
			}
		}
		
		
		if (!glazeElementTagNameIsSelfClosing($tagName)) {
			echo "</$tagName>";
		}
		
		if (glazeElementTagAddNewLineAfterClosing($tagName)) {
			echo "\n";
		}
		
		$outputtedString = null;
	endwhile;
}
