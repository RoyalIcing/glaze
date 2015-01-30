<?php
/*
** Copyright 2014 Patrick Smith
** http://www.burntcaramel.com/
** Released under the MIT license: http://opensource.org/licenses/MIT
*/

namespace BurntCaramel
{

class Glaze
{
	const TYPE_TEXT = 'text';
	const TYPE_URL = 'URL';
	const TYPE_EMAIL_ADDRESS = 'emailAddress';
	const TYPE_EMAIL_ADDRESS_MAILTO_URL = 'emailAddressMailtoURL';
	const TYPE_SPACED_LIST_ATTRIBUTE = 'spacedListAttribute';
	const TYPE_PREGLAZED = 'preglazed';
	
	
	/**
	*	Preserve the text for HTML output (escapes it)
	*
	*	@param string $string The string to preserve
	*	@return string The escaped string
	*/
	static public function text($string)
	{
		// Convert to UTF-8 if it's not already.
		$string = mb_convert_encoding($string, 'UTF-8', mb_detect_encoding($string));
		// Encode quotes too: this function covers general and attribute text in one.
		return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
	}
	
	/**
	*	Preserve the URL for HTML output (escapes it)
	*
	*	@param string $URLString The URL to preserve
	*	@return string The escaped URL string
	*/
	static public function URL($URLString)
	{
		return self::text($URLString);
	}
	
	static protected function numericallyEncodeString($string)
	{
		// Based on http://github.com/mdesign/md.spam_me_not.ee_addon/blob/master/plugins/pi.md_spam_me_not.php
		$stringLength = strlen($string);
		$stringDisplay = '';
		for ($i = 0; $i < $stringLength; $i++):
			$method = rand(1, 2);
			$characterOrdinal = ord($string[$i]);
			$encodedCharacter = '&#' .( ($method === 1)? $characterOrdinal : 'x' .dechex($characterOrdinal) ). ';';
			$stringDisplay .= $encodedCharacter;
		endfor;
		
		return $stringDisplay;
	}
	
	/**
	*	Preserve the email address for semi-obsfucated HTML output
	*
	*	@param string $emailAddress The email address to preserve
	*	@return string The semi-obsfucated and escaped email address
	*/
	static public function emailAddress($emailAddress)
	{
		return self::numericallyEncodeString($emailAddress);
	}
	
	/**
	*	Preserve the email address for semi-obsfucated HTML output and display it as a mailto: URL
	*
	*	@param string $emailAddress The email address to display
	*	@return string The semi-obsfucated and escaped mailto: email address URL
	*/
	static public function emailAddressMailtoURL($emailAddress)
	{
		$emailAddressParts = explode('@', $emailAddress);
		$emailAddressURL = 'mailto:' .rawurlencode($emailAddressParts[0]). '@' .rawurlencode($emailAddressParts[1]);
	
		return self::numericallyEncodeString($emailAddressURL);
	}
	
	/**
	*	Use preferred types for certain attributes.
	*
	*	@param string $attributeName The name of the attribute
	*	@return string The preferred type
	*/
	static public function defaultTypeForAttributeName($attributeName)
	{
		$attributeName = strtolower($attributeName);
	
		if ($attributeName === 'class'):
			return self::TYPE_SPACED_LIST_ATTRIBUTE;
		elseif ($attributeName === 'href' || $attributeName === 'src'):
			return self::TYPE_URL;
		endif;
	
		return self::TYPE_TEXT;
	}
	
	/**
	*	Process the input value, the method depending on the optional `$valueType` argument (default ::TYPE_TEXT)
	*
	*	@param string $value The name of the attribute
	*	@param string $valueType Optional type to process the value as
	*	@return string The preserved value
	*/
	static public function value($value, $valueType = null)
	{
		if ($valueType === self::TYPE_PREGLAZED) {
			return $value;
		}
		else if ($valueType === self::TYPE_URL) {
			return self::URL($value);
		}
		else if ($valueType === self::TYPE_EMAIL_ADDRESS) {
			return self::emailAddress($value);
		}
		else if ($valueType === self::TYPE_EMAIL_ADDRESS_MAILTO_URL) {
			return self::emailAddressMailtoURL($value);
		}
		
		if (($valueType === self::TYPE_SPACED_LIST_ATTRIBUTE) && is_array($value)) {
			$value = implode(' ', $value);
		}
	
		return self::text($value);
	}
	
	static public function check(&$potentialContent)
	{
		return !empty($potentialContent) ? $potentialContent : false;
	}
}


class GlazePreparedItem
{
	/**
	*	Serve the prepared item's content, echoing it
	*/
	public function serve($options = null)
	{
		
	}
}


class GlazePreparedElement extends GlazePreparedItem
{
	protected $tagName;
	protected $attributes;
	
	static protected function elementInfoForPassedOptions($tagNameOrElementOptions)
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
	
	static public function preparedItemForInput($contentValue = null, $contentType = Glaze::TYPE_TEXT)
	{
		if (!isset($contentValue)):
			// For self closing elements.
			return null;
		elseif ($contentValue instanceof GlazePreparedItem):
			return $contentValue;
		else:
			return GlazePrepare::content($contentValue, $contentType);
		endif;
	}
	
	public function __construct($tagNameOrElementOptions, $contentValue = null, $contentType = Glaze::TYPE_TEXT)
	{
		$elementInfo = self::elementInfoForPassedOptions($tagNameOrElementOptions);
		$this->tagName = $elementInfo['tagName'];
		$this->attributes = $elementInfo['attributes'];
		
		$this->innerPreparedItem = self::preparedItemForInput($contentValue, $contentType);
	}
	
	static public function prepareAttributeValue($attributeValue = true, $valueType = null)
	{
		if (isset($valueType)):
			return array(
				'value'	=> $attributeValue,
				'valueType' => $valueType
			);
		else:
			return $attributeValue;
		endif;
	}
	
	static public function serveAttribute($attributeName, $attributeValue = true, $valueType = null)
	{
		if (is_array($attributeValue) && isset($attributeValue['valueType'])) {
			$attributeOptions = $attributeValue;
			$attributeValue = $attributeOptions['value'];
			$valueType = $attributeOptions['valueType'];
		}
	
		if (empty($valueType)) {
			$valueType = Glaze::defaultTypeForAttributeName($attributeName);
		}
	
		// Boolean false attribute (omitted)
		if ($attributeValue === false) {
			return;
		}
		// Boolean true attribute (HTML5 shows just the attribute name)
		else if ($attributeValue === true) {
			// Looks like | $name|
			echo ' ' .$attributeName;
		}
		// Normal attribute with a text value
		else {
			// Looks like | $name="$value"|
			echo ' ' .$attributeName. '="' .Glaze::value($attributeValue, $valueType). '"';
		}
	}
	
	static protected function serveAttributesArray($attributes)
	{
		if (empty($attributes)):
			return;
		endif;
	
		foreach ($attributes as $attributeName => $attributeValue):
			self::serveAttribute($attributeName, $attributeValue);
		endforeach;
	}
	
	static public function tagIsSelfClosing($tagName)
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

	static public function tagIsBlockLike($tagName)
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
			case 'hr':
			case 'label':
				return true;
			case 'html':
			case 'head':
			case 'body':
				return true;
			default:
				return false;
		endswitch;
	}
	
	static public function tagBelongsInHead($tagName)
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
	
	static protected function tagShouldAddNewLineAfterOpening($tagName)
	{
		return self::tagIsBlockLike($tagName);
	}

	static protected function tagShouldAddNewLineAfterClosing($tagName)
	{
		return self::tagIsBlockLike($tagName) || self::tagBelongsInHead($tagName);
	}
	
	
	public function setAttribute($attributeName, $attributeValue = true, $valueType = null)
	{
		$this->attributes[$attributeName] = self::prepareAttributeValue($attributeValue, $valueType);
	}
	
	public function setAttributeChecking($attributeName, &$attributeValueToCheck, $attributeValueToUse = null, $valueType = null)
	{
		if (empty($attributeValueToCheck)) {
			return;
		}
		
		$this->setAttribute($attributeName, isset($attributeValueToUse) ? $attributeValueToUse : $attributeValueToCheck, $valueType);
	}
	
	protected function getStoredClassNames()
	{
		if (empty($this->attributes['class'])):
			return array();
		endif;
		
		$preparedAttribute = $this->attributes['class'];
		if (!empty($preparedAttribute['valueType'])):
			return (array)($preparedAttribute['value']);
		else:
			return (array)($preparedAttribute);
		endif;
	}
	
	public function addClassNames($classNames)
	{
		$currentClassNames = $this->getStoredClassNames();
		$combinedClassNames = array_merge($currentClassNames, (array)($classNames));
		
		$this->attributes['class'] = $combinedClassNames;
	}
	
	public function append($itemInput)
	{
		$preparedItemToAppend = self::preparedItemForInput($itemInput);
		
		if (!isset($this->innerPreparedItem)):
			$this->innerPreparedItem = $preparedItemToAppend;
		else:
			$innerPreparedItem = $this->innerPreparedItem;
			
			if (!($innerPreparedItem instanceof GlazePreparedContent)):
				$innerPreparedItem = GlazePrepare::content(array($innerPreparedItem));
				$this->innerPreparedItem = $innerPreparedItem;
			endif;
			
			$innerPreparedItem->appendPreparedItem($preparedItemToAppend);
		endif;
		
		return $preparedItemToAppend;
	}
	
	public function appendNewElement($tagNameOrElementOptions, $contentValue = null, $contentType = Glaze::TYPE_TEXT)
	{
		$element = GlazePrepare::element($tagNameOrElementOptions, $contentValue, $contentType);
		if (empty($element)):
			return;
		endif;
		
		$this->append($element);
		
		return $element;
	}
	
	public function beginCapturingContent()
	{
		ob_start();
	}
	
	public function finishCapturingContent()
	{
		$capturedHTMLContent = ob_get_clean();
		$preparedContent = GlazePrepare::contentWithUnsafeHTML($capturedHTMLContent);
		$this->append($preparedContent);
	}

	public function serve($options = null)
	{
		$serveResult = array();
		
		$tagName = $this->tagName;
		$attributes = $this->attributes;
	
		echo "<$tagName";
	
		if (!empty($attributes)):
			self::serveAttributesArray($attributes);
		endif;
	
		echo '>';
	
		$newLineAfterOpening = self::tagShouldAddNewLineAfterOpening($tagName);
		if ($newLineAfterOpening):
			echo "\n";
		endif;
	
		$innerPreparedItem = $this->innerPreparedItem;
		$innerServeResult = null;
		if (isset($innerPreparedItem)):
			$innerServeResult = GlazeServe::serve($innerPreparedItem);
		endif;
		
		if ($newLineAfterOpening && empty($innerServeResult['endedWithNewLine'])):
			echo "\n";
		endif;
	
		if (!self::tagIsSelfClosing($tagName)):
			echo "</$tagName>";
		endif;
	
		if (self::tagShouldAddNewLineAfterClosing($tagName)):
			echo "\n";
			$serveResult['endedWithNewLine'] = true;
		endif;
		
		return $serveResult;
	}
}


class GlazePreparedContent extends GlazePreparedItem
{
	public function __construct($contentValue, $contentType, $spacingHTML)
	{
		$this->contentValue = $contentValue;
		$this->contentType = $contentType;
		$this->spacingHTML = $spacingHTML;
	}
	
	public function appendPreparedItem($item)
	{
		$contentValue = (array)$this->contentValue;
		
		$contentValue[] = $item;
		
		$this->contentValue = $contentValue;
	}
	
	public function serve($options = null)
	{
		$contentValue = $this->contentValue;
		$contentType = $this->contentType;
		$spacingHTML = $this->spacingHTML;
		
		$internalOptions = array(
			'type' => $contentType
		);
		
		$lastServeReturnValue = null;
	
		// If is ordered-array:
		if (is_array($contentValue)):
			$contentValueArray = (array)$contentValue;
			$count = count($contentValueArray);
			$i = 0;
			foreach ($contentValueArray as $contentValueItem):
				$i++;
				
				if (isset($contentValueItem) && ($contentValueItem !== false)): // Cannot use empty as it discard '0' dumbly.
					$lastServeReturnValue = GlazeServe::serve($contentValueItem, $internalOptions);
		
					if ($i < $count):
						echo $spacingHTML;
					endif;
				endif;
			endforeach;
		// If is string:
		elseif (is_string($contentValue)):
			$lastServeReturnValue = GlazeServe::serve($contentValue, $internalOptions);
		endif;
		
		return $lastServeReturnValue;
	}
}

class GlazePrepare
{
	static public function contentSeparatedBy($contentValue, $contentType = Glaze::TYPE_TEXT, $spacingHTML = '')
	{
		if ($contentValue === false):
			return false;
		endif;
	
		return new GlazePreparedContent(
			$contentValue,
			$contentType,
			$spacingHTML
		);
	}
	
	static public function content($contentValue, $contentType = Glaze::TYPE_TEXT)
	{
		return self::contentSeparatedBy($contentValue, $contentType, '');
	}
	
	static public function contentSeparatedBySpaces($contentValue, $contentType = Glaze::TYPE_TEXT)
	{
		return self::contentSeparatedBy($contentValue, $contentType, ' ');
	}
	
	static public function contentSeparatedBySoftLineBreaks($contentValue, $contentType = Glaze::TYPE_TEXT)
	{
		return self::contentSeparatedBy($contentValue, $contentType, "<br>\n");
	}

	static public function contentWithUnsafeHTML($contentValue)
	{
		return self::contentSeparatedBy($contentValue, Glaze::TYPE_PREGLAZED, '');
	}
	
	
	static public function element($tagNameOrElementOptions, $contentValue = null, $contentType = Glaze::TYPE_TEXT)
	{
		if ($contentValue === false):
			return false;
		endif;
	
		return new GlazePreparedElement(
			$tagNameOrElementOptions,
			$contentValue,
			$contentType
		);
	}
}


class GlazeServe
{
	static public function attribute($attributeName, $attributeValue = true, $valueType = null)
	{
		GlazePreparedElement::serveAttribute($attributeName, $attributeValue, $valueType);
	}
	
	static public function attributeChecking($attributeName, &$attributeValueToCheck, $attributeValueToUse = null, $valueType = null)
	{
		// This is the check, if the value doesn't exist then return.
		if (empty($attributeValueToCheck)) {
			return;
		}
	
		GlazePreparedElement::serveAttribute($attributeName, isset($attributeValueToUse) ? $attributeValueToUse : $attributeValueToCheck, $valueType);
	}
	
	static public function serve($preparedItem, $options = null)
	{
		if (!isset($preparedItem) || ($preparedItem === false)): // empty discards '0' too unfortunately.
			return null;
		elseif ($preparedItem instanceof GlazePreparedItem):
			return $preparedItem->serve($options);
		elseif (is_string($preparedItem)):
			$contentType = !empty($options['type']) ? $options['type'] : null;
			echo Glaze::value($preparedItem, $contentType);
			return null;
		endif;
	}
	
	static public function element($tagNameOrElementOptions, $contentValue = null, $contentType = Glaze::TYPE_TEXT)
	{
		$element = GlazePrepare::element($tagNameOrElementOptions, $contentValue, $contentType);
		$element->serve();
	}
	
	static public function printR($object)
	{
		self::element('pre', print_r($object, true));
	}
}

}