glaze
=====

Easy to use PHP functions for displaying *stuff*. Works with text, URLs, email addresses, and HTML attributes. Lets you focus more on what is being displayed, rather than thinking all the time that "before I display this string it must be escaped for the Hyper Text Markup Language..."

```php
/* The name of functions focus on what they are used for, rather than how they do it. */

string glazeText( string $string ) // Returns display text

string glazeURL( string $string ) // Returns a URL appropriate for displaying

string glazeEmailAddress( string $emailAddress ) // This one attempts to confuse dumb spam robots that try to find your email address.

string glazeEmailAddressMailtoURL( string $emailAddress ) // Those trendy mailto:me@mywebsite.com links, also dumb-spam-bot proof.

string glazeNumberWithOrdinals( int $number ) // Returns a number as 1st, 2nd, 3rd, 4th...

string glazyAttribute( string $attributeName, string $attributeValue [, string $valueType ] ) // Displays an HTML element's attribute: | name="value"|

string glazyAttributeCheck( string $attributeName, mixed &$attributeValueToCheck [, string $attributeValueToUse = null, string $valueType = null] ) // This works the same as the above function, but checks a variable reference you pass first. If $attributeValueToUse isn't passed then $attributeValueToCheck is also used as the value.

```

### Simple example

```php
?>
<a<?php glazyAttribute('href', 'http://www.facebook.com/'); ?>><?= glazeText('All my friends & family are on here.') ?></a>
<?php
```

### Classes

```php
// Long way
if (!empty($classNames)):
?> class="<?= implode(' ', $classNames); ?>"<?php
endif;

// Using glaze
glazyAttributeCheck('class', $classNames);
```
	
### More complex example

```php
$info = json_decode($filePath, true); // true: decodes as an array

$classNamesArray = array('item', 'book');
$classNamesArray[] = $info['genre']; // e.g. 'thriller'
?>
<div<?php
glazyAttribute('id', $info['itemID']);
glazyAttribute('class', $classNamesArray); // Lets you use an array of strings for class attributes.
glazyAttributeCheck('data-sales-count', $info['salesCount']); // Only display the attribute if variable reference $info['salesCount'] is set.
glazyAttributeCheck('selected', $info['selected'], 'selected'); // Only displays the attribute, with the value 'selected', if $info['selected'] is true.
?>>
<?= glazeText($info['itemDescription']) ?>
</div>
<?php
```

Using already escaped information:

```php
$escapedText = 'Bangers &amp; Mash';
glazyAttribute('alt', $escapedText, GLAZE_TYPE_PREGLAZED);
```


### If you have any improvements, please send them in!

- All these functions require a `<?=` or an echo, does this hamper readability?