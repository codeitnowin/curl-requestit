# CodeItNow Curl RequestIt
Light weight curl request library by http://www.codeitnow.in, It has setters and getters to make easy to use. You can use Curl Request with any framework like Symfony, Laravel, CackPHP, CodeIgniter, Yii etc. This is alternate of Guzzle and other libraries.

## Installation - 
CodeItNow Curl RequestIt can install by composer.

```
composer require codeitnowin/curlrequestit
``` 

## Uses -
You can use Curl RequestIt by adding composer autoload file from vendor folder and then use namespace as given in below example

### Example - Request:
```php
require_once 'vendor/autoload.php';

use CodeItNow\Curl\RequestIt;

$request = new RequestIt();

$request->setUrl('http://localhost')
        ->setParams(array("name"=>"CodeItNow", "library"=>"CurlRequestIt"))
        ->send('POST');
print_r($request->getResponse());
```
