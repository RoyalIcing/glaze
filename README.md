Glaze
=====

### *[French and Viennese pastry chefs originally invented the idea of glazing cakes as a way of preserving them—the glaze sealed off the cakes from the air and prevented them from growing stale.](http://www.epicurious.com/articlesguides/howtocook/primers/cakesfrostings)*

**When displaying anything on the web it must be properly prepared for HTML.** Any text, any URL, and any HTML element’s attributes and contents must be *escaped* before they are displayed.

Normally people use functions like `htmlspecialchars()`, or they even madly paste text into their source code and manually change characters like `&` into `&amp;` and `>` into `&gt;`.

Well there’s these things called computers and you can avoid all that manual work and use whatever baking-inspired function names you like.

## Glaze preserves the content you want to display.

### Just tell it what you want to display and let it worry about the HTML writing and escaping part. It works with elements, attributes, text, URLs, and email addresses.


## Whole elements

```php
// Easy escaped elements in one line.
glazyElement('h1#siteTitle', 'Title');
glazyElement('h2.tagline', 'The home of examples & more'); // No need to escape the &
glazyElement('p.any.classes.you.need', 'Blah blah blah blah');


// Or use array version, to specify any attributes you like:

// Elements with both attributes and contents.
glazyElement(array(
	'tagName' => 'a',
	'href' => 'http://www.infinitylist.com/',
	'class' => 'externalLink'
), 'Adventure & creative videos daily.');


// Self closing elements without contents are handled.
glazyElement(array(
	'tagName' => 'meta',
	'name' => 'description',
	'content' => 'Site description as seen by search engines'
));
```


## Class attributes

```php

$classNames = '';

if (isArticle()):
	$classNames = array('article');
	
	if (isFeatureArticle()):
		$classNames[] = 'feature';
	endif;
endif;


// Long way:
// Juggling if statements and PHP open/close tags.
if (!empty($classNames)):
?> class="<?= implode(' ', $classNames); ?>"<?php
endif;


// Using glaze, accepts a string or array:
// If `$classNames` is empty then nothing will be displayed.
glazyAttributeCheck('class', $classNames);
```


## Check values for attributes before displaying

```php
// Using JSON from a web API say.
$info = json_decode($jsonString, true); // true: decodes as an array

// Build a list of classes using an array, don't fuss with appending to a string
$classNamesArray = array('item', 'book');
$classNamesArray[] = !empty($info['published']) ? 'published' : 'upcoming';
$classNamesArray[] = 'genre-' .$info['genreIdentifier']; // e.g. 'genre-thriller'

// Begin the <div>
$bookItemDiv = glazyBegin('div');

glazyAttribute('id', "bookItem-{$info['itemID']}");

// Lets you use an array of strings for class attributes.
glazyAttribute('class', $classNamesArray);

// Only display the attribute if variable reference $info['salesCount'] is present.
glazyAttributeCheck('data-sales-count', $info['salesCount']);

// Only displays the attribute, with the value 'selected', if $info['selected'] is true.
glazyAttributeCheck('selected', $info['selected'], 'selected');

glazyElement('h5.authorName', $info['authorName']);
glazyElement('p.description', $info['itemDescription']);

// Finish and close the </div>
glazyFinish($bookItemDiv);
```

### Using already escaped information

```php
$escapedText = 'Bangers &amp; Mash';
glazyAttribute('alt', $escapedText, GLAZE_TYPE_PREGLAZED);
```


## *~ Les fonctions ~*
```php

// Returns text ready for display.
string glazeText( string $string )

// Returns a URL ready for display.
string glazeURL( string $string )

// This attempts to confuse dumb spam robots that try to find your email address.
string glazeEmailAddress( string $emailAddress )

// Those trendy mailto:me@mywebsite.com links, also dumb-spam-bot proof.
string glazeEmailAddressMailtoURL( string $emailAddress )

// Returns a number as 1st, 2nd, 3rd, 4th...
string glazeNumberWithOrdinals( int $number )


/* The following g-lazy functions display for you, instead of returning a string. */

// Displays an HTML element's attribute: | name="value"|
void glazyAttribute( string $attributeName, string $attributeValue [, string $valueType ] )

// This works the same as the above function, but checks a variable reference you pass first.
// If $attributeValueToUse isn't passed then $attributeValueToCheck is also the value that is displayed.
void glazyAttributeCheck( string $attributeName, mixed &$attributeValueToCheck [, string $attributeValueToUse = null, string $valueType = null] )

// Display a simple <tag id="elementID" class="classes you need">CONTENTS</tag>, with a choice for the tag name, and its contents value and type.
void glazyElement( string/array $tagNameOrElementOptions, string $contentsValue [, string $valueType ] )

// For truly lazy debugging, use this to spit out a <pre> tag containing the contents of an object.
void glazyPrintR( $object )



// Open an element, and use glazyAttribute() for attributes, and then simply display your element's contents.
resource glazyBegin( string $tagName [, string $valueType = GLAZE_TYPE_PREGLAZED ] )

// Close element, optionally passing the return value from glazyBegin()
// otherwise closes the most recent open element.
void glazyFinish( [ resource $openGlazyElement ] )


/*
	Never think again "before this string is outputted to the user,
	it must be first escaped for the Hyper Text Markup Language,
	therefore I must use this 'special character' function and
	not forget its name."
*/

```
