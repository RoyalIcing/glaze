<?php
/*
Copyright 2013, 2014: Patrick Smith

This content is released under the MIT License: http://opensource.org/licenses/MIT
*/

require_once(dirname(__FILE__). '/../all.php');

use BurntCaramel\Glaze;
use BurntCaramel\Glaze\Prepare as GlazePrepare;
use BurntCaramel\Glaze\Serve as GlazeServe;

$html = GlazePrepare::element('html');
{
	$html->appendNewElement('head', array(
		Glaze\Prepare::element('title', 'An example of using glaze.php'),
		Glaze\Prepare::element(array('tagName' => 'meta', 'charset' => 'utf-8'))
	));
	
	$body = $html->appendNewElement('body');
	{
		$body->appendNewElement('header', array(
			Glaze\Prepare::element('h1', 'Glaze preserves your content by escaping any <html> characters.')
		));
		
		$body->appendNewElement('p', 'Your content should be stored as it was written. Escaping first makes it difficult when content is used outside of the web, such as in an app.');
		
		$body->appendNewElement('p', array(
			'You can easily format what you write, adding ',
			Glaze\Prepare::element('strong', '<strong>'),
			' or ',
			Glaze\Prepare::element('em', '<em>'),
			' just by interspersing text with `GlazePrepare::element(tagName, content)`.'
		));
		
		
		
		$body->appendNewElement('h2', 'Check values before displaying.');
		
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
		$bookItemDiv = $body->appendNewElement('div');
		{
			$bookItemDiv->setAttribute('id', "bookItem-{$info['itemID']}");
	
			// Lets you use an array of strings for class attributes.
			//$bookItemDiv->setAttribute('class', $classNamesArray);
			$bookItemDiv->addClassNames($classNamesArray);

			// Only display the attribute if variable reference $info['salesCount'] is present.
			$bookItemDiv->setAttributeChecking('data-sales-count', $info['salesCount']);
			$bookItemDiv->setAttributeChecking('data-sales-count-nope', $info['salesCount_NOPE']);
	
			// Only displays the attribute, with the value 'selected', if $info['selected'] is true.
			$bookItemDiv->setAttributeChecking('selected', $info['selected'], 'selected');
			$bookItemDiv->setAttributeChecking('selected-nope', $info['selected_NOPE'], 'selected');
	
			// Will display:
			$bookItemDiv->appendNewElement('h5.authorName', Glaze\check($info['authorName']));
			// Will not display:
			$bookItemDiv->appendNewElement('h5.authorName', Glaze\check($info['authorName_NOPE']));
			
			// Will display:
			$bookItemDiv->appendNewElement('p.description', Glaze\Prepare::contentSeparatedBySoftLineBreaks( Glaze\check($info['itemDescription']) ));
			// Will not display:
			$bookItemDiv->appendNewElement('p.description', Glaze\Prepare::contentSeparatedBySoftLineBreaks( Glaze\check($info['itemDescription_NOPE']) ));
		}
		
		
		
		$body->appendNewElement('h2', 'These are already escaped &amp; &lt; &gt;', Glaze\TYPE_PREGLAZED);
		
		
		$img = $body->appendNewElement('img');
		{
			$img->setAttribute('src', 'http://placehold.it/150x50');
			
			$escapedText = 'Bangers &amp; Mash';
			$img->setAttribute('alt', $escapedText, Glaze\TYPE_PREGLAZED);
		}
		
		
		
		
		
		
		$body->appendNewElement('hr');
		$body->appendNewElement('p', array(
			'Updating this file by using: ',
			Glaze\Prepare::element('br'),
			Glaze\Prepare::element('code', 'php -f example-update.php')
		));
		$body->appendNewElement('p', array(
			'Check this file with the last version by using: ',
			Glaze\Prepare::element('br'),
			Glaze\Prepare::element('code', 'php -f example-check.php')
		));
		
		$body->appendNewElement('p', array(
			'You can download glaze.php from here: ',
			Glaze\Prepare::element(array(
				'tagName' => 'a',
				'href' => 'http://github.com/BurntCaramel/glaze'
			), 'github.com/BurntCaramel/glaze')
		));
	}
}

echo '<!doctype html>' ."\n";
$html->serve();