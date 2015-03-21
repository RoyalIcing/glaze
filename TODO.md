Glaze TODO
=====

## Multiple elements for multiple array items
```php
	glazyMultipleElements('p', array(
		'Paragraph 1',
		'Paragraph 2',
		'Paragraph 3'
	));
	
	glazyPrepareMultipleElements('p', $descriptionLines);
```

## Register your own escaping functions
```php
	define('EXAMPLE_SPECIAL_URL_FINDER', 'EXAMPLE_SPECIAL_URL_FINDER');
	
	function exampleSpecialURLFinder($content) {
		$content = ...
		
		return $content;
	}
	
	glazyRegisterContentTransformer(EXAMPLE_SPECIAL_URL_FINDER, 'exampleSpecialURLFinder');
	glazyContent('Make this a <a> link: http://www.burntcaramel.com/');
	
	
	
	define('GARNISH_ORDINAL_GLAZE_TYPE', 'GARNISH_ORDINAL_GLAZE_TYPE');
	
	glazyRegisterContentType(GARNISH_ORDINAL_GLAZE_TYPE, 'garnishNumberWithOrdinals');
	glazyContent(22, GARNISH_ORDINAL_GLAZE_TYPE); // 22nd
```

## Preparing and changing elements
```php
	define('EXAMPLE_INFO_TYPE', 'EXAMPLE_INFO_TYPE');

	function exampleInfoTypeTransformer($info) {
		$preparedContent = ...
		
		return $preparedContent;
	}
	
	glazyRegisterInfoType(EXAMPLE_INFO_TYPE, 'exampleInfoTypeTransformer');

	$preparedInfo = glazyPrepareInfo('uniqueType', array(
		'associativeArray' => 1,
		'ofAnything' => 5.0,
		'youNeed' => true
	));
	
	glazyServe($preparedInfo);
```

## Doesn't support siblings text with elements

Displaying inside `glazyBegin()` with echoed text then an element such as a `<span>` then more text will group the text together at the end. A way around this is to echo all output as unique identifiers, such as `{glazy_RANDOM_element34_begin}`, `{glazy_RANDOM_element34_finish}` and find and replace after all tags and attributes.
	
	
## Serve to arbitrary strings
http://evertpot.com/222/