<?php
/*
Copyright 2013, 2014: Patrick Smith

This content is released under the MIT License: http://opensource.org/licenses/MIT
*/

// php -f README-examples.php


require_once(dirname(__FILE__). '/../glaze-oo.php');

use BurntCaramel\Glaze;
use BurntCaramel\GlazePrepare;
use BurntCaramel\GlazeServe;


echo "\n";

GlazeServe::element('h1#siteTitle', 'Title');
// No need to escape the &
GlazeServe::element('h2.tagline', 'The home of examples & more');
GlazeServe::element('p.any.classes.you.need', 'Blah blah blah blah');

echo "\n\n\n";


GlazeServe::element(array(
	'tagName' => 'a',
	'href' => 'http://www.infinitylist.com/',
	'class' => 'externalLink'
), 'Adventure & creative videos daily.');

echo "\n\n\n";

GlazeServe::element(array(
	'tagName' => 'meta',
	'name' => 'description',
	'content' => 'Site description as seen by search engines'
));

echo "\n\n\n";


$classNames = array('post');

if (true):
	$classNames[] = 'article';
	
	if (true):
		$classNames[] = 'feature';
	endif;
endif;

?>
<div<?php
if (!empty($classNames)):
?> class="<?= implode(' ', $classNames); ?>"<?php
endif;
?>>
<?php

echo "\n\n";

// The -Checking method makes sure that if `$classNames` is empty, then nothing will be displayed.
?>
<div<?php GlazeServe::attributeChecking('class', $classNames); ?>>
<?php

echo "\n\n\n";


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
$bookItemDiv = GlazePrepare::element('div');
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
	$bookItemDiv->appendElement('h5.authorName',
		GlazePrepare::checkContent($info['authorName'])
	);
	// Will not display, as key 'authorName_NOPE' does not exist.
	$bookItemDiv->appendElement('h5.authorName',
		GlazePrepare::checkContent($info['authorName_NOPE'])
	);
	
	// Will display:
	$bookItemDiv->appendElement('p.description',
		GlazePrepare::contentSeparatedBySoftLineBreaks(
			GlazePrepare::checkContent($info['itemDescription'])
		)
	);
	// Will not display, as key 'itemDescription_NOPE' does not exist.
	$bookItemDiv->appendElement('p.description',
		GlazePrepare::contentSeparatedBySoftLineBreaks(
			GlazePrepare::checkContent($info['itemDescription_NOPE'])
		)
	);
}
$bookItemDiv->serve();


echo "\n\n\n";

$escapedText = 'Bangers &amp; Mash';
GlazeServe::attribute('alt', $escapedText, Glaze::TYPE_PREGLAZED);

echo "\n\n";