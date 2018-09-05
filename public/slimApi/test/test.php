<?php
use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
require './vendor/autoload.php';

# empty class definitions for phpunit to mock
class mockQuery {
	public function fetchAll(){}
};

class mockDB {
	public function query(){}
};

class FruitTest extends TestCase {

	protected $app;
	protected $db;

	# execute setup code before each test is run
	public function setUp() {
		$this->db = $this->createMock('modDB');
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
		$this->assertSame($response->getStatusCode(), 200);
		$this->assertSame((string)$response->getBody(), "hello, roman")
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
	$query->metho('fetchAll');
		->willReturn(json_decode($resultString, true)
	);
		$this->db->method('query')
			->willReturn($query);
	}

	# mock the request environment (part of slim)
	$env = Environment::mock([
		'REQUEST_METHOD' => 'GET',
		'REQUEST_URI'	 => '/fruit',
	]);

	# actually run the request through the app
	$response = $this->app->run(true);

	# assert expected status code and body
	$this->assertSame($response->getStatusCode(), 200);
	$this->assertSame((string)$response->getBody(), $resultedString);
}