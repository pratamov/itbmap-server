<?php

	require 'Slim/Slim.php';
	\Slim\Slim::registerAutoloader();
	
	$app = new \Slim\Slim(
	
		array(
			'debug'		=>	false,
			'mode'		=>	'maintenance'
		)
		
	);
	
	$app->config = array(
		
		'host'		=>	'127.0.0.1',
		'dbname'	=>	'itbmap',
		'uname'		=>	'root',
		'passwd'	=>	'asdqwe123'
			
	);
	
	$log = $app->getLog();

	$app->get('/', function() use ($app, $log){
		
		echo "test";
		
	});
	
	
	$app->group('/v1', function () use ($app, $log) {
		
		/*
		 * @api {get} /beacons
		 * @apiDescription Get list uuid of availlable beacon in area
		 * @apiParam {String} [region_name]  Optional specific region which cover the beacons.
		 *
		 */
		$app->get('/beacons', function() use ($app, $log){
			
			$app->response->headers->set('Content-Type', 'application/json');

			try{
				
				$db = new PDO('mysql:host='.$app->config['host'].';dbname='.$app->config['dbname'].';charset=utf8', $app->config['uname'], $app->config['passwd']);
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				
				$return = new stdClass();

				$stmt = $db->prepare('SELECT DISTINCT uuid, map_name FROM map');
				$stmt->execute();
				
				$beacon_list = array();
				while($datum = $stmt->fetch(PDO::FETCH_ASSOC)){
					array_push($beacon_list, array(
						'uuid'=>$datum['uuid'],
						'map_name'=>$datum['map_name']
					));
				}
				
				$return->message = "ok";
				$return->code = 200;
				
				$return->data['beacon_list'] = $beacon_list;
					
				
				$app->halt(200, json_encode($return));
				$app->stop();
				
				
			} catch(PDOException $e) {
				
				$return = new stdClass();
				$return->message 	= $e->getMessage();
				$return->code		= 500;
				$app->halt(500, json_encode($return));
				$app->stop();
				
			}
			
		});

		$app->get('/beacons/:uuid/map_name', function($uuid) use ($app, $log){
			
			$app->response->headers->set('Content-Type', 'application/json');

			try{
				
				$db = new PDO('mysql:host='.$app->config['host'].';dbname='.$app->config['dbname'].';charset=utf8', $app->config['uname'], $app->config['passwd']);
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				$stmt = $db->prepare('SELECT map_name FROM map WHERE uuid = :uuid');
				$stmt->execute(
					array(
						'uuid' => $uuid
					)
				);
				
				$map_name = null;
				if ($datum = $stmt->fetch(PDO::FETCH_ASSOC)){
					$map_name = $datum['map_name'];
				}
				
				$return = new stdClass();
				$return->message 		= "ok";
				$return->code 			= 200;
				$return->data['map_name'] = $map_name;
				$app->halt(200, json_encode($return));
				$app->stop();
				
				
			} catch(PDOException $e) {
				
				$return = new stdClass();
				$return->message 	= $e->getMessage();
				$return->code		= 500;
				$app->halt(500, json_encode($return));
				$app->stop();
				
			}
			
		});
		
		
		$app->get('/beacons/:map_name/uuid', function($map_name) use ($app, $log){
			
			$app->response->headers->set('Content-Type', 'application/json');

			try{
				
				$db = new PDO('mysql:host='.$app->config['host'].';dbname='.$app->config['dbname'].';charset=utf8', $app->config['uname'], $app->config['passwd']);
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				$stmt = $db->prepare('SELECT uuid FROM map WHERE map_name = :map_name');
				$stmt->execute(
					array(
						'map_name' => $map_name
					)
				);
				
				$uuid = null;
				if ($datum = $stmt->fetch(PDO::FETCH_ASSOC)){
					$uuid = $datum['uuid'];
				}
				
				$return = new stdClass();
				$return->message 	= "ok";
				$return->code 		= 200;
				$return->data['uuid'] = $uuid;
				$app->halt(200, json_encode($return));
				$app->stop();
				
				
			} catch(PDOException $e) {
				
				$return = new stdClass();
				$return->message 	= $e->getMessage();
				$return->code		= 500;
				$app->halt(500, json_encode($return));
				$app->stop();
				
			}
			
		});
		
		$app->get('/beacons/:uuid', function($uuid) use ($app, $log){
			
			$app->response->headers->set('Content-Type', 'application/json');
			
			try{
				$db = new PDO('mysql:host='.$app->config['host'].';dbname='.$app->config['dbname'].';charset=utf8', $app->config['uname'], $app->config['passwd']);
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				$stmt = $db->prepare('SELECT * FROM map WHERE uuid = :uuid');
				
				if ($map_name = $app->request->get('map_name')){
					
					$stmt = $db->prepare('SELECT * FROM map WHERE map_name = :map_name');
					
					$stmt->execute(
						array(
							'map_name' => $map_name
						)
					);
					
				}
				else{
					
					$stmt->execute(
						array(
							'uuid' => $uuid
						)
					);

				}
				
				$map = null;
				if ($datum = $stmt->fetch(PDO::FETCH_ASSOC)){
					$map = $datum;
				}
				
				$return = new stdClass();
				$return->message 	= "ok";
				$return->code 		= 200;
				$return->data['map'] 	= $map;
				$app->halt(200, json_encode($return));
				$app->stop();

			} catch(PDOException $e) {
				
				$return = new stdClass();
				$return->message 	= $e->getMessage();
				$return->code		= 500;
				$app->halt(500, json_encode($return));
				$app->stop();
				
			}
			
		});
	
	});
	
	$app->run();
	
?>