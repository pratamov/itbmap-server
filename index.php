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
		
		echo shell_exec("pwd");
		
	});
	
	
	$app->group('/v1', function () use ($app, $log) {
		
		/*
		 * API-09
		 * @api {post} /maps
		 * @apiDescription
		 *
		 */
		$app->post('/regions', function() use ($app, $log){
			$app->response->headers->set('Content-Type', 'application/json');
			
			try{
				$db = new PDO('mysql:host='.$app->config['host'].';dbname='.$app->config['dbname'].';charset=utf8', $app->config['uname'], $app->config['passwd']);
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				
				$region = new stdClass();
				
				$region->uuid = $app->request->post('uuid');
				$region->region_name = $app->request->post('region_name');
				
				$stmt = $db->prepare('
					INSERT INTO region 
						(region_name, uuid) 
					VALUES 
						(:region_name, :uuid)
					');
				
				$stmt->execute(
				
					array(
						'region_name' => $map->region_name,
						'uuid' => $map->uuid
					)
					
				);
				
				$return = new stdClass();
				$return->message 	= "ok";
				$return->code 		= 200;
				$return->data 		= $app->request()->post();
				$app->halt(200, json_encode($return));
				$app->stop();

			} catch(PDOException $e) {
				
				$return = new stdClass();
				$return->message 	= $e->getMessage();
				$return->code		= 500;
				$app->halt(200, json_encode($return));
				$app->stop();
				
			}
		});
		
		/*
		 * API-01
		 * @api {get} /regions/
		 * @apiDescription Get list uuid of availlable beacon in area
		 * @apiParam {String} [region_name]  Optional specific region which cover the beacons.
		 *
		 */
		$app->get('/regions/', function() use ($app, $log){
			
			$app->response->headers->set('Content-Type', 'application/json');

			try{
				
				$db = new PDO('mysql:host='.$app->config['host'].';dbname='.$app->config['dbname'].';charset=utf8', $app->config['uname'], $app->config['passwd']);
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				
				$return = new stdClass();

				$stmt = $db->prepare('SELECT DISTINCT uuid, region_name FROM region');
				$stmt->execute();
				
				$regions = array();
				
				while($record = $stmt->fetch(PDO::FETCH_ASSOC)){
					array_push($regions, array(
						'uuid'=>$record['uuid'],
						'region_name'=>$record['region_name']
					));
				}
				
				$return->message = "ok";
				$return->code = 200;
				$return->data['regions'] = $regions;
					
				
				$app->halt(200, json_encode($return));
				$app->stop();
				
				
			} catch(PDOException $e) {
				
				$return = new stdClass();
				$return->message 	= $e->getMessage();
				$return->code		= 500;
				$app->halt(200, json_encode($return));
				$app->stop();
				
			}
			
		});
		
		/*
		 * API-02
		 * @api {get} /maps
		 * @apiDescription
		 *
		 */
		$app->get('/maps/', function() use ($app, $log){
			
			$app->response->headers->set('Content-Type', 'application/json');

			try{
				
				$db = new PDO('mysql:host='.$app->config['host'].';dbname='.$app->config['dbname'].';charset=utf8', $app->config['uname'], $app->config['passwd']);
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				
				$return = new stdClass();

				$stmt = $db->prepare('SELECT DISTINCT * FROM map');
				$stmt->execute();
				
				$maps = array();
				while($record = $stmt->fetch(PDO::FETCH_ASSOC)){
					
					array_push($maps, $record);
					
				}
				
				$return->message = "ok";
				$return->code = 200;
				
				$return->data['maps'] = $maps;
					
				
				$app->halt(200, json_encode($return));
				$app->stop();
				
				
			} catch(PDOException $e) {
				
				$return = new stdClass();
				$return->message 	= $e->getMessage();
				$return->code		= 500;
				$app->halt(200, json_encode($return));
				$app->stop();
				
			}
			
		});
		
		/*
		 * API-03
		 * @api {get} /maps/:minor_id/
		 * @apiDescription
		 *
		 */
		$app->get('/maps/:minor_id/', function($minor_id) use ($app, $log){
			
			$app->response->headers->set('Content-Type', 'application/json');
			
			try{
				$db = new PDO('mysql:host='.$app->config['host'].';dbname='.$app->config['dbname'].';charset=utf8', $app->config['uname'], $app->config['passwd']);
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				$stmt = $db->prepare('SELECT * FROM map WHERE minor_id = :minor_id');
				
				$stmt->execute(
				
					array(
						'minor_id' => $minor_id
					)
					
				);

				$maps = null;
				if ($record = $stmt->fetch(PDO::FETCH_ASSOC)){
					
					$stmt_geofence = $db->prepare('SELECT * FROM geofence WHERE minor_id = :minor_id');
					
					$stmt_geofence->execute(
					
						array(
							'minor_id' => $minor_id
						)
						
					);
					
					$geofences = array();
					
					while ($record_geofence = $stmt->fetch(PDO::FETCH_ASSOC)){
						
						array_push($geofences, $record_geofence);
						
					}
					
					$record['geofences'] = $geofences;
					
					$maps = $record;
				}
				
				$return = new stdClass();
				$return->message 	= "ok";
				$return->code 		= 200;
				$return->data['maps'] 	= $maps;
				$app->halt(200, json_encode($return));
				$app->stop();

			} catch(PDOException $e) {
				
				$return = new stdClass();
				$return->message 	= $e->getMessage();
				$return->code		= 500;
				$app->halt(200, json_encode($return));
				$app->stop();
				
			}
			
		});
		
		/*
		 * API-04
		 * @api {post} /maps/
		 * @apiDescription
		 *
		 */
		$app->post('/maps/', function() use ($app, $log){
			
			$app->response->headers->set('Content-Type', 'application/json');
			
			try{
				$db = new PDO('mysql:host='.$app->config['host'].';dbname='.$app->config['dbname'].';charset=utf8', $app->config['uname'], $app->config['passwd']);
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				
				$maps = new stdClass();
				
				$maps->minor_id = $app->request->post('minor_id');
				$maps->uuid = $app->request->post('uuid');
				$maps->position_x_beacon_1 = $app->request->post('position_x_beacon_1');
				$maps->position_y_beacon_1 = $app->request->post('position_y_beacon_1');
				$maps->position_x_beacon_2 = $app->request->post('position_x_beacon_2');
				$maps->position_y_beacon_2 = $app->request->post('position_y_beacon_2');
				$maps->position_x_beacon_3 = $app->request->post('position_x_beacon_3');
				$maps->position_y_beacon_3 = $app->request->post('position_y_beacon_3');
				$maps->map_name = $app->request->post('map_name');
				$maps->map_description = $app->request->post('map_description');
				$maps->map_real_width = $app->request->post('map_real_width');
				$maps->map_real_height = $app->request->post('map_real_height');
				
				//AUTOGENERATE
				$maps->map_raw_image_filename = "";
				$maps->map_tile_image_url = "";
				$maps->map_height = 0;
				$maps->map_width = 0;
				
				$stmt = $db->prepare('
					INSERT INTO map 
						(minor_id, uuid, position_x_beacon_1, position_x_beacon_2, position_x_beacon_3, position_y_beacon_1, position_y_beacon_2, position_y_beacon_3,
						map_name, map_description, map_real_width, map_real_height, map_raw_image_filename, map_tile_image_url, map_height, map_width) 
					VALUES 
						(:minor_id, :uuid, :position_x_beacon_1, :position_x_beacon_2, :position_x_beacon_3, :position_y_beacon_1, :position_y_beacon_2, :position_y_beacon_3,
						:map_name, :map_description, :map_real_width, :map_real_height, :map_raw_image_filename, :map_tile_image_url, :map_height, :map_width)');
				
				$stmt->execute(
				
					array(
						'minor_id' => $maps->minor_id,
						'uuid' => $maps->uuid,
						'position_x_beacon_1' => $maps->position_x_beacon_1,
						'position_x_beacon_2' => $maps->position_x_beacon_2,
						'position_x_beacon_3' => $maps->position_x_beacon_3,
						'position_y_beacon_1' => $maps->position_y_beacon_1,
						'position_y_beacon_2' => $maps->position_y_beacon_2,
						'position_y_beacon_3' => $maps->position_y_beacon_3,
						'map_name' => $maps->map_name,
						'map_description' => $maps->map_description,
						'map_real_width' => $maps->map_real_width,
						'map_real_height' => $maps->map_real_height,
						'map_raw_image_filename' => $maps->map_raw_image_filename,
						'map_tile_image_url' => $maps->map_tile_image_url,
						'map_height' => $maps->map_height,
						'map_width' => $maps->map_width
					)
					
				);
				
				$return = new stdClass();
				$return->message 	= "ok";
				$return->code 		= 200;
				$return->data['maps'] 		= $maps;
				$app->halt(200, json_encode($return));
				$app->stop();

			} catch(PDOException $e) {
				
				$return = new stdClass();
				$return->message 	= $e->getMessage();
				$return->code		= 500;
				$app->halt(200, json_encode($return));
				$app->stop();
				
			}
			
		});
		
		/*
		 * API-05
		 * @api {put} /maps/:minor_id/
		 * @apiDescription
		 *
		 */
		$app->put('/maps/:minor_id/', function($minor_id) use ($app, $log){
			
			$app->response->headers->set('Content-Type', 'application/json');
			
			try{
				$db = new PDO('mysql:host='.$app->config['host'].';dbname='.$app->config['dbname'].';charset=utf8', $app->config['uname'], $app->config['passwd']);
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				
				$maps = new stdClass();
				
				$maps->minor_id = $minor_id;
				
				$stmt = $db->prepare('SELECT * FROM map WHERE minor_id = :minor_id AND uuid = :uuid');
				
				$stmt->execute(
				
					array(
					
						'minor_id' => $minor_id,
						'uuid' => $uuid
						
					)
					
				);
				
				if ($record = $stmt->fetch(PDO::FETCH_OBJ)){
					$maps = $record;
					$maps->uuid = 
						$app->request->put('uuid') ? $app->request->put('uuid') : $record->uuid;
					$maps->position_x_beacon_1 = 
						$app->request->put('position_x_beacon_1') ? $app->request->put('position_x_beacon_1') : $record->position_x_beacon_1;
					$maps->position_y_beacon_1 = 
						$app->request->put('position_y_beacon_1') ? $app->request->put('position_y_beacon_1') : $record->position_y_beacon_1;
					$maps->position_x_beacon_2 = 
						$app->request->put('position_x_beacon_2') ? $app->request->put('position_x_beacon_2') : $record->position_x_beacon_2;
					$maps->position_y_beacon_2 = 
						$app->request->put('position_y_beacon_2') ? $app->request->put('position_y_beacon_2') : $record->position_y_beacon_2;
					$maps->position_x_beacon_3 = 
						$app->request->put('position_x_beacon_3') ? $app->request->put('position_x_beacon_3') : $record->position_x_beacon_3;
					$maps->position_y_beacon_3 = 
						$app->request->put('position_y_beacon_3') ? $app->request->put('position_y_beacon_3') : $record->position_y_beacon_3;
					$maps->map_name = 
						$app->request->put('map_name') ? $app->request->put('map_name') : $record->map_name;
					$maps->map_description =
						$app->request->put('map_description') ? $app->request->put('map_description') : $record->map_description;
					$maps->map_real_width = 
						$app->request->put('map_real_width') ? $app->request->put('map_real_width') : $record->map_real_width;
					$maps->map_real_height = 
						$app->request->put('map_real_height') ? $app->request->put('map_real_height') : $record->map_real_height;
				}
				else{
					throw new PDOException("Map with minor_id ".$minor_id." is not found.");
				}
				
				$stmt_update = $db->prepare('
					UPDATE map SET
						uuid = :uuid,
						position_x_beacon_1 = :position_x_beacon_1, 
						position_x_beacon_2 = :position_x_beacon_2,
						position_x_beacon_3 = :position_x_beacon_3, 
						position_y_beacon_1 = :position_y_beacon_1, 
						position_y_beacon_2 = :position_y_beacon_2, 
						position_y_beacon_3 = :position_y_beacon_3, 
						map_name = :map_name, 
						map_description = :map_description, 
						map_real_width = :map_real_width, 
						map_real_height = :map_real_height
					
					WHERE minor_id = :minor_id');
				
				$stmt_update->execute(
				
					array(
						'minor_id' => $maps->minor_id,
						'uuid' => $maps->uuid,
						'position_x_beacon_1' => $maps->position_x_beacon_1,
						'position_x_beacon_2' => $maps->position_x_beacon_2,
						'position_x_beacon_3' => $maps->position_x_beacon_3,
						'position_y_beacon_1' => $maps->position_y_beacon_1,
						'position_y_beacon_2' => $maps->position_y_beacon_2,
						'position_y_beacon_3' => $maps->position_y_beacon_3,
						'map_name' => $maps->map_name,
						'map_description' => $maps->map_description,
						'map_real_width' => $maps->map_real_width,
						'map_real_height' => $maps->map_real_height
					)
					
				);
				
				$return = new stdClass();
				$return->message 		= "ok";
				$return->code 			= 200;
				$return->data['maps'] 	= $maps;
				$app->halt(200, json_encode($return));
				$app->stop();

			} catch(PDOException $e) {
				
				$return = new stdClass();
				$return->message 	= $e->getMessage();
				$return->code		= 500;
				$app->halt(200, json_encode($return));
				$app->stop();
				
			}
			
		});
		
		/*
		 * API-06
		 * @api {delete} /maps/:minor_id/
		 * @apiDescription
		 *
		 */
		$app->delete('/maps/:minor_id/', function($minor_id) use ($app, $log){
			
			$app->response->headers->set('Content-Type', 'application/json');
			
			try{
				$db = new PDO('mysql:host='.$app->config['host'].';dbname='.$app->config['dbname'].';charset=utf8', $app->config['uname'], $app->config['passwd']);
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				
				$map = new stdClass();
				$map->minor_id = $minor_id;
				
				$stmt = $db->prepare('SELECT * FROM map WHERE minor_id = :minor_id');
				
				$stmt->execute(
				
					array(
						'minor_id' => $map->minor_id
					)
					
				);
				
				$return = new stdClass();
				$return->message 	= "ok";
				$return->code 		= 200;
				$return->data 		= $map;
				$app->halt(200, json_encode($return));
				$app->stop();

			} catch(PDOException $e) {
				
				$return = new stdClass();
				$return->message 	= $e->getMessage();
				$return->code		= 500;
				$app->halt(200, json_encode($return));
				$app->stop();
				
			}
			
		});
		
		
		/*
		 * API-07
		 * @api {post} /maps/:minor_id/geofences/
		 * @apiDescription
		 *
		 */
		$app->post('/maps/:minor_id/geofences/', function($minor_id) use ($app, $log){
			$app->response->headers->set('Content-Type', 'application/json');
			
			try{
				$db = new PDO('mysql:host='.$app->config['host'].';dbname='.$app->config['dbname'].';charset=utf8', $app->config['uname'], $app->config['passwd']);
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				
				$geofences = new stdClass();
				
				$geofences->minor_id = $minor_id;
				$geofences->name = $app->request->post('name');
				$geofences->name = $app->request->post('name');
				$geofences->x1 = $app->request->post('x1');
				$geofences->y1 = $app->request->post('y1');
				$geofences->x2 = $app->request->post('x2');
				$geofences->y2 = $app->request->post('y2');
				$geofences->x3 = $app->request->post('x3');
				$geofences->y3 = $app->request->post('y3');
				$geofences->x4 = $app->request->post('x4');
				$geofences->y4 = $app->request->post('y4');
				
				$stmt = $db->prepare('
					INSERT INTO geofence 
						(id_geofence, minor_id, name, x1, y1, x2, y2, x3, y3, x4, y4) 
					VALUES 
						(:id_geofence, :minor_id, :name, :x1, :y1, :x2, :y2, :x3, :y3, :x4, :y4)
					');
				
				$stmt->execute(
				
					array(
						id_geofence => $geofences->id_geofence, 
						minor_id => $geofences->minor_id, 
						name => $geofences->name, 
						x1 => $geofences->x1, 
						y1 => $geofences->y1, 
						x2 => $geofences->x2, 
						y2 => $geofences->y2, 
						x3 => $geofences->x3, 
						y3 => $geofences->y3, 
						x4 => $geofences->x4, 
						y4 => $geofences->y4
					)
					
				);
				
				$return = new stdClass();
				$return->message 			= "ok";
				$return->code 				= 200;
				$return->data['geofences'] 	= $geofences;
				$app->halt(200, json_encode($return));
				$app->stop();

			} catch(PDOException $e) {
				
				$return = new stdClass();
				$return->message 	= $e->getMessage();
				$return->code		= 500;
				$app->halt(200, json_encode($return));
				$app->stop();
				
			}
		});
		
		/*
		 * API-08
		 * @api {delete} /geofence/:id_geofence/
		 * @apiDescription
		 *
		 */
		$app->delete('/geofence/:id_geofence/', function($id_geofence) use ($app, $log){
			$app->response->headers->set('Content-Type', 'application/json');
			
			try{
				$db = new PDO('mysql:host='.$app->config['host'].';dbname='.$app->config['dbname'].';charset=utf8', $app->config['uname'], $app->config['passwd']);
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				
				$geofences = new stdClass();
				$geofences->id_geofence = $id_geofence;
				
				$stmt = $db->prepare('SELECT * FROM geofence WHERE id_geofence = :id_geofence');
				
				$stmt->execute(
				
					array(
						'id_geofence' => $geofences->id_geofence
					)
					
				);
				
				$return = new stdClass();
				$return->message 	= "ok";
				$return->code 		= 200;
				$return->data['geofences'] 		= $geofences;
				$app->halt(200, json_encode($return));
				$app->stop();

			} catch(PDOException $e) {
				
				$return = new stdClass();
				$return->message 	= $e->getMessage();
				$return->code		= 500;
				$app->halt(200, json_encode($return));
				$app->stop();
				
			}
		});
		
		
	});
	
	$app->group('/calibration', function () use ($app, $log) {
		
		$app->get('/', function() use ($app, $log){
			$app->response->headers->set('Content-Type', 'application/json');
			
			try{
				$db = new PDO('mysql:host='.$app->config['host'].';dbname='.$app->config['dbname'].';charset=utf8', $app->config['uname'], $app->config['passwd']);
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			
				$data = new stdClass();
				$data->uuid = $app->request->get('uuid');
				$data->minor_id = $app->request->get('minor_id');
				$data->major_id = $app->request->get('major_id');
				$data->distance_prediction = $app->request->get('distance_prediction');
				$data->distance_measured = $app->request->get('distance_measured');
				
				$stmt = $db->prepare('
						INSERT INTO calibration 
							(uuid, minor_id, major_id, distance_prediction, distance_measured) 
						VALUES 
							(:uuid, :minor_id, :major_id, :distance_prediction, :distance_measured)
						');
					
				$stmt->execute(
				
					array(
						uuid => $data->uuid, 
						minor_id => $data->minor_id, 
						major_id => $data->major_id, 
						distance_prediction => $data->distance_prediction, 
						distance_measured => $data->distance_measured
					)
					
				);
			
				$return = new stdClass();
				$return->message 	= "ok";
				$return->code 		= 200;
				$return->data['data'] 		= $data;
				$app->halt(200, json_encode($return));
				$app->stop();

			} catch(PDOException $e) {
				
				$return = new stdClass();
				$return->message 	= $e->getMessage();
				$return->code		= 500;
				$app->halt(200, json_encode($return));
				$app->stop();
				
			}
		});
		
	});
	
	$app->run();
	
?>