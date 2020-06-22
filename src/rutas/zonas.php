<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//$app = new \Slim\App;

// GET Obtener las zonas por filtro
$app->get('/api/zonas_search', function(Request $request, Response $response){
		$search = $request->getParam('search');
		$index = $request->getParam('index');

		if (!isset($index)){
			$index = 0;
		}

		if (!isset($search)){
			$search = "";
		}
    $message = '';
    $zonas = array();

		
			$sql = "SELECT * 
			FROM tb_zone 
			WHERE id_zone LIKE '%$search%' OR id_region LIKE '%$search%'
			AND status = 1
			ORDER BY id_zone ASC
			LIMIT 10 OFFSET $index;";
    
    try {
        $db = new db();
        $db = $db->dbConnection();
        $resultado = $db->query($sql);
        if ($resultado->rowCount() > 0) {
            $zonas = $resultado->fetchAll(PDO::FETCH_OBJ);
            $result = 1;
        } else {
            $result  = 0;
            $message = 'No hay zonas registradas';
        }

        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
        $out['data'] = $zonas;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
});

// GET Obtener las zonas por filtro
$app->post('/api/zonas', function(Request $request, Response $response){
		$index = $request->getParam('index');

		if (!isset($index)){
			$index = 0;
		}

    $message = '';
    $zonas = array();

			$sql = "SELECT * 
			FROM tb_zone 
			WHERE status = 1
			ORDER BY id_zone ASC
			LIMIT 10 OFFSET $index;";
    
    try {
        $db = new db();
        $db = $db->dbConnection();
        $resultado = $db->query($sql);
        if ($resultado->rowCount() > 0) {
            $zonas = $resultado->fetchAll(PDO::FETCH_OBJ);
            $result = 1;
        } else {
            $result  = 0;
            $message = 'No hay zonas registradas';
        }

        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
        $out['data'] = $zonas;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
});

// GET Obtener las zonas por region
$app->post('/api/zonas_region', function(Request $request, Response $response){
		$id_region = $request->getParam('id_region');
    $message = '';
    $zonas = array();

		if(strlen($id_region) > 0 && $id_region != ''){
			$sql = "SELECT * FROM tb_zone WHERE id_region = '$id_region' ORDER BY id_zone ASC";
		} else {
			$sql = "SELECT * FROM tb_zone ORDER BY id_zone ASC";
		}
    
    try {
        $db = new db();
        $db = $db->dbConnection();
        $resultado = $db->query($sql);
        if ($resultado->rowCount() > 0) {
            $zonas = $resultado->fetchAll(PDO::FETCH_OBJ);
            $result = 1;
        } else {
            $result  = 0;
            $message = 'No hay zonas registradas';
        }

        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
        $out['data'] = $zonas;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
});