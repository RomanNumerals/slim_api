<?php
namespace roman\slimApi;
use \Psr\Http\Message\ServerRequestInteface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require './vendor/autoload.php';

class Components
{

  private $app;
  public function __construct($db) {
    $config['db']['host'] = 'localhost';
    $config['db']['user'] = 'root';
    $config['db']['pass'] = 'root';
    $config['db']['dbname'] = 'apidb';

    $app = new \Slim\App(['settings' => $config]);

    $container = $app->getContainer();
    $container['db'] = $db;

    $this->app = $app;
  }

  public function get() {
    return $this->app;
  }
}
