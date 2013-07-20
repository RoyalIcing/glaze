glaze
=====

Easy to use PHP functions for displaying *stuff*. Escapes text & URLs, email addresses, and HTML attributes.

```php
// Simple text
echo glazeText('Text to display');

// Encode an email address to make it harder for spam bots to pick up.
$emailAddressDisplay = glazeNumericEntityEncodedText('email@example.com');
```

For escaping HTML attributes:

```php
?>
<a<?= glazeAttribute('href', 'http://www.facebook.com/') ?>><?= glazeText('Friends & family contact me here.') ?></a>
<?php
```
	
More complex example:

```php
$info = json_decode($filePath, true); // true: decodes as an array

$classNamesArray = array('item', 'book');
$classNamesArray[] = $info['genre']; // e.g. 'thriller'
?>
<div<?php
echo glazeAttribute('id', $info['itemID']);
echo glazeAttribute('class', $classNamesArray); // Works with an array of values for class attributes.
echo glazeAttributeCheck('data-sales-count', $info['salesCount']); // Only display the attribute if value $info['salesCount'] has something there.
echo glazeAttributeCheck('selected', $info['selected'], 'selected'); // Only displays the attribute, with the value 'selected', if $info['selected'] is true.
?>>
<?= glazeText($info['itemDescription']) ?>
</div>
<?php
```

Using already escaped information:

```php
$escapedText = 'Bangers &amp; Mash';
echo glazeAttribute('alt', $escapedText, GLAZE_TYPE_PREGLAZED);
```
