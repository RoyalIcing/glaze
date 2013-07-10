glaze
=====

Simple PHP functions for escaping text, URLs, email addresses, and HTML attributes. Aims to be very simple and easy to read.

	// Simple text
	echo glazeText('Text to display');
	
	// Escape an email address, for making it harder for spam bots to pick up.
	$emailAddressDisplay = glazeEncodedText('email@example.com');

For escaping HTML attributes:

	?>
	<a<?= glazeAttribute('href', 'http://www.facebook.com/', 'URL') ?>><?= glazeText('Friends & family contact me here.') ?></a>
	<?php
	
Using JSON information:

	$info = json_decode($filePath, true);
	?>
	<div<?php
	echo glazeAttribute('id', $info['itemID']);
	echo glazeAttributeCheck('data-sales-count', $info['salesCount']); // Only display the attribute if value $info['salesCount'] is not empty.
	echo glazeAttributeCheck('class', $info['isBestSeller'], 'bestSeller'); // Only displays the attribute, using the value 'bestSeller', if $info['isBestSeller'] is not empty.
	?>>
	<?= glazeText($info['itemDescription']) ?>
	</div>
	<?php

Using already escaped information:
	
	$escapedText = 'Bangers &amp; Mash';
	echo glazeAttribute('alt', $escapedText, 'glazed');

