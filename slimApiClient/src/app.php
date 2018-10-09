<?php
namespace roman\slimApi;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;
require './vendor/autoload.php';

class App
{
	private $app;
	private const SCRIPT_INCLUDE = '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
   <script
     src="https://code.jquery.com/jquery-3.3.1.min.js"
     integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
     crossorigin="anonymous"></script>
   </head>
   <script src=".public/script.js"></script>';

   public function __construct() {
   	$app = new \Slim\App(['settings' => $config]);

   	$container = $app->getContainer();

   	$container['renderer'] = new PhpRenderer("./templates");

   	function makeApiRequest($path){
   		$ch = curl_init();

   		# Set the URL that you want to GET by using the CURLOPT_URL option.
   		curl_setopt($ch, CURLOPT_URL, "http://localhost/Slim/$path");
   		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

   		$response = curl_exec($ch);
   		return json_decode($response, true);
   	}

   	$app->get('/fruits', function(Request $request, Response $response, array $args) {
   		$responseRecords = makeApiRequest('fruits');

   		$templateVariables = [
   			"title" => "Fruits",
   			"responseRecords" => $responseRecords
   		];
   		return $this->renderer->render($response, "/fruits.html", $templateVariables);
   	});

   	$app->get('/fruits/add', function(Request $request, Response $response) {
   		$templateVariables = [
   			"type" => "new",
   			"title" => "Add Fruit"
   		];
   		return $this->renderer->render($response, "/fruitsForm.html", $templateVariables);
   	});

   	$app->get('/fruits/{id}', function(Request $request, Response $response, array $args) {
   		$id = $args['id'];
   		$responseRecords = makeApiRequest('fruits/'.$id);
   		$body = "<h1>Fruit Name: ".$responseRecords['fruit_name']."</h1>";
   		$body = $body . "<h2>Fruit Color: ".$responseRecords['fruit_color']."</h2>";
   		$body = $body . "<h2>Season: ".$responseRecords['season']."</h2>";
   		$body = $body . "<h2>Calories: ".$responseRecords['calories']."</h2>";
   		$body = $body . "<p>Description: ".$responseRecords['description']."</p>";
   		$response->getBody()->write($body);
   		return $response;
   	});

   	$app->get('/fruits/{id}/edit', function(Request $request, Response $response, array $args) {
   		$id = $args['id'];
   		$responseRecord = makeApiRequest('fruits/'.$id);
   		$templateVariables = [
   			"type" => "edit",
   			"title" => "Edit Fruit",
   			"id" => $id,
   			"person" => $responseRecord
   		];
   		return $this->renderer->render($response, "/fruitsEdit.html", $templateVariables);
   	});
   	$this->app = $app;
   }

   /**
   	- Get an instance of the application
   	-
   	- @return \Slim\App
   */
   	public function get() {
   		return $this->$app;
   	}
}