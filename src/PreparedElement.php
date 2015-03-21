<?php
/*
** Copyright 2014 Patrick Smith
** http://www.burntcaramel.com/
** Released under the MIT license: http://opensource.org/licenses/MIT
*/

namespace BurntCaramel\Glaze
{
	class PreparedElement extends PreparedItem
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
	
		static protected function preparedItemForInput($contentValue = null, $contentType = TYPE_TEXT)
		{
			if (!isset($contentValue)):
				// For self closing elements.
				return null;
			elseif ($contentValue instanceof PreparedItem):
				return $contentValue;
			else:
				return Prepare::content($contentValue, $contentType);
			endif;
		}
	
		public function __construct($tagNameOrElementOptions, $contentValue = null, $contentType = TYPE_TEXT)
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
	
			if (!isset($valueType)) {
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
			if (!isset($attributeValueToCheck) || ($attributeValueToCheck === false)) {
				return;
			}
		
			$this->setAttribute($attributeName, isset($attributeValueToUse) ? $attributeValueToUse : $attributeValueToCheck, $valueType);
		}
	
		protected function getStoredClassNames()
		{
			if (!isset($this->attributes['class'])):
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
	
		public function appendPreparedItem($preparedItemToAppend)
		{
			if (!isset($this->innerPreparedItem)):
				$this->innerPreparedItem = $preparedItemToAppend;
			else:
				$innerPreparedItem = $this->innerPreparedItem;
			
				if (!($innerPreparedItem instanceof PreparedContent)):
					$innerPreparedItem = Prepare::content(array($innerPreparedItem));
					$this->innerPreparedItem = $innerPreparedItem;
				endif;
			
				$innerPreparedItem->appendPreparedItem($preparedItemToAppend);
			endif;
		
			return $preparedItemToAppend;
		}
	
		public function append($itemInput)
		{
			$preparedItemToAppend = self::preparedItemForInput($itemInput);
		
			$this->appendPreparedItem($preparedItemToAppend);
		
			return $preparedItemToAppend;
		}
	
		/**
		*	Use to make sure you have an element, wrapping with `$tagNameToAdd` if one from `$arrayOfTagNames` is not present.
		*	@param $tagNameToAdd Tag name to add if element's tag name is not in `$arrayOfTagNames`.
		*	@param $arrayOfTagNames Can be null
		*/
		public function ensureElementWithTagNames($tagNameToAdd, $arrayOfTagNames = null)
		{
			if (!isset($arrayOfTagNames)):
				$arrayOfTagNames = array($tagNameToAdd);
			endif;
			
			$indexOfCurrentTag = array_search($this->tagName, $arrayOfTagNames);
			if ($indexOfCurrentTag !== false):
				return $this;
			endif;
		
			// Wrap the receiver in a new element with the specified tag name.
			return new PreparedElement(
				$tagNameToAdd,
				$this
			);
		}
	
		/**
		*	Captures the displayed output (from the output buffer) until finishCapturingContent() is called.
		*/
		public function beginCapturingContent()
		{
			ob_start();
		}
	
		public function finishCapturingContent()
		{
			$capturedHTMLContent = ob_get_clean();
			$preparedContent = Prepare::contentWithUnsafeHTML($capturedHTMLContent);
			$this->append($preparedContent);
		}
	
		/**
		*	Displays the element with its attributes and content.
		*/
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
				$innerServeResult = Serve::serve($innerPreparedItem);
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
}