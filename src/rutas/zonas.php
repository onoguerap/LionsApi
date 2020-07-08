<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//$app = new \Slim\App;

// GET Obtener las zonas por filtro
$app->get('/api/zonas_search/{search}/{index}', function(Request $request, Response $response, array $args){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //
		$search = $args['search'];
		$index = $args['index'];

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
        $db = new db($selecteddb);
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
$app->get('/api/zonas/{index}', function(Request $request, Response $response, array $args){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //
		$index = $args['index'];

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
        $db = new db($selecteddb);
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
$app->get('/api/zonas_region/{id_region}', function(Request $request, Response $response, array $args){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //
		$id_region = $args['id_region'];
    $message = '';
    $zonas = array();

		if(strlen($id_region) > 0 && $id_region != ''){
			$sql = "SELECT * FROM tb_zone WHERE id_region = '$id_region' ORDER BY id_zone ASC";
		} else {
			$sql = "SELECT * FROM tb_zone ORDER BY id_zone ASC";
		}
    
    try {
        $db = new db($selecteddb);
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

// POST Agregar una zona
$app->post('/api/zona_add', function(Request $request, Response $response){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //
    $description = $request->getParam('description');
    $id_region = $request->getParam('id_region');
    

    $sql = "INSERT INTO tb_zone (id_zone, description, id_region)
    VALUES (:id_zone, :description, :id_region);";

    try {
        $db = new db($selecteddb);
        $db = $db->dbConnection();
        $resultado = $db->prepare($sql);

        $resultado->bindParam(':id_zone', $description);
        $resultado->bindParam(':description', $description);
        $resultado->bindParam(':id_region', $id_region);

        if ($resultado->execute()) {
            $result = 1;
            $message = "Zona Agregada Exitosamente!";
        } else {
            $result = 0;
            $message = "No ha sido posible agregar la zona!";
        }
        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
});

// PUT Editar una zona
$app->put('/api/zona_edit/{id}', function(Request $request, Response $response){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //
    $id_zone = $request->getAttribute('id');
    $description = $request->getParam('description');
    $id_region = $request->getParam('id_region');

    $sql = "UPDATE tb_zone SET 
    description = :description
    ,id_region = :id_region
    WHERE id_zone = '$id_zone'
    LIMIT 1";

    try {
        $db = new db($selecteddb);
        $db = $db->dbConnection();
        $resultado = $db->prepare($sql);

        $resultado->bindParam(':description', $description);
        $resultado->bindParam(':id_region', $id_region);

        if ($resultado->execute()) {
            $result = 1;
            $message = "Zona Editada Exitosamente!";
        } else {
            $result = 0;
            $message = "No ha sido posible editar la zona!";
        }
        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
});

// PUT Editar status de una zona
$app->put('/api/zona_delete/{id}', function(Request $request, Response $response){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //
    $id_zone = $request->getAttribute('id');
    $status = 0;

    $sql = "UPDATE tb_zone SET 
    status = :status
    WHERE id_zone = '$id_zone'
    LIMIT 1";

    try {
        $db = new db($selecteddb);
        $db = $db->dbConnection();
        $resultado = $db->prepare($sql);

        $resultado->bindParam(':status',$status);

        if ($resultado->execute()) {
            $result = 1;
            $message = "Region Eliminada Exitosamente!";
        } else {
            $result = 0;
            $message = "No ha sido posible eliminar el region!";
        }
        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
});