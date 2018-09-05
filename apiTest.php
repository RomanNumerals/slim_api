<?php

public function testHelloName() {
  $env = Environment::mock([
    'REQUEST_METHOD' => 'GET',
    'REQUEST_URI' => '/hello/Test',
  ]);
  $req = Request::createFromeEnvironment($env);
  $this->app->getContainer()['request'] = $req;
  $response = $this->app->run(true);
  $this->assertSame($response->getStatusCode(), 200);
  $this->assertSame((string)$response->getBody(), "Hello, Test");
}
