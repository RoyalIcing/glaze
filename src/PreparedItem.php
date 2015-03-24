<?php
/*
** Copyright 2014 Patrick Smith
** http://www.burntcaramel.com/
** Released under the MIT license: http://opensource.org/licenses/MIT
*/

namespace BurntCaramel\Glaze
{
	class PreparedItem
	{
		/**
		*	Appends an instance of PreparedElement or PreparedContent
		*	@return Returns the prepared item that was passed, possibly modified.
		*/
		public function appendPreparedItem($preparedItemToAppend)
		{
			// For subclasses.
			return null;
		}
	
		/**
		*	Appends a new PreparedElement made with the passed arguments.
		*/
		public function appendNewElement($tagNameOrElementOptions, $contentValue = null, $contentType = TYPE_TEXT)
		{
			$element = Prepare::element($tagNameOrElementOptions, $contentValue, $contentType);
			if (!isset($element) || $element === false):
				return;
			endif;
		
			$this->appendPreparedItem($element);
		
			return $element;
		}
	
		/**
		*	Appends a new HTML comment.
		*/
		public function appendInvisibleComment($contentValue = null, $contentType = TYPE_TEXT)
		{
			$content = Prepare::invisibleComment($contentValue, $contentType);
			if (!isset($content) || $content === false):
				return;
			endif;
		
			$this->appendPreparedItem($content);
		
			return $content;
		}
	
		/**
		*	Use to make sure you have an element, wrapping with $tagNameToAdd if one from $arrayOfTagNames is not present.
		*/
		public function ensureElementWithTagNames($tagNameToAdd, $arrayOfTagNames = null)
		{
			// Wrap the receiver in a new element with the specified tag name.
			return new PreparedElement(
				$tagNameToAdd,
				$this
			);
		}
	
		/**
		*	Make sure you have a <div> element, wrapping if one is not present.
		*/
		public function ensureDivElement()
		{
			return $this->ensureElementWithTagNames('div');
		}
	
		/**
		*	Make sure you have a <span> element, wrapping if one is not present.
		*/
		public function ensureSpanElement()
		{
			return $this->ensureElementWithTagNames('span');
		}
	
		/**
		*	Displays the receiver's content.
		*/
		public function serve($options = null)
		{
			// For subclasses.
		}
	}
}