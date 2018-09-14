<?php
namespace roman\slimApi;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require './vendor/autoload.php';

class App {

	private $app;
	public function __construct($db) {

		$config['db']['host'] = 'localhost';
		$config['db']['user'] = 'root';
		$config['db']['pass'] = 'test';
		$config['db']['dbname'] = 'apiDB';

		$app = new \Slim\App(['settings' => $config]);

		$container = $app->getContainer();
		$container['db'] = $db;
		# logs for app
		$container['logger'] = function($c) {
			$logger = new \Monolog\Logger('my_logger');
			$file_handler = new
			\Monolog\Handler\StreamHandler('./logs/app.log');
			$logger->pushHandler($file_handler);
			return $logger;
		};

		$app->get('/fruit', function(Request $request, Response $response) {
			$this->logger->addInfo("GET /fruit");
			$fruit = $this->db->query('SELECT * FROM fruit')->fetchAll();
			$jsonResponse = $response->withJson($fruit);
			return $jsonResponse;
		});

		$app->get('/fruit/{id}', function(Request $request, Response $response, array $args) {
			$id = $args['id'];
			$this->logger->addInfo("GET /fruit/".$id);
			$fruit = $this->db->query('SELECT * FROM fruit WHERE id='.$id)->fetch();

			if($fruit) {
				$response = $response ->withJson($fruit);
			} else {
				$errorData = array('status' => 404, 'message' => 'not found');
				$response = $response->withJson($errorData, 404);
			}
			return $response;
		});

		$app->post('/fruit', function(Request $request, Response $response) {
			$this->logger->addInfo("POST /people/");

			/*
			- Check that fruit exists
			- $person = $this->db->query('SELECT * FROM fruit WHERE id='.$id)->fetch();
			- if(!$fruit) {
			$errorData = array('status' => 404, 'message' => 'not found');
			- $response = $response->withJson($errorData, 404);
			- return $response;
		}
			*/

			# build query string
			$createString = "INSERT INTO fruit ";
			$fields = $request->getParsedBody();
			$keysArray = array_keys($fields);
			$last_key = end($keysArray);
			$values = '(';
			$fieldNames = '(';
			foreach($fields as $field => $value) {
				$values = $values . "'" . "$value" . "'";
				$fieldNames = $fieldNames . "$field";
				if ($field != $last_key) {
					# conditionally add a comma to avoid sql syntax problems
					$values = $values . ", ";
					$fieldNames = $fieldNames . ", ";
				}
			}
			$values = $values . ')';
			$fieldNames = $fieldNames . ') VALUES ';
			$createString = $createString . $fieldNames . $values . ";";
			# execute query
			try {
				$this->db->exec($createString);
			} catch (\PDOException $e) {
				var_dump($e);
				$errorData = array('status' => 400, 'message' => 'Invalid data provided to create fruit');
				return $response->withJson($errorData, 400);
			}
			# return updated record
			$fruit = $this->db->query('SELECT * FROM fruit ORDER BY id DESC LIMIT 1')->fetch();
			$jsonResponse = $response->withJson($fruit);

			return $jsonResponse;
		});

		$app->put('/fruit/{id}', function (Request $request, Response $response, array $args) {
				$id = $args['id'];
				$this->logger->addInfo("PUT /fruit/".$id);

				# check that fruit exists
				$fruit = $this->db->query('SELECT * FROM fruit WHERE id='.$id)->fetch();
				if(!$fruit){
					$errorData = array('status' => 404, 'message' => 'not found');
					$response = $response->withJson($errorData, 404);
					return $response;
				}

				# build query string
				$updateString = "UPDATE fruit SET ";
				$fields = $request->getParsedBody();
				$keysArray = array_keys($fields);
				$last_key = end($keysArray);
				foreach($fields as $field => $value) {
					$updateString = $updateString . "$field = '$value'";
					if ($field != $last_key) {
						# conditionally add a comma to avoid sql syntax problems
						$updateString = $updateString . ", ";
					}
				}
				$updateString = $updateString . " WHERE id = $id;";

				# execute query
				try {
					$this->db->exec($updateString);
				} catch (\PDOException $e) {
					$errorData = array('status' => 400, 'message' => 'Invalid data provided to update');
					return $response->withJson($errorData, 400);
				}
				# return updated record
				$person = $this->db->query('SELECT * FROM fruit WHERE id='.$id)->fetch();
				$jsonResponse = $response->withJson($fruit);

				return $jsonResponse;
		});

		$app->delete('/fruit{id}', function(Request $request, Response $response, array $args) {
			$id = $args['id'];
			$this->logger->addInfo("DELETE /fruit/".$id);
			$deleteMsg = $this->db->exec('DELETE FROM fruit WHERE id='.$id);
			if($deleteMsg) {
				$response = $response->withStatus(200);
			} else {
				$errorData = array('status' => 404, 'message' => 'not found');
				$response = $response->withJson($errorData, 404);
			}
			return $response;
		});

		$this->app = $app;
	}

	/**
	 * Get an instance of the app
	 * @return \Slim\App
	 */
	 public function get() {
	 	return $this->app;
	 }
}
