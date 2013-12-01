· Glaze ·
=====

### *[French and Viennese pastry chefs originally invented the idea of glazing cakes as a way of preserving them—the glaze sealed off the cakes from the air and prevented them from growing stale.](http://www.epicurious.com/articlesguides/howtocook/primers/cakesfrostings)*

When displaying anything on the web it must be properly dealt with for the HTML format. Any bits of text, any URL, and any HTML attribute must be properly *escaped* before displaying them.

Normally people use functions like `htmlspecialchars()`, or they madly paste text into their source code and manually change characters like `&` into `&amp;` and `>` into `&gt;`.

Well there’s these things called computers and you can avoid all that manual work or use whatever baking-inspired function names you like.

## Glaze preserves the text you want to display.

Just tell it what you want to display and let it worry about the HTML-escaping part. It works with text, URLs, email addresses, and HTML attributes.


## A simple example
```php
/* HTML needs the ampersand encoded as &amp; */
?>
<a<?php glazyAttribute('href', 'http://www.facebook.com/'); ?>>
<?= glazeText('All my friends & family are on here.') ?>
</a>
<?php
```

## Class attributes

```php
// $classNames is an [array] of class names.

// Long way
if (!empty($classNames)):
?> class="<?= implode(' ', $classNames); ?>"<?php
endif;

// Using glaze
glazyAttributeCheck('class', $classNames);
```
	
## More complex example

```php
// Using JSON from a web API or from a file.
$info = json_decode($filePath, true); // true: decodes as an array

// Build a list of classes using an array, not worrying about appending to a string
$classNamesArray = array('item', 'book');

$classNamesArray[] = !empty($info['published']) ? 'published' : 'upcoming';

$classNamesArray[] = $info['genreIdentifier']; // e.g. 'thriller'

?>
<div<?php
glazyAttribute('id', "bookItem-{$info['itemID']}");
// Lets you use an array of strings for class attributes.
glazyAttribute('class', $classNamesArray);
// Only display the attribute if variable reference $info['salesCount'] is present.
glazyAttributeCheck('data-sales-count', $info['salesCount']);
// Only displays the attribute, with the value 'selected', if $info['selected'] is true.
glazyAttributeCheck('selected', $info['selected'], 'selected');
?>>
<?= glazeText($info['itemDescription']) ?>
</div>
<?php
```

### Using already escaped information

```php
$escapedText = 'Bangers &amp; Mash';
glazyAttribute('alt', $escapedText, GLAZE_TYPE_PREGLAZED);
```


## *Les fonctions*
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

// Display a simple <tag>CONTENTS</tag>, with a choice for the tag name, and its contents value and type.
void glazyElement( string $tagName, string $contentsValue [, string $valueType ] )

// For truly lazy debugging, use this to spit out a <pre> tag containing the contents of an object.
void glazyPrintR( $object )


/*
	Never think again "before I output this string to the user,
	it must be first escaped for the Hyper Text Markup Language,
	therefore I must use this function which does this for me
	and remember its name every time."
*/

```

#### · Glaze ·
