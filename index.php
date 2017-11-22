<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require './vendor/autoload.php';

$app = new \Slim\App;

use Medoo\Medoo;

$container = $app->getContainer();

// Connect database with medoo
$container['database'] = function () {
	return new Medoo([
		'database_type' => 'sqlite',
		'database_file' => 'test.db'
	]); 
};

// Get list of books
$app->get('/books',  function($request, $response, $args) {
    $data = $this->database->select("Books", "*");
    
    return $response->write(json_encode($data));
});

// Add new book
$app->post('/books', function($request, $response, $args) {
    $date = new DateTime();
    $newId = $date->getTimestamp();
    $data = $request->getBody()->getContents();
    $datanew = json_decode($data);
    $this->database->insert("Books", [
        'id' => $newId,
        'name' => $datanew->name,
        'author' => $datanew->author
    ]);

    return $response->write(json_encode($this->database));
});

// Update book (name and author) based on id
$app->put('/books/{id}', function($request, $response, $args) {
    $data = $request->getBody()->getContents();
    $datanew = json_decode($data);
    $this->database->update("Books", [
        "name" => $datanew->name,
        "author" => $datanew->author,
    ], [
        "id" => $args['id']
    ]);

    return $response->write(json_encode($this->database));
});

// Delete book based on id
$app->delete('/books/{id}', function($request, $response, $args) {
    $this->database->delete("Books", [
        "id" => $args['id']
    ]);

    return $response->write(json_encode($this->database));
});

$app->run();
?>