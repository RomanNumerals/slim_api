<?php
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

		$app->get('/fruit', function(Request $request, Response $response){
			$fruit = $this->db->query('SELECT * FROM fruit')->fetchAll();
			$jsonResponse = $response->withJson($fruit);
			return $jsonResponse;
		});

		$app->get('/fruit/{id}', function(Request $request, Response $response, array $args) {
			$id = $args['id'];
			$fruit = $this->db->query('SELECT * FROM fruit WHERE id='.$id)->fetch();
			$jsonResponse = $response->withJson($fruit);
			return $jsonResponse;
		});

		$app->put('/fruit/{id}', function(Request $request, Response $response, array $args) {
			$id = $args['id'];
			$this->logger->addInfo("PUT /fruit/" .$id);

			# builds query string
			$updateString = "UPDATE fruit SET ";
			$fields = $request->getParsedBody();
			$keysArray = array_keys($fields);
			$last_key = end($keysArray);
			foreach($fields as $fields => $value) {
				$updateString = $updateString . "$field = '$value'";
				# adding commma to avoid sql syntax error
				if ($field != $last_key) { 
					$updateString = $updateString . ", ";
				}
			}
			$updateString = $updateString . " WHERE id = $id;";

			# execute query
			$this->db->exec($updateString);

			# returns updated record
			$fruit = $this->db->query('SELECT * FROM fruit WHERE id='.$id)->fetch();
			$jsonResponse = $response->withJson($fruit);
			return $jsonResponse;
		});

		$app->delete('/fruit{id}', function(Request $request, Response $response, array $args) {
			$id = $args['id'];
			$deleteMsg = $this->db->exec('DELETE FROM fruit WHERE id='.$id);
			$jsonResponse = $response->withJson($fruit);
			return
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
