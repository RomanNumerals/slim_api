<?php
use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Uri;
use Slim\Http\RequestBody;
require './vendor/autoload.php';

# empty class definitions for phpunit to mock
class mockQuery {
	public function fetchAll(){}
	public function fetch(){}
};

class mockDB {
	public function query(){}
	public function exec(){}
};

class FruitTest extends TestCase {

	protected $app;
	protected $db;

	# execute setup code before each test is run
	public function setUp() {
		$this->db = $this->createMock('mockDb');
		$this->app = (new roman\slimApi\App($this->db))->get();
	}

	# tests the helloName endpoint
	public function testHelloName() {
		$env = Environment::mock([
			'REQUEST_METHOD' => 'GET',
			'REQUEST_URI'    => '/hello/roman',
		]);
		$req = Request::createFromEnvironment($env);
		$this->app->getContainer()['request'] = $req;
		$response = $this->app->run(true);
		$this->assertSame(200, $response->getStatusCode());
		$this->assertSame((string)$response->getBody(), "hello, roman"),
		(string)$response->getBody());
	}

	/*
	  - tests the GET fruit endpoint
	  - $resultString is holding mock data
	*/
	public function testGetFruit() {
		$resultedString = '[
			{"id":"1", "fruit_name":"apple", "fruit_color":"red", "season":"fall", "calories":"103", "description":"crunchy and refreshing"},
			{"id":"2", "fruit_name":"grapes", "fruit_color":"green", "season":"summer", "calories":"45", "description":"great for making white wine"}
	]';

	# mock the query class & fetchAll functions
	$query = $this->createMock('mockQuery');
	$query->method('fetchAll');
		->willReturn(json_decode($resultString, true)
	);
		$this->db->method('query')
			->willReturn($query);

	# mock the request environment (part of slim)
	$env = Environment::mock([
		'REQUEST_METHOD' => 'GET',
		'REQUEST_URI'	 => '/fruit',
	]);
	$req = Request::createFromEnvironment($env);
	$this->app->getContainer()['request'] = $req;

	# actually run the request through the app
	$response = $this->app->run(true);

	# assert expected status code and body
	$this->assertSame(200, $response->getStatusCode());
	$this->assertSame($resultString, (string)$response->getBody());
	}

	public function testGetFruitFailed() {
		$query = $this->createMock('mockQuery');
		$query->method('fetch')->willReturn(false);
		$this->db->method('query')->willReturn($query);
		$env = Environment::mock([
			'REQUEST_METHOD' => 'GET',
			'REQUEST_URI'    => '/fruit/1',
		]);
		$req = Request::createFromEnvironment($env);
		$this->app->getContainer()['request'] = $req;

		# actually run the request through the app
		$response = $this->app->run(true);

		# assert expected status code and body
		$this->assertSame(404, $response->getStatusCode());
		$this->assertSame('{"status":404, "message":"not found"}', (string)$response->getBody());
	}

	public function testUpdateFruit() {
		# expected result $resultString
		$resultString = '{"id":"1", "fruit_name":"apple", "fruit_color":"red", "season":"fall", "calories":"103", "description":"crunchy yet refreshing"}';

		# mock the query class & fetchAll functions
		$query = $this->createMock('mockQuery');
		$query->method('fetch')
			->willReturn(json_decode($resultString, true)
		);
		$this->db->method('query')
			->willReturn($query);
		 $this->db->method('exec')
		 	->willReturn(true);

		# mock the request environment (part of slim)
		$env = Environment::mock([
			'REQUET_METHOD' => 'PUT',
			'REQUEST_URI'   => '/fruit/1',
		]);
		$req = Request::createFromEnvironment($env);
		$requestBody = ["fruit_name" => "apple", "fruit_color" => "red", "season" => "fall", "calories" => "103", "description" => "crunchy yet refreshing"];
		$req = $req->withParsedBody($requestBody);
		$this->app->getContainer()['request'] = $req;

		# actually run the request through the app
		$response = $this->app->run(true);

		# assert expected status code and body
		$this->assertSame(200, $response->getStatusCode());
		$this->assertSame($resultString, (string)$response->getbody());
	}

	# test fruit update failed due to invalid fields
	
}
