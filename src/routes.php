<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


$db = new ezSQL_mysqli('root','root','greyareas','localhost');
$app = new \Slim\App([
	'settings' => [
        'displayErrorDetails' => true,
	]
]);
$app->get('/', function (Request $request, Response $response) use ($db) {

	$response->write(file_get_contents(__DIR__.'/templates/index.html'));

	return $response;


});
$app->get('/{postcode}.json', function (Request $request, Response $response) use ($db) {
	header('Content-type: application/json');
	try {

		$postcode = $request->getAttribute('postcode');

		if (!preg_match("/^[0-9]{4}$/", $postcode)) {
			throw new Exception('invalid Postcode!',400);
		}
		$res= $db->get_row("SELECT poa_code, ST_AsGeoJSON(SHAPE) as 'geometry' FROM test WHERE poa_code = '$postcode'");

		if (!$res) {
			throw new Exception('Post code not found!!',404);
		}

		$body = [

			'response' => 200,
			'postcode' => $res->poa_code,
			'geometry' => [
				'type' => 'FeatureCollection',
				'features' => [
						[
							'type' => 'Feature',
							'geometry' => json_decode($res->geometry),
							"properties" => [
								'postcode' => $res->poa_code,
							]
						]
					]
			]
			//todo add report functions resuls here?

		];

		$jsonResponse = $response->withJson($body,200);
		return $jsonResponse;


	} catch (Exception $e) {

		$err = [
			'response' => $e->getCode(),
			'message' => $e->getMessage()
		];
 		$jsonResponse = $response->withJson($err,200);
		return $jsonResponse;
	}
});

$app->run();