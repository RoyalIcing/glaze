<?php
/*
Copyright 2013, 2014: Patrick Smith

This content is released under the MIT License: http://opensource.org/licenses/MIT
*/

require_once(dirname(__FILE__). '/../glaze-oo.php');

use BurntCaramel\Glaze;
use BurntCaramel\GlazePrepare;

$html = GlazePrepare::element('html');
{
	$html->appendElement('head', array(
		GlazePrepare::element('title', 'An example of using glaze.php'),
		GlazePrepare::element(array('tagName' => 'meta', 'charset' => 'utf-8'))
	));
	
	$body = $html->appendElement('body');
	{
		$body->appendElement('header', array(
			GlazePrepare::element('h1', 'Glaze preserves your content by escaping any <html> characters.')
		));
		
		$body->appendElement('p', 'Your content should be stored as it was written. Escaping first makes it difficult when content is used outside of the web, such as in an app.');
		
		$body->appendElement('p', array(
			'You can easily format what you write, adding ',
			GlazePrepare::element('strong', '<strong>'),
			' or ',
			GlazePrepare::element('em', '<em>'),
			' just by interspersing text with `GlazePrepare::element(tagName, content)`.'
		));
		
		
		
		$body->appendElement('h2', 'Check values before displaying.');
		
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
		$bookItemDiv = $body->appendElement('div');
		{
			$bookItemDiv->setAttribute('id', "bookItem-{$info['itemID']}");
	
			// Lets you use an array of strings for class attributes.
			$bookItemDiv->setAttribute('class', $classNamesArray);

			// Only display the attribute if variable reference $info['salesCount'] is present.
			$bookItemDiv->setAttributeChecking('data-sales-count', $info['salesCount']);
			$bookItemDiv->setAttributeChecking('data-sales-count-nope', $info['salesCount_NOPE']);
	
			// Only displays the attribute, with the value 'selected', if $info['selected'] is true.
			$bookItemDiv->setAttributeChecking('selected', $info['selected'], 'selected');
			$bookItemDiv->setAttributeChecking('selected-nope', $info['selected_NOPE'], 'selected');
	
			// Will display:
			$bookItemDiv->appendElement('h5.authorName', GlazePrepare::checkContent($info['authorName']));
			// Will not display:
			$bookItemDiv->appendElement('h5.authorName', GlazePrepare::checkContent($info['authorName_NOPE']));
			// Will display:
			$bookItemDiv->appendElement('p.description', GlazePrepare::contentSeparatedBySoftLineBreaks( GlazePrepare::checkContent($info['itemDescription']) ));
			// Will not display:
			$bookItemDiv->appendElement('p.description', GlazePrepare::contentSeparatedBySoftLineBreaks( GlazePrepare::checkContent($info['itemDescription_NOPE']) ));
		}
		
		
		
		$body->appendElement('h2', 'These are already escaped &amp; &lt; &gt;', Glaze::TYPE_PREGLAZED);
		
		
		$img = $body->appendElement('img');
		{
			$img->setAttribute('src', 'http://placehold.it/150x50');
			
			$escapedText = 'Bangers &amp; Mash';
			$img->setAttribute('alt', $escapedText, Glaze::TYPE_PREGLAZED);
		}
		
		
		
		
		
		
		$body->appendElement('hr');
		$body->appendElement('p', array(
			'Updating this file by using: ',
			GlazePrepare::element('br'),
			GlazePrepare::element('code', 'php -f example-update.php')
		));
		$body->appendElement('p', array(
			'Check this file with the last version by using: ',
			GlazePrepare::element('br'),
			GlazePrepare::element('code', 'php -f example-check.php')
		));
		
		$body->appendElement('p', array(
			'You can download glaze.php from here: ',
			GlazePrepare::element(array(
				'tagName' => 'a',
				'href' => 'http://github.com/BurntCaramel/glaze'
			), 'github.com/BurntCaramel/glaze')
		));
	}
}

echo '<!doctype html>' ."\n";
$html->serve();