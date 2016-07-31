<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


$db = new ezSQL_mysqli('root','root','greyareas','localhost');
$app = new \Slim\App([
	'settings' => [
        'displayErrorDetails' => true,
	]
]);
$app->get('/search.html', function (Request $request, Response $response) use ($db) {

	ob_start();

	if (!isset($_GET['postcode']) || empty($_GET['postcode'])) {
		$postcode = '';
	} else {
		$postcode = $_GET['postcode'];
	}

	if (!preg_match("/^[0-9]{4}$/", $postcode)) {
		$postcode = '';
	}	

	require(__DIR__.'/templates/search.php');
	
	$body = ob_get_clean();

	$response->write($body);

	return $response;


});

$app->get('/{lat}/{lon}/topostcode.json', function (Request $request, Response $response) use ($db) {
	header('Content-type: application/json');

	$lat = $request->getAttribute('lat');
	$lon = $request->getAttribute('lon');

	try {

		if (!is_numeric($lat) || !is_numeric($lon)) {
			throw new Exception ('Invalid position!',400);
		}

		$geospatial= $db->get_row("SELECT poa_code FROM postcodes WHERE st_within(GeomFromText('POINT($lon $lat)',1),SHAPE)");

		if (!$geospatial) {
			throw new Exception('cant find a post code for that area.',404);
		}

		$payload = [
			'response' => 200,
			'postcode' => $geospatial->poa_code
		];

	} catch (Exception $e) {
		$payload = [
			'response' => $e->getCode(),
			'message' => $e->getMessage()
		];
	}

	$jsonResponse = $response->withJson($payload,200);
	return $jsonResponse;

});

$app->get('/{postcode}.json', function (Request $request, Response $response) use ($db) {
	header('Content-type: application/json');
	try {

		$postcode = $request->getAttribute('postcode');

		if (!preg_match("/^[0-9]{4}$/", $postcode)) {
			throw new Exception('invalid Postcode!',400);
		}

		$geospatial= $db->get_row("SELECT poa_code, ST_AsGeoJSON(SHAPE) as 'geometry' FROM postcodes WHERE poa_code = '$postcode'");

		if (!$geospatial) {
			throw new Exception('Post code not found!!',404);
		}

		$countArr = [];

		//Get some pensioner counts
		$counts = $db->get_row("SELECT * FROM dss_demographics WHERE postcode = '$postcode'");
		if (!$counts) {
			throw new Exception('No demographic data found for this post code',404);
		}
		$seniors = is_numeric($counts->age_pension) ? (int) $counts->age_pension : 0; 
		$healthcare = is_numeric($counts->seniors_health_card) ? (int) $counts->seniors_health_card : 0; 
		//if we have more senior helthcare card holders we'll use that
		if ($healthcare < $seniors) {
			$countArr['Pensioners'] = $seniors;
		} else {
			$countArr['Pensioners'] = $healthcare;
		}
		//done getting pensioner counts

		//preliminary 
		// $facilities = $db->get_results("SELECT * FROM community_facilities WHERE st_within(`position`, (SELECT SHAPE FROM postcodes WHERE poa_code = '$postcode'))");

		// foreach ($facilities as $facility) {

		// 	if (!array_key_exists($facility->FEATURETYPE, $countArr)) {
		// 		$countArr[$facility->FEATURETYPE] = 0;
		// 	}
		// 	$countArr[$facility->FEATURETYPE]++;
		// }

		// $discounts = $db->get_results("SELECT * FROM business_discounts WHERE Outlet_Postcode = '$postcode'");
		// error_log($db->error);
		// foreach ($discounts as $discount) {

		// 	if (!array_key_exists('Seniors Discount Location', $countArr)) {
		// 		$countArr['Seniors Discount Location'] = 0;
		// 	}
		// 	$countArr['Seniors Discount Location']++;
		// }

		$scores = $db->get_row("SELECT * FROM postcode_scores WHERE postcode = '$postcode'");

		$areas = [
			'cultural',
			'social',
			'connected',
			'economic',
			'active',
			'average',
		];

		$percentages = [];
		$vals = [];
		$maxes = [];
		$mins = [];
		$avgs = [];

		foreach ($areas as $area) {
			$max = (int) $db->get_var("SELECT max($area) FROM postcode_scores");
			$avg = (int) $db->get_var("SELECT avg($area) FROM postcode_scores");
			$min = (int) $db->get_var("SELECT min($area) FROM postcode_scores");

			$value = (int) $scores->{$area};
			//min always seems to be 0, so leave it out
			$percentile = ceil( ($value / ($max) ) *100);

			if (is_nan($percentile) || is_infinite($percentile)) {
				$percentile = 0;
			} 
			if ($percentile > 100) {
				$percentile = 100;
			}

			$percentages[$area] = $percentile;
			$vals[$area] = $value;
			$maxes[$area] = $max;
			$mins[$area] = $min;
			$avgs[$area] = $avg;
		};

		// var_dump($vals);
		// var_dump($mins);
		// var_dump($maxes);
		// var_dump($percentages);
		// exit;

		$body = [

			'response' => 200,
			'postcode' => $geospatial->poa_code,
			'counts' => $countArr,
			'percentages' => $percentages,
			'values' => $vals,
			'min' => $mins,
			'max' => $maxes,
			'avgs' => $avgs,
			'geometry' => [
				'type' => 'FeatureCollection',
				'features' => [
						[
							'type' => 'Feature',
							'geometry' => json_decode($geospatial->geometry),
							"properties" => [
								'postcode' => $geospatial->poa_code,
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
