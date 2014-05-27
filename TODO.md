Glaze TODO
=====

## Nested elements

```php
	// YES
	
	$h3 = glazyBegin('h3');
	glazyElement(array(
		'tagName' => 'a'
		'href' => $newPostEditURL
	), 'Edit post');
	glazyFinish($h3);
	
	
	glazyLayer(
		glazyPrepare('h3'),
		glazyPrepare(array(
			'tagName' => 'a'
			'href' => $newPostEditURL
		)),
		'Edit post'
	);
	
	// NO?
	
	glazyLayer(
		glazyPrepare('h3'),
		glazyPrepare(array(
			'tagName' => 'a'
			'href' => $newPostEditURL
		), 'Edit post')
	);
	
	glazyLayer(
		glazyPrepare('h3'),
		glazyPrepareLink(array(
			'href' => $newPostEditURL
		), 'Edit post')
	);
	
	// WRONG as you don't know which one is a HTML tag
	// and which is is plain text.
	glazyLayer(
		'h3',
		array(
			'tagName' => 'a'
			'href' => $newPostEditURL
		),
		'Edit post'
	);
	
	
	glazyLayering(
		glazyPrepare('h3'),
		glazyFilling(
			glazyPrepare(array(
				'tagName' => 'a'
				'href' => $newPostEditURL
			), 'Edit post'),
			glazyPrepare(array(
				'tagName' => 'a'
				'href' => $newPostViewURL
			), 'View post'),
		)
	);
	
	glazyFilling(
		glazyPrepare('h3'),
		array(
			glazyPrepare(array(
				'tagName' => 'a'
				'href' => $newPostEditURL
			), 'Edit post'),
			glazyPrepare(array(
				'tagName' => 'a'
				'href' => $newPostViewURL
			), 'View post'),
		)
	);
	
	// YES?
	
	glazyLayer(
		glazyBegin('h3'),
		glazyBeginLink($newPostEditURL),
		'Edit post'
	);
	
	// NO?
	
	glazyLayer(
		array('h3'),
		array('tagName' => 'a', 'href' => $newPostEditURL),
		'Edit post'
	);
	
	glazyLayer(
		array('h3'),
		array('a', 'href' => $newPostEditURL),
		'Edit post'
	);
```

## Joining lines and line breaks

```php	
	glazyElement('h3', array(
			'Submitted by: ' .$processedEntries['name']['value'],
			'Their email: ' .$processedEntries['email']['value'],
		), GLAZE_VALUE_TYPE_ARRAY_OF_LINES);
	
		glazyLayer(
			glazyPrepare('h3'),
			// Content functions are always the last ones.
			garnishWithLineBreaks(array(
				'Submitted by: ' .$processedEntries['name']['value'],
				'Their email: ' .$processedEntries['email']['value'],
			))
		);
	
	
		glazyLayer(
			glazyPrepare('h3'),
			garnishJoinWithLineBreaks(array(
				garnishSingleLine(
					'Submitted by: ',
					glazyPrepare('strong', $processedEntries['name']['value'])
				),
				garnishSingleLine(
					'Their email: ',
					glazyPrepare('strong', $processedEntries['email']['value'])
				),
			))
		);
```

## Multiple elements for multiple array items
```php
	glazyMultiple('p', $descriptionLines);
	
	glazyLayer(
		glazyPrepare('div.description'),
		glazyPrepareMultiple('p', $descriptionLines)
	);
```

## Register your own escaping functions
```php
	glazeRegister('mySpecialURLFinder');
```