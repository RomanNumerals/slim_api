<?php
require './vendor/autoload.php';
$app = (new roman\slimApiClient\App())->get();
$app->run();