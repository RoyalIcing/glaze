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
glazyElement('h1#siteTitle', 'Title');
glazyElement('h2.tagline', 'The home of examples & more'); // No need to escape the &
glazyElement('p.any.classes.you.need', 'Blah blah blah blah');
```

Or use associated array version, to specify any attributes you like:

```php
glazyElement(array(
	'tagName' => 'a',
	'href' => 'http://www.infinitylist.com/',
	'class' => 'externalLink'
), 'Adventure & creative videos daily.');
```

Self closing elements are also handled.

```php
glazyElement(array(
	'tagName' => 'meta',
	'name' => 'description',
	'content' => 'Site description as seen by search engines'
));
```


## Class attributes

Say you want to display an element’s class names, some optional.

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
if (!empty($classNames)):
?> class="<?= implode(' ', $classNames); ?>"<?php
endif;
```

Or use the `glazyAttribute`/`glazyAttributeCheck` function, passing a string or array:

```php
// The -Check function makes sure that if `$classNames` is empty, then nothing will be displayed.
glazyAttributeCheck('class', $classNames);
```


## Check values for attributes before displaying

Using JSON from a web API, for example.

```php
$info = json_decode($jsonString, true); // true: decodes as an array

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
	
	// Only displays the attribute, with the value 'selected', if $info['selected'] is true.
	glazyAttributeCheck('selected', $info['selected'], 'selected');
	
	glazyElement('h5.authorName', $info['authorName']);
	glazyElement('p.description', $info['itemDescription']);
	
	// Finish and close the </div>
}
glazyFinish($bookItemDiv);
```

### Using already escaped information

```php
$escapedText = 'Bangers &amp; Mash';
glazyAttribute('alt', $escapedText, GLAZE_TYPE_PREGLAZED);
```


## *~ Les fonctions ~*

The `glazyBegin`, `glazyElement`, and `glazyPrepare` functions accept either a selector type string (single element) or an array of attributes, with the particular tag specified with the key 'tagName'.

Single-item selector:

```php
$tagNameOrElementOptions = 'article#mainArticle.interview.hasPhotos';
```

Associated array of tag name and attributes:
 
```php
$tagNameOrElementOptions = array(
	'tagName' => 'a',
	'href' => 'http://www.burntcaramel.com/',
	'class' => array('some', 'class', 'names')
);
```

Display HTML elements and attributes using nested function calls:

```php
// Open an element, displaying additional attributes using glazyAttribute() for attributes, and then simply display your element's contents.
resource glazyBegin( string/array $tagNameOrElementOptions [, string $valueType = GLAZE_TYPE_PREGLAZED ] )

// Displays an HTML element's attribute: | name="value"|
void glazyAttribute( string $attributeName, string $attributeValue [, string $valueType = null (automatic detection) ] )

// This works the same as the above function, but checks a variable reference you pass first.
// If $attributeValueToUse isn't passed then $attributeValueToCheck is also the value that is displayed.
void glazyAttributeCheck( string $attributeName, mixed &$attributeValueToCheck [, string $attributeValueToUse = null, string $valueType = null] )

// Display inner textual content.
void glazyContent ( string $contentValue [, string $contentType = GLAZE_TYPE_TEXT ] )

// Close element, optionally passing the return value from glazyBegin()
// otherwise closes the most recent open element.
void glazyFinish( [ resource $begunGlazyElement ] )
```

Display simple HTML element with inner text:

```php
// Display a simple HTML element `<tag id="elementID" class="classes you need">CONTENTS</tag>`, with a choice for the tag name, and its contents value and type.
void glazyElement( string/array $tagNameOrElementOptions, string $contentValue [, string $valueType ] )

// For truly lazy debugging, use this to spit out a <pre> tag containing the contents of an object.
void glazyPrintR( $object )
```

Display HTML element and content using nested objects, which are prepared first and then served to display them:

```php
// Prepare an HTML element but don’t display it yet. $contentValue can be a string, a prepared content, or another prepared element.
resource glazyPrepareElement( string/array $tagNameOrElementOptions [, string/array $contentValue = null, string $valueType = GLAZE_TYPE_TEXT ] )

// Display prepared element created using the glazyPrepareElement function.
void glazyServeElement( [ resource $preparedElement ] )


// Prepare content but don’t display it yet.
// If an array is passed for $contentValue, it is joined using $spacing. It can contain strings and other prepared content and elements.
resource glazyPrepareContentWithSpacing( string/array $contentValue [, string $contentType = GLAZE_TYPE_TEXT, $spacing = '' ] )

// Prepare content joining items of $contentValue together with no spaces in between.
resource glazyPrepareContentJoined( string/array $contentValue [, string $contentType = GLAZE_TYPE_TEXT ] )

// Prepare content insert `<br>` line breaks between each item of $contentValue.
resource glazyPrepareContentWithLineBreaks( string/array $contentValue [, string $contentType = GLAZE_TYPE_TEXT ] )

// Prepare content with HTML, containing potentially unsafe <script> tags. Nothing is altered.
resource glazyPrepareContentWithUnsafeHTML( string $contentValue )

// Display prepared content created using one of the glazyPrepareContent... functions.
void glazyServeContent( [ resource $preparedContent ] )


// Display prepared element or content created using any of the glazyPrepare... functions, or a string.
function glazyServe( $preparedInfoOrString [, $contentType = GLAZE_TYPE_TEXT ] )
```

Base preserving (escaping) functions:

```php
// Returns text ready for display
// Type is GLAZE_TYPE_TEXT
string glazeText( string $string )

// Returns a URL ready for display
// Type is GLAZE_TYPE_URL
string glazeURL( string $string )

// This attempts to confuse dumb spam robots that try to find your email address
// Type is GLAZE_TYPE_EMAIL_ADDRESS
string glazeEmailAddress( string $emailAddress )

// Those trendy mailto:me@mywebsite.com links, also dumb-spam-bot proof.
// Type is GLAZE_TYPE_EMAIL_ADDRESS_MAILTO_URL
string glazeEmailAddressMailtoURL( string $emailAddress )

// The type GLAZE_TYPE_SPACED_LIST_ATTRIBUTE can be used for the class attribute, joining an array of class names into a single string.

// The type GLAZE_TYPE_PREGLAZED can be used for text that is already escaped and ready for HTML.
```