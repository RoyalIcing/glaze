<?php
/*
** Copyright 2014 Patrick Smith
** http://www.burntcaramel.com/
** Released under the MIT license: http://opensource.org/licenses/MIT
*/

namespace BurntCaramel\Glaze
{
	require_once(dirname(__FILE__). '/PreparedItem.php');
	require_once(dirname(__FILE__). '/PreparedContent.php');
	require_once(dirname(__FILE__). '/PreparedElement.php');
	
	class Prepare
	{
		/**
		*	Returns prepared content separated by other content.
		*/
		static public function contentSeparatedBy($contentValue, $contentType = Glaze::TYPE_TEXT, $spacingContent = null)
		{
			if ($contentValue === false):
				return false;
			endif;
	
			return new PreparedContent(
				$contentValue,
				$contentType,
				$spacingContent
			);
		}
	
		/**
		*	Returns prepared content with the specified content type.
		*/
		static public function content($contentValue, $contentType = Glaze::TYPE_TEXT)
		{
			return self::contentSeparatedBy($contentValue, $contentType, '');
		}
	
		/**
		*	Returns prepared content separated by a single space.
		*/
		static public function contentSeparatedBySpaces($contentValue, $contentType = Glaze::TYPE_TEXT)
		{
			return self::contentSeparatedBy($contentValue, $contentType, ' ');
		}
	
		/**
		*	Returns prepared content separated by an HTML string.
		*/
		static public function contentSeparatedByHTML($contentValue, $contentType = Glaze::TYPE_TEXT, $spacingHTML = '')
		{
			if ($contentValue === false):
				return false;
			endif;
		
			$spacingContent = new PreparedContent(
				$spacingHTML,
				TYPE_UNSAFE_HTML
			);
	
			return new PreparedContent(
				$contentValue,
				$contentType,
				$spacingContent
			);
		}
	
		/**
		*	Returns prepared content separated by the <br> line break HTML element
		*/
		static public function contentSeparatedBySoftLineBreaks($contentValue, $contentType = Glaze::TYPE_TEXT)
		{
			return self::contentSeparatedByHTML($contentValue, $contentType, "<br>\n");
		}

		/**
		*	Returns prepared content with whatever HTML you need.
		*/
		static public function contentWithUnsafeHTML($contentValue)
		{
			return self::contentSeparatedBy($contentValue, Glaze::TYPE_UNSAFE_HTML, '');
		}
	
		/**
		*	Returns prepared element with the same arguments that PreparedElement's constructor takes.
		*	Checks if $contentValue is false, if it is then returns false.
		*/
		static public function element($tagNameOrElementOptions, $contentValue = null, $contentType = Glaze::TYPE_TEXT)
		{
			if ($contentValue === false):
				return false;
			endif;
	
			return new PreparedElement(
				$tagNameOrElementOptions,
				$contentValue,
				$contentType
			);
		}
	
		/**
		*	Returns prepared attribute value with a particular type.
		*/
		static public function attributeValue($attributeValue, $valueType = null)
		{
			return PreparedElement::prepareAttributeValue($attributeValue, $valueType);
		}
	
		/**
		*	Returns prepared HTML comment
		*/
		static public function invisibleComment($contentValue, $contentType = Glaze::TYPE_TEXT)
		{
			return self::content(array(
				self::contentWithUnsafeHTML('<!-- '),
				self::content($contentValue, $contentType),
				self::contentWithUnsafeHTML(' -->'."\n"),
			));
		}
	}
}