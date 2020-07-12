<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//$app = new \Slim\App;

// GET Obtener las regiones por filtro
$app->get('/api/regiones_search/{search}/{index}', function(Request $request, Response $response, array $args){
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
    $regiones = array();

    $sql = "SELECT * 
		FROM tb_region 
		WHERE id_region LIKE '%$search%'
		AND status = 1
		ORDER BY id_region ASC
		LIMIT 10 OFFSET $index;";

    try {
        $db = new db($selecteddb);
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");
        if ($resultado = mysqli_query($link, $sql)) {
            if (mysqli_num_rows($resultado) > 0) {
                while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
                    $regiones[] = $row;
                }
                $message = 'Si hay regiones registradas';
                $result = 1;
            } else {
            $result  = 0;
            $message = 'No hay regiones registradas';
        }
        /* liberar el conjunto de resultados */
        mysqli_free_result($resultado);  
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
// GET Obtener las regiones con offset
$app->get('/api/regiones/{index}', function(Request $request, Response $response, array $args){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //
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
        $db = new db($selecteddb);
        $link = $db->dbConnection();
        if ($resultado = mysqli_query($link, $sql)) {
            if (mysqli_num_rows($resultado) > 0) {
                while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
                    $regiones[] = $row;
                }
                $message = 'Si hay regiones registradas';
                $result = 1;
            } else {
                $result  = 0;
                $message = 'No hay regiones registradas';
            }
            /* liberar el conjunto de resultados */
            mysqli_free_result($resultado);
        }

        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
        $out['data'] = $regiones;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
    // Close connection
    $link->close();
});
// GET Obtener la region para edicion
$app->get('/api/region/{id_region}', function(Request $request, Response $response, array $args){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //
    $id_region = $args['id_region'];

    if (!isset($id_region)){
        $id_region = 0;
    }

    $message = '';
    $regiones = array();

    $sql = "SELECT * 
		FROM tb_region 
		WHERE status = 1
		AND id_region = '$id_region'
		LIMIT 1;";

    try {
        $db = new db($selecteddb);
        $link = $db->dbConnection();
        if ($resultado = mysqli_query($link, $sql)) {
            if (mysqli_num_rows($resultado) > 0) {
                while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
                    $regiones[] = $row;
                }
                $message = 'Si hay regiones registradas';
                $result = 1;
            } else {
                $result  = 0;
                $message = 'No hay regiones registradas';
            }
            /* liberar el conjunto de resultados */
            mysqli_free_result($resultado);
        }

        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
        $out['data'] = $regiones;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
    // Close connection
    $link->close();
});
// GET Obtener las todas las regiones
$app->get('/api/todas_regiones', function(Request $request, Response $response, array $args){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));

    $message = '';
    $regiones = array();

    $sql = "SELECT * 
		FROM tb_region 
		WHERE status = 1
		ORDER BY id_region ASC;";

    try {
        $db = new db($selecteddb);
        $link = $db->dbConnection();
        if ($resultado = mysqli_query($link, $sql)) {
            if (mysqli_num_rows($resultado) > 0) {
                while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
                    $regiones[] = $row;
                }
                $message = 'Si hay regiones registradas';
                $result = 1;
            } else {
                $result  = 0;
                $message = 'No hay regiones registradas';
            }
            /* liberar el conjunto de resultados */
            mysqli_free_result($resultado);
        }

        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
        $out['data'] = $regiones;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
    // Close connection
    $link->close();
});
// POST Agregar una region
$app->post('/api/region_add', function(Request $request, Response $response){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //
    $description = $request->getParam('description');

    $sql = "INSERT INTO tb_region (id_region, description)
    VALUES ('$description', '$description');";

    try {
        $db = new db($selecteddb);
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");
        if ($resultado = mysqli_query($link, $sql)) {
            $result = 1;
            $message = "Region agregada exitosamente!";
        } else {
            $result = 0;
            $message = "No ha sido posible agregar la region!";
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

// PUT Editar una region
$app->put('/api/region_edit/{id}', function(Request $request, Response $response){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //
    $id_region = $request->getAttribute('id');
    $description = $request->getParam('description');

    $sql = "UPDATE tb_region SET 
    description = '$description'
    WHERE id_region = '$id_region'
    LIMIT 1";

    try {
        $db = new db($selecteddb);
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");
        if ($resultado = mysqli_query($link, $sql)) {
            $result = 1;
            $message = "Region editada exitosamente!";
        } else {
            $result = 0;
            $message = "No ha sido posible editar la region!";
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

// PUT Editar status de una region
$app->put('/api/region_delete/{id}', function(Request $request, Response $response){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //
    $id_region = $request->getAttribute('id');
    $status = 0;

    $sql = "UPDATE tb_region SET 
    status = $status
    WHERE id_region = '$id_region'
    LIMIT 1";

    try {
        $db = new db($selecteddb);
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");
        if ($resultado = mysqli_query($link, $sql)) {
            $result = 1;
            $message = "Region eliminada exitosamente!";
        } else {
            $result = 0;
            $message = "No ha sido posible eliminar la region!";
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