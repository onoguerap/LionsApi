<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//$app = new \Slim\App;

// GET Obtener las regiones por filtro
$app->get('/api/regiones_search/{search}/{index}', function(Request $request, Response $response, array $args){
	$search = $args['search'];
		$index = $args['index'];

		if (!isset($index)){
			$index = 0;
		}

		if (!isset($search)){
			$search = "";
		}
    $message = '';
    $regiones = array();

    $sql = "SELECT * 
		FROM tb_region 
		WHERE id_region LIKE '%$search%'
		AND status = 1
		ORDER BY id_region ASC
		LIMIT 10 OFFSET $index;";

    try {
        $db = new db();
        $db = $db->dbConnection();
        $resultado = $db->query($sql);
        if ($resultado->rowCount() > 0) {
            $regiones = $resultado->fetchAll(PDO::FETCH_OBJ);
            $result = 1;
        } else {
            $result  = 0;
            $message = 'No hay regiones registradas';
        }

        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
        $out['data'] = $regiones;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
});
// GET Obtener las regiones
$app->get('/api/regiones/{index}', function(Request $request, Response $response, array $args){
		$index = $args['index'];

		if (!isset($index)){
			$index = 0;
		}

    $message = '';
    $regiones = array();

    $sql = "SELECT * 
		FROM tb_region 
		WHERE status = 1
		ORDER BY id_region ASC
		LIMIT 10 OFFSET $index;";

    try {
        $db = new db();
        $db = $db->dbConnection();
        $resultado = $db->query($sql);
        if ($resultado->rowCount() > 0) {
            $regiones = $resultado->fetchAll(PDO::FETCH_OBJ);
            $result = 1;
        } else {
            $result  = 0;
            $message = 'No hay regiones registradas';
        }

        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
        $out['data'] = $regiones;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
});