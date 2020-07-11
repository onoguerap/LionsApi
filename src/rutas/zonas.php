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
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");
        if ($resultado = mysqli_query($link, $sql)) {
            if (mysqli_num_rows($resultado) > 0) {
                while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
                    $zonas[] = $row;
                }
                $message = 'Si hay zonas registradas';
                $result = 1;
            } else {
            $result  = 0;
            $message = 'No hay zonas registradas';
        }
        /* liberar el conjunto de resultados */
        mysqli_free_result($resultado);  
        }

        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
        $out['data'] = $zonas;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
    // Close connection
    $link->close(); 
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
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");
        if ($resultado = mysqli_query($link, $sql)) {
            if (mysqli_num_rows($resultado) > 0) {
                while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
                    $zonas[] = $row;
                }
                $message = 'Si hay zonas registradas';
                $result = 1;
            } else {
            $result  = 0;
            $message = 'No hay zonas registradas';
        }
        /* liberar el conjunto de resultados */
        mysqli_free_result($resultado);  
        }

        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
        $out['data'] = $zonas;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
    // Close connection
    $link->close();   
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
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");
        if ($resultado = mysqli_query($link, $sql)) {
            if (mysqli_num_rows($resultado) > 0) {
                while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
                    $zonas[] = $row;
                }
                $message = 'Si hay zonas registradas';
                $result = 1;
            } else {
            $result  = 0;
            $message = 'No hay zonas registradas';
        }
        /* liberar el conjunto de resultados */
        mysqli_free_result($resultado);  
        }

        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
        $out['data'] = $zonas;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
    // Close connection
    $link->close(); 
});

// POST Agregar una zona
$app->post('/api/zona_add', function(Request $request, Response $response){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //
    $description = $request->getParam('description');
    $id_region = $request->getParam('id_region');
    

    $sql = "INSERT INTO tb_zone (id_zone, description, id_region)
    VALUES ('$description', '$description', '$id_region');";

    try {
        $db = new db($selecteddb);
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");
        if ($resultado = mysqli_query($link, $sql)) {
            $result = 1;
            $message = "Zona agregada exitosamente!";
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
    // Close connection
    $link->close(); 
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
    description = '$description'
    ,id_region = '$id_region'
    WHERE id_zone = '$id_zone'
    LIMIT 1";

    try {
        $db = new db($selecteddb);
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");
        if ($resultado = mysqli_query($link, $sql)) {
            $result = 1;
            $message = "Zona editada exitosamente!";
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
    // Close connection
    $link->close(); 
});

// PUT Editar status de una zona
$app->put('/api/zona_delete/{id}', function(Request $request, Response $response){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //
    $id_zone = $request->getAttribute('id');
    $status = 0;

    $sql = "UPDATE tb_zone SET 
    status = $status
    WHERE id_zone = '$id_zone'
    LIMIT 1";

    try {
        $db = new db($selecteddb);
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");
        if ($resultado = mysqli_query($link, $sql)) {
            $result = 1;
            $message = "Zona eliminada exitosamente!";
        } else {
            $result = 0;
            $message = "No ha sido posible eliminar la zona!";
        }
        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
    // Close connection
    $link->close(); 
});