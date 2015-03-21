<?php
/*
** Copyright 2014 Patrick Smith
** http://www.burntcaramel.com/
** Released under the MIT license: http://opensource.org/licenses/MIT
*/

namespace BurntCaramel\Glaze
{
	require_once(dirname(__FILE__). '/Prepare.php');
	
	class Serve
	{
		static public function attribute($attributeName, $attributeValue = true, $valueType = null)
		{
			PreparedElement::serveAttribute($attributeName, $attributeValue, $valueType);
		}
	
		static public function attributeChecking($attributeName, &$attributeValueToCheck, $attributeValueToUse = null, $valueType = null)
		{
			// This is the check, if the value doesn't exist then return.
			if (!isset($attributeValueToCheck) || ($attributeValueToCheck === false)) {
				return;
			}
	
			PreparedElement::serveAttribute($attributeName, isset($attributeValueToUse) ? $attributeValueToUse : $attributeValueToCheck, $valueType);
		}
	
		static public function serve($preparedItem, $options = null)
		{
			if (!isset($preparedItem) || ($preparedItem === false)): // empty discards '0' too unfortunately.
				return null;
			elseif ($preparedItem instanceof PreparedItem):
				return $preparedItem->serve($options);
			elseif (is_string($preparedItem)):
				$contentType = !empty($options['type']) ? $options['type'] : null;
				echo Glaze::value($preparedItem, $contentType);
				return null;
			endif;
		}
	
		static public function element($tagNameOrElementOptions, $contentValue = null, $contentType = TYPE_TEXT)
		{
			$element = Prepare::element($tagNameOrElementOptions, $contentValue, $contentType);
			$element->serve();
		}
	
		static public function printR($object)
		{
			self::element('pre', print_r($object, true));
		}
	}
}