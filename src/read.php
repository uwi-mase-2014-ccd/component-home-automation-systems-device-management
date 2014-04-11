<?php

use GuzzleHttp\Client;

try {
	ini_set("display_errors", 1);
	ini_set("track_errors", 1);
	ini_set("html_errors", 1);
	error_reporting(E_ALL);

	require 'vendor/autoload.php';
	
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
			$body = str_replace("\n\r", "\n", $body);
			
			$bodyLines = explode("\n", $body);
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
		
			$rows = $body['data'];
			
			if (!is_array($rows)) {
				$rows = array();
			}
			return $rows;
		} else {
			throw new Exception('The Database component returned an enexpected result.');
		}
	}
	
	if (isset($_GET['id'])) {
		$deviceId = str_replace("'", "\'", $_GET['id']);
	
		$query = "SELECT * FROM devices WHERE id = '" . $deviceId . "'";
	
		$returnedRows = executeDBQuery($query);
		
		if (count($returnedRows) == 0) {
			$response = array(
				'code' => 500,
				'data' => new stdClass,
				'debug' => array(
					'data' => array(
						'id' => $deviceId,
					),
					'message' => 'An exception has occured. No device found with the given id.'
				)
			);
	
			die(json_encode($response, JSON_PRETTY_PRINT));
		}
		
		if (isset($returnedRows[0]['values'])) {
			$returnedRows[0]['values'] = json_decode($returnedRows[0]['values']);
		}
		
		$response = array(
			'code' => 200,
			'data' => array(
				'device' => $returnedRows[0],
				'message' => "Success"
			),
			'debug' => new stdClass
		);
	} else {
		$query = 'SELECT * FROM devices ';
	
		$returnedRows = executeDBQuery($query);
		
		$returnedRows = array_map(function($row) {
			if (isset($row['values'])) {
				$row['values'] = json_decode($row['values']);
			}
			
			return $row;
		}, $returnedRows);
		
		$response = array(
			'code' => 200,
			'data' => array(
				'devices' => $returnedRows,
				'message' => "Success"
			),
			'debug' => new stdClass
		);
	}
	
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
