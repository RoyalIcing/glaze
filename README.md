Glaze
=====

### *[French and Viennese pastry chefs originally invented the idea of glazing cakes as a way of preserving them—the glaze sealed off the cakes from the air and prevented them from growing stale.](http://www.epicurious.com/articlesguides/howtocook/primers/cakesfrostings)*

**When displaying anything on the web it must be properly prepared for HTML.** Any text, any URL, and any HTML element’s attributes and contents must be *escaped* before they are displayed.

Normally people use functions like `htmlspecialchars()`, or they even madly paste text into their source code and manually change characters like `&` into `&amp;` and `>` into `&gt;`.

Well there’s these things called computers and you can avoid all that manual work and use much more powerful functions with baking-inspired names.

## Glaze preserves the content you want to display.

Just tell it what you want to display and let it worry about the HTML writing and escaping part. It works with nested HTML elements, attributes, text, URLs, and email addresses. My aim is to make it easier to read and write than the usual PHP ways, whilst also taking care of escaping everything by default.


## Whole elements

Escaped elements in one line.

```php
use BurntCaramel\Glaze;
use BurntCaramel\Glaze\Prepare as GlazePrepare;
use BurntCaramel\Glaze\Serve as GlazeServe;

GlazeServe::element('h1#siteTitle', 'Title');
// No need to escape the &
GlazeServe::element('h2.tagline', 'The home of examples & more');
GlazeServe::element('p.any.classes.you.need', 'Blah blah blah blah');

/* Displays: */?>
<h1 id="siteTitle">
Title
</h1>
<h2 class="tagline">
The home of examples &amp; more
</h2>
<p class="any classes you need">
Blah blah blah blah
</p>
```

Or use associated array version, to specify any attributes you like:

```php
GlazeServe::element(array(
	'tagName' => 'a',
	'href' => 'http://www.infinitylist.com/',
	'class' => 'externalLink'
), 'Adventure & creative videos.');

/* Displays: */?>
<a href="http://www.infinitylist.com/" class="externalLink">Adventure &amp; creative videos.</a>
```

Self closing elements are also handled.

```php
GlazeServe::element(array(
	'tagName' => 'meta',
	'name' => 'description',
	'content' => 'Site description as seen by search engines'
));

/* Displays: */?>
<meta name="description" content="Site description as seen by search engines">
```


## Class attributes

Say you want to display an HTML element’s class names where some are optional.

```php
$classNames = array('post');

if (isArticle()):
	$classNames[] = 'article';
	
	if (isFeatureArticle()):
		$classNames[] = 'feature';
	endif;
endif;
```

You could juggle `if` statements and PHP open/close tags:

```php
?>
<div<?php
if (!empty($classNames)):
?> class="<?= implode(' ', $classNames); ?>"<?php
endif;
?>>

<?php/* Displays: */?>
<div class="post article feature">
```

Or use Glaze, passing a string or array:

```php
// The -Checking method makes sure that if `$classNames` is empty, then nothing will be displayed.
?>
<div<?php GlazeServe::attributeChecking('class', $classNames); ?>>

<?php/* Displays: */?>
<div class="post article feature">
```


## Check values for attributes before displaying

Using JSON from a web API, for example.

```php
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

$bookItemDiv = GlazePrepare::element('div');
{
	$bookItemDiv->setAttribute('id', "bookItem-{$info['itemID']}");
	
	// Lets you use an array of strings for class attributes.
	$bookItemDiv->addClassNames($classNamesArray);
	
	// Only display the attribute if variable reference $info['salesCount'] is present.
	$bookItemDiv->setAttributeChecking('data-sales-count', $info['salesCount']);
	$bookItemDiv->setAttributeChecking('data-sales-count-nope', $info['salesCount_NOPE']);
	
	// Only displays the attribute, with the value 'selected', if $info['selected'] is true.
	$bookItemDiv->setAttributeChecking('selected', $info['selected'], 'selected');
	$bookItemDiv->setAttributeChecking('selected-nope', $info['selected_NOPE'], 'selected');
	
	// Will display:
	$bookItemDiv->appendNewElement('h5.authorName',
		Glaze\check($info['authorName'])
	);
	// Will not display, as key 'authorName_NOPE' does not exist.
	$bookItemDiv->appendNewElement('h5.authorName',
		Glaze\check($info['authorName_NOPE'])
	);
	
	// Will display:
	$bookItemDiv->appendNewElement('p.description',
		GlazePrepare::contentSeparatedBySoftLineBreaks(
			Glaze\check($info['itemDescription'])
		)
	);
	// Will not display, as key 'itemDescription_NOPE' does not exist.
	$bookItemDiv->appendNewElement('p.description',
		GlazePrepare::contentSeparatedBySoftLineBreaks(
			Glaze\check($info['itemDescription_NOPE'])
		)
	);
}
$bookItemDiv->serve();
```

Displays:
```html
<div id="bookItem-fddf3tq3tt3t3" class="item book upcoming genre-thriller" data-sales-count="56" selected="selected">
<h5 class="authorName">
John Smith
</h5>
<p class="description">
Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.<br>
Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.<br>
Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
</p>
</div>
```

### Using already escaped information

```php
$escapedText = 'Bangers &amp; Mash';
GlazeServe::attribute('alt', $escapedText, Glaze\TYPE_PREGLAZED);

/* Displays: */?>
 alt="Bangers &amp; Mash"
```
