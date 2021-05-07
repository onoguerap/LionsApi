<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Http\UploadedFile;

$container['base_url_products'] = 'http://138.68.239.185/uploads/products/';

// GET Obtener los cortes por busqueda
$app->get('/api/product_search/{search}/{index}', function(Request $request, Response $response, array $args){

		$search = $args['search'];	
		$index = $args['index'];

		if (!isset($search)){
			$search = "";
		}
		if (!isset($index)){
			$index = 0;
		}
		
    $message = '';
    $result = 0;
		$cortes = array();

		$sql = "SELECT p.idProduct, p.name, p.description, pr.url
		FROM TB_PRODUCT p
		LEFT JOIN TB_PRODUCT_RESOURCE pr ON p.idProduct = pr.idProduct
		WHERE p.state = 1
    AND pr.view = 'home'
		AND p.name LIKE '%$search%' OR p.description LIKE '%$search%'
		LIMIT 10 OFFSET $index;";
    
    try {
        $db = new grilldb();
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");
				$base_url = $this->get('base_url_products');
        if ($resultado = mysqli_query($link, $sql)) {
            if (mysqli_num_rows($resultado) > 0) {
                while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
										$row['url'] = $base_url.'home/'.$row['url'];
                    $cortes[] = $row;
                }
                $message = 'Si hay cortes registrados';
                $result = 1;
            } else {
            $result  = 0;
            $message = 'No hay cortes registrados';
        }
        /* liberar el conjunto de resultados */
        mysqli_free_result($resultado);  
        }
        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
				$out['data'] = $cortes;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
    // Close connection
    $link->close();
});

// GET Obtener los cortes
$app->get('/api/products/{index}', function(Request $request, Response $response, array $args){

		$index = $args['index'];

		if (!isset($index)){
			$index = 0;
		}
		
    $message = '';
    $result = 0;
		$cortes = array();

		$sql = "SELECT p.idProduct, p.name, p.description, pr.url
		FROM TB_PRODUCT p
		LEFT JOIN TB_PRODUCT_RESOURCE pr ON p.idProduct = pr.idProduct
		WHERE p.state = 1
    AND pr.view = 'home'
		LIMIT 10 OFFSET $index;";
    
    try {
        $db = new grilldb();
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");
				$base_url = $this->get('base_url_products');
        if ($resultado = mysqli_query($link, $sql)) {
            if (mysqli_num_rows($resultado) > 0) {
                while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
										$row['url'] = $base_url.'home/'.$row['url'];
                    $cortes[] = $row;
                }
                $message = 'Si hay cortes registrados';
                $result = 1;
            } else {
            $result  = 0;
            $message = 'No hay cortes registrados';
        }
        /* liberar el conjunto de resultados */
        mysqli_free_result($resultado);  
        }
        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
				$out['data'] = $cortes;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
    // Close connection
    $link->close();
});

// GET Obtener los terminos
$app->get('/api/product_meat_terms/{index}', function(Request $request, Response $response, array $args){

		$index = $args['index'];

		if (!isset($index)){
			$index = 0;
		}
		
    $message = '';
    $result = 0;
		$terminos = array();

		$sql = "SELECT *
		FROM TB_PRODUCT_MEAT_TERM
		LIMIT 10 OFFSET $index;";
    
    try {
        $db = new grilldb();
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");
        if ($resultado = mysqli_query($link, $sql)) {
            if (mysqli_num_rows($resultado) > 0) {
                while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
                    $terminos[] = $row;
                }
                $message = 'Si hay terminos registrados';
                $result = 1;
            } else {
            $result  = 0;
            $message = 'No hay terminos registrados';
        }
        /* liberar el conjunto de resultados */
        mysqli_free_result($resultado);  
        }
        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
				$out['data'] = $terminos;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
    // Close connection
    $link->close();
});

// GET Obtener los grosores
$app->get('/api/product_thickness/{index}', function(Request $request, Response $response, array $args){

		$index = $args['index'];

		if (!isset($index)){
			$index = 0;
		}
		
    $message = '';
    $result = 0;
		$grosores = array();

		$sql = "SELECT *
		FROM TB_PRODUCT_THICKNESS
		LIMIT 10 OFFSET $index;";
    
    try {
        $db = new grilldb();
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");
        if ($resultado = mysqli_query($link, $sql)) {
            if (mysqli_num_rows($resultado) > 0) {
                while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
                    $grosores[] = $row;
                }
                $message = 'Si hay grosores registrados';
                $result = 1;
            } else {
            $result  = 0;
            $message = 'No hay grosores registrados';
        }
        /* liberar el conjunto de resultados */
        mysqli_free_result($resultado);  
        }
        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
				$out['data'] = $grosores;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
    // Close connection
    $link->close();
});

// GET Obtener el timer del producto final
$app->get('/api/product_timer/{idProduct}/{idProductMeatTearm}/{idProductThickness}', function(Request $request, Response $response, array $args){

		$idProduct = $args['idProduct'];
		$idProductMeatTearm = $args['idProductMeatTearm'];
		$idProductThickness = $args['idProductThickness'];

		$message = '';
    $result = 0;
		$tiempo = array();

		if (!isset($idProduct) || !isset($idProductMeatTearm) || !isset($idProductThickness)){
			$out['result'] = $result;
			$out['message'] = 'No se enviaron todos los datos necesarios';
			$out['data'] = $tiempo;
			echo json_encode($out, JSON_UNESCAPED_UNICODE);
			$link->close();
			die();
		}
	
		$sql = "SELECT DATE_FORMAT(PT.time, '%H:%i') time
		FROM TB_PRODUCT_FINAL PF
		INNER JOIN TB_PRODUCT_TIMER PT ON PF.idProductTimer = PT.idProductTimer
		WHERE PF.idProduct = '$idProduct'
		AND PF.idProductMeatTerm = '$idProductMeatTearm'
		AND PF.idProductThickness = '$idProductThickness'
		LIMIT 1";
    
    try {
        $db = new grilldb();
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");
        if ($resultado = mysqli_query($link, $sql)) {
            if (mysqli_num_rows($resultado) > 0) {
                while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
                    $tiempo[] = $row;
                }
                $message = 'Si hay tiempo registrado';
                $result = 1;
            } else {
            $result  = 0;
            $message = 'No hay tiempo registrado';
        }
        /* liberar el conjunto de resultados */
        mysqli_free_result($resultado);  
        }
        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
				$out['data'] = $tiempo;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
    // Close connection
    $link->close();
});