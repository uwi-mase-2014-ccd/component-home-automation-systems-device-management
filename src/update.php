<?php

use GuzzleHttp\Client;

try {
	ini_set("display_errors", 1);
	ini_set("track_errors", 1);
	ini_set("html_errors", 1);
	error_reporting(E_ALL);

	require 'vendor/autoload.php';


	if ($_SERVER['REQUEST_METHOD'] != 'POST') {
		$response = array(
			'code' => 400,
			'data' => new stdClass,
			'debug' => array(
				'data' => new stdClass,
				'message' => 'This service only accepts a POST Request.'
			)
		);

		die(json_encode($response, JSON_PRETTY_PRINT));
	}
	
	// Parse JSON Request
	$inputJSON = file_get_contents('php://input');
	$data = json_decode( $inputJSON, TRUE ); //convert JSON into array

	if ($data !== NULL) {
		$_POST = $data;
	}

	if (!isset($_POST['name']) || !isset($_POST['values']) || !isset($_POST['id'])) {
		$response = array(
			'code' => 400,
			'data' => new stdClass,
			'debug' => array(
				'data' => new stdClass,
				'message' => 'This service requires the following arguments [name, values, id].'
			)
		);

		die(json_encode($response, JSON_PRETTY_PRINT));	
	}
	
	if (!is_array($_POST['values'])) {
		$response = array(
			'code' => 400,
			'data' => new stdClass,
			'debug' => array(
				'data' => new stdClass,
				'message' => 'This values argument must be a valid array.'
			)
		);

		die(json_encode($response, JSON_PRETTY_PRINT));	
	}
	
	foreach($_POST['values'] as $value) {
		if (!isset($value['title']) || !isset($value['value'])) {
			$response = array(
				'code' => 400,
				'data' => new stdClass,
				'debug' => array(
					'data' => array(
						'value' => $value
					),
					'message' => 'Each value must have a title and a value.'
				)
			);

			die(json_encode($response, JSON_PRETTY_PRINT));	
		}
	}

	function executeDBQuery($query) {
		$RESQUEST_BODY = array(
			"server" 	=> "ticketmanager.mysoftware.io",
			"database" 	=> "projecthas",
			"userID" 	=> "projecthas-db",
			"password" 	=> "password",
			"dbtype"	=> "mysql",
		
			"query" 	=> $query
		);
	
		$DB_ENDPOINT = 'http://ma.holycrosschurchjm.com/dbcomponent.php';
	
		$client = new Client();

		// Send request to DB Component
		$res = $client->post($DB_ENDPOINT, array(
			'body' => $RESQUEST_BODY
		));
	
	
		if (isset($_GET['_debug'])) {
			var_dump($res);
			
			var_dump(array('body' => (string)$res->getBody()));
		}
	
		// Check if it succeeded
		if ($res->getStatusCode() == 200) {
			$body = trim($res->getBody());
			// Clean up body
			$bodyLines = explode('\n', $body);
			$cleanedBody = '';
			$jsonStart = false;
			foreach($bodyLines as $line) {
				$line = trim($line);
				if ($line[0] == '{') {
					$jsonStart = true;
				}
				
				if ($jsonStart) {
					$cleanedBody = $cleanedBody . $line . "\n";
				}
			}
	
			$body = json_decode($cleanedBody, TRUE);
	
			if (isset($_GET['_debug'])) {
				var_dump(array('body-cleaned' => $cleanedBody));
				var_dump(array('body-parsed' => $body));
			}
			
			if ($body == NULL || !isset($body['code']) || !isset($body['data'])) {
				throw new Exception('The Database component returned an enexpected result.');
			}
		
			if ($body['code'] != 200) {
				throw new Exception('The Database component returned an enexpected result.');
			}
			
			if (strtolower($body['data']) != 'successful') {
				throw new Exception('The Database component returned an enexpected result.');
			}
		
			return $body['data'];
		} else {
			throw new Exception('The Database component returned an enexpected result.');
		}
	}
	
	$cleanValues = array_map(function($value) {
		return array(
			'title' => str_replace("'", "\'", $value['title']),
			'value' => str_replace("'", "\'", $value['value'])
		);
	}, $_POST['values']);
	
	$encodedValues = json_encode($cleanValues);
	$deviceName = str_replace("'", "\'", $_POST['name']);
	$deviceId = str_replace("'", "\'", $_POST['id']);
	
	$query = 'UPDATE devices ';
	$query .= "SET `name` = '" . $deviceName . "', ";
	$query .= " `values` = '" . $encodedValues . "' ";
	$query .= "WHERE `id` = '" . $deviceId . "' ";
	
	$returnedRows = executeDBQuery($query);
	$response = array(
		'code' => 200,
		'data' => array(
			'device' => array(
				'id' => $deviceId,
				'name' => $deviceName,
				'values' => $cleanValues
			),
			'device-row' => $returnedRows,
			'message' => "Success"
		),
		'debug' => new stdClass
	);
	
	die(json_encode($response, JSON_PRETTY_PRINT));
	
} catch (Exception $e) {
	$response = array(
		'code' => 500,
		'data' => new stdClass,
		'debug' => array(
			'data' => array(
				'Caught exception: ' => $e->getMessage(),
			),
			'message' => 'An exception has occured.'
		)
	);
	
	die(json_encode($response, JSON_PRETTY_PRINT));
}
