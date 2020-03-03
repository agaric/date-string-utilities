# Date String Utilities

This package currently consists of one function which helps to detect dates hidden within a string.

```php
<?php

use acurrieclark\DateStringUtilities\DateInStringFinder;

$string = 'The painters said they had completed the job on 4th March 2020';

$date = DateInStringFinder::find($string);

/**
 * [
 *      'day' => 4,
 *      'month' => 3,
 *      'year' => 2020,
 *  ]
 */
```

## Date Formats
Many Date formats are supported, a full list of which can be seen in the [test file](/tests/DateInStringFinderTest.php).

### North American Dates
It should be noted that dates in the format `2/3/2020` will be interpreted as a UK date ie. **2nd March 2020**, ***not*** 3rd February 2020 as might be expected in the USA and Canada.

## Credits

This package borrows from [Etienne Tremel](https://github.com/etiennetremel)'s [PHP-Find-Date-in-String](https://github.com/etiennetremel/PHP-Find-Date-in-String) package. 

## Contribute

Pull requests are most welcome. In particular, feel free to add to the tests with currently passing examples which you want to preserve in future versions.
