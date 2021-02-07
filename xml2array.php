<?php

$file = file_get_contents('t.xml');

$array = json_decode(json_encode(simplexml_load_string($file)),true);

echo '<pre>';
print_r($array);
echo '</pre>';