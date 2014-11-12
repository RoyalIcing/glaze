<?php
/*
Copyright 2013, 2014: Patrick Smith

This content is released under the MIT License: http://opensource.org/licenses/MIT
*/

require_once(dirname(__FILE__). '/../glaze.php');

echo '<!doctype html>' ."\n";
$html = glazyBegin('html');
{
	glazyServe(glazyPrepareElement('head', array(
		glazyPrepareElement('title', 'An example of using glaze.php'),
		glazyPrepareElement(array('tagName' => 'meta', 'charset' => 'utf-8'))
	)));
	
	$body = glazyBegin('body');
	{
		glazyServe(glazyPrepareElement('header', array(
			glazyPrepareElement('h1', 'Glaze preserves your content by escaping any <html> characters.')
		)));
		
		glazyElement('p', 'Your content should be stored as it was written. Escaping first makes it difficult when content is used outside of the web, such as in an app.');
		
		glazyElement('p', array(
			'You can easily format what you write, adding ',
			glazyPrepareElement('strong', '<strong>'),
			' or ',
			glazyPrepareElement('em', '<em>'),
			' just by interspersing text with the function `glazyPrepareElement(tagName, content)`.'
		));
		
		
		
		glazyElement('h2', 'Check values before displaying.');
		
		//$info = json_decode($jsonString, true); // true: decodes as an array
		$info = array(
			'itemID' => 'fddf3tq3tt3t3',
			'published' => false,
			'genreIdentifier' => 'thriller',
			'salesCount' => 56,
			'selected' => true,
			'authorName' => 'John Smith',
			'itemDescription' => array(
				'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
				'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
				'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.'
			)
		);

		// Build a list of classes using an array, don't fuss with appending to a string
		$classNamesArray = array('item', 'book');
		$classNamesArray[] = !empty($info['published']) ? 'published' : 'upcoming';
		$classNamesArray[] = 'genre-' .$info['genreIdentifier']; // e.g. 'genre-thriller'

		// Begin the <div>
		$bookItemDiv = glazyBegin('div');
		{
			glazyAttribute('id', "bookItem-{$info['itemID']}");
	
			// Lets you use an array of strings for class attributes.
			glazyAttribute('class', $classNamesArray);

			// Only display the attribute if variable reference $info['salesCount'] is present.
			glazyAttributeCheck('data-sales-count', $info['salesCount']);
			glazyAttributeCheck('data-sales-count', $info['salesCount_NOPE']);
	
			// Only displays the attribute, with the value 'selected', if $info['selected'] is true.
			glazyAttributeCheck('selected', $info['selected'], 'selected');
			glazyAttributeCheck('selected-nope', $info['selected_NOPE'], 'selected');
	
			glazyElement('h5.authorName', glazyCheckContent($info['authorName']));
			glazyElement('p.description', glazyPrepareContentJoinedByLineBreaks(glazyCheckContent($info['itemDescription'])));
			glazyElement('p.description', glazyPrepareContentJoinedByLineBreaks(glazyCheckContent($info['itemDescription_NOPE'])));
	
			// Finish and close the </div>
		}
		glazyFinish($bookItemDiv);
		
		
		
		glazyElement('h2', 'Using already escaped information');
		
		
		$img = glazyBegin('img');
		{
			glazyAttribute('src', 'http://placehold.it/150x50');
			
			$escapedText = 'Bangers &amp; Mash';
			glazyAttribute('alt', $escapedText, GLAZE_TYPE_PREGLAZED);
		}
		glazyFinish($img);
		
		
		
		glazyElement('hr');
		glazyElement('p', array(
			'Updating this file by using: ',
			glazyPrepareElement('br'),
			glazyPrepareElement('code', 'php -f example-update.php')
		));
		glazyElement('p', array(
			'Check this file with the last version by using: ',
			glazyPrepareElement('br'),
			glazyPrepareElement('code', 'php -f example-check.php')
		));
		
		glazyElement('p', array(
			'You can download glaze.php from here: ',
			glazyPrepareElement(array(
				'tagName' => 'a',
				'href' => 'http://github.com/BurntCaramel/glaze'
			), 'github.com/BurntCaramel/glaze')
		));
		
		
	}
	glazyFinish($body);
}
glazyFinish($html);