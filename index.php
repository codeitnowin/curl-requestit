<?php
require_once 'vendor/autoload.php';

use CodeItNow\Curl\RequestIt;

$request = new RequestIt();

$request->setUrl('http://localhost')
        ->setParams(array("name"=>"CodeItNow", "library"=>"CurlRequestIt"))
        ->send('POST');
print_r($request->getResponse());

