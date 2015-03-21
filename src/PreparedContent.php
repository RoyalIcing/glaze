<?php
/*
** Copyright 2014 Patrick Smith
** http://www.burntcaramel.com/
** Released under the MIT license: http://opensource.org/licenses/MIT
*/

namespace BurntCaramel\Glaze
{
	class PreparedContent extends PreparedItem
	{
		public function __construct($contentValue, $contentType, $spacingContent = null)
		{
			$this->contentValue = $contentValue;
			$this->contentType = $contentType;
			$this->spacingContent = $spacingContent;
		}
	
		/**
		*	Appends the item (element or content) to the receiver.
		*/
		public function appendPreparedItem($item)
		{
			$contentValue = (array)($this->contentValue);
		
			$contentValue[] = $item;
		
			$this->contentValue = $contentValue;
		}
	
		/**
		*	Displays the content.
		*/
		public function serve($options = null)
		{
			$contentValue = $this->contentValue;
			$contentType = $this->contentType;
			$spacingContent = $this->spacingContent;
		
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
						$lastServeReturnValue = Serve::serve($contentValueItem, $internalOptions);
		
						if ($i < $count && isset($spacingContent)):
							Serve::serve($spacingContent, $internalOptions);
						endif;
					endif;
				endforeach;
			// If is string:
			elseif (is_string($contentValue)):
				$lastServeReturnValue = Serve::serve($contentValue, $internalOptions);
			endif;
		
			return $lastServeReturnValue;
		}
	}
}