<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//$app = new \Slim\App;

// GET Obtener los clubes por filtro o la totalidad
$app->get('/api/clubes_search/{search}/{index}', function(Request $request, Response $response, array $args){
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
    $result = 0;
		$clubs = array();

			$sql = "SELECT C.id_club, C.name_club, DATE_FORMAT(C.creation_date, '%Y-%m-%d') creation_date, DATE_FORMAT(SYSDATE(), '%Y') - DATE_FORMAT(C.creation_date, '%Y') creation_years, C.meeting_date, C.meeting_hour, C.id_region, R.description region_description, C.id_zone, Z.description zone_description
			FROM tb_clubs C
			INNER JOIN tb_region R ON C.id_region = R.id_region
			INNER JOIN tb_zone Z ON C.id_zone = Z.id_zone
			WHERE C.name_club LIKE '%$search%' OR C.id_region LIKE '%$search%' OR C.id_zone LIKE '%$search%' OR C.id_club LIKE '%$search%'
            AND C.status = 1
			LIMIT 10 OFFSET $index;";
    
    try {
        $db = new db($selecteddb);
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");
        if ($resultado = mysqli_query($link, $sql)) {
            if (mysqli_num_rows($resultado) > 0) {
                while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
                    $clubs[] = $row;
                }
                $message = 'Si hay clubs registrados';
                $result = 1;
            } else {
            $result  = 0;
            $message = 'No hay clubs registrados';
        }
        /* liberar el conjunto de resultados */
        mysqli_free_result($resultado);  
        }
        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
				$out['data'] = $clubs;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
    // Close connection
    $link->close();
});

// GET Obtener los clubes
$app->get('/api/clubes/{index}', function(Request $request, Response $response, array $args){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //
		$index = $args['index'];

		if (!isset($index)){
			$index = 0;
		}
		
    $message = '';
    $result = 0;
		$clubs = array();
		
			$sql = "SELECT C.id_club, C.name_club, DATE_FORMAT(C.creation_date, '%Y-%m-%d') creation_date, DATE_FORMAT(SYSDATE(), '%Y') - DATE_FORMAT(C.creation_date, '%Y') creation_years, C.meeting_date, C.meeting_hour, C.id_region, R.description region_description, C.id_zone, Z.description zone_description
			FROM tb_clubs C
			INNER JOIN tb_region R ON C.id_region = R.id_region
			INNER JOIN tb_zone Z ON C.id_zone = Z.id_zone
            WHERE C.status = 1
			LIMIT 10 OFFSET $index;";	
		
    
    try {
        $db = new db($selecteddb);
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");
        if ($resultado = mysqli_query($link, $sql)) {
            if (mysqli_num_rows($resultado) > 0) {
                while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
                    $clubs[] = $row;
                }
                $message = 'Si hay clubs registrados';
                $result = 1;
            } else {
            $result  = 0;
            $message = 'No hay clubs registrados';
        }
        /* liberar el conjunto de resultados */
        mysqli_free_result($resultado);  
        }
        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
				$out['data'] = $clubs;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
    // Close connection
    $link->close();
});

// GET Obtener la información del club
$app->get('/api/club/{id_club}', function(Request $request, Response $response, array $args){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //
	$id_club = $args['id_club'];
    $message = '';
    $result = 0;
    $clubs = array();
    $club_government = array();

		if (strlen($id_club) == 0) {
			$out['ok'] = 1;
			$out['result'] = 0;
			$out['message'] = "No se enviaron los datos necesarios!";
			$out['data'] = $clubs;
			echo json_encode($out, JSON_UNESCAPED_UNICODE);
			die();
		}
		
		$sql = "SELECT C.club_code, C.name_club, DATE_FORMAT(C.creation_date, '%Y-%m-%d') creation_date, DATE_FORMAT(SYSDATE(), '%Y') - DATE_FORMAT(C.creation_date, '%Y') creation_years, C.meeting_date, C.meeting_hour, C.id_region, R.description region_description, C.id_zone, Z.description zone_description
		FROM tb_clubs C
		INNER JOIN tb_region R ON C.id_region = R.id_region
		INNER JOIN tb_zone Z ON C.id_zone = Z.id_zone
		WHERE C.id_club = $id_club
        LIMIT 1;";
        
        $sql2 = "SELECT T.description cargo, CONCAT_WS(' ', M.name, M.last_name) name
        FROM tb_type_members TM
        INNER JOIN tb_type T ON TM.id_type = T.id_type
        INNER JOIN tb_members M ON TM.member_code = M.member_code
        INNER JOIN tb_clubs C ON M.club_code = C.club_code
        WHERE C.id_club = $id_club
        AND T.isClub = 1;";
    
    try {
        // $db = new db($selecteddb);
        // $db = $db->dbConnection();
        // $resultado = $db->query($sql);
        // if ($resultado->rowCount() > 0) {
        //     $clubs = $resultado->fetchAll(PDO::FETCH_OBJ);
        //     //
        //     $resultado = $db->query($sql2);
        //     if ($resultado->rowCount() > 0) {
        //         $club_government = $resultado->fetchAll(PDO::FETCH_OBJ);
        //     }
        //     $result  = 1;
        // } else {
        //     $result = 0;
        //     $message = "No se encontraron clubes con ese ID!";
        // }
        $db = new db($selecteddb);
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");
        if ($resultado = mysqli_query($link, $sql)) {
            if (mysqli_num_rows($resultado) > 0) {
                while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
                    $clubs[] = $row;
                }
                if ($resultado = mysqli_query($link, $sql2)) {
                    if (mysqli_num_rows($resultado) > 0) {
                        while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
                            $club_government[] = $row;
                        }
                    }
                }
                $message = 'Si hay clubs registrados';
                $result = 1;
            } else {
            $result  = 0;
            $message = 'No hay clubs registrados';
        }
        /* liberar el conjunto de resultados */
        mysqli_free_result($resultado);  
        }
        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
        $out['data']['info'] = $clubs;
        $out['data']['administracion'] = $club_government;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
    // Close connection
    $link->close();
});

// POST Agregar un club
$app->post('/api/club_add', function(Request $request, Response $response){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //
    $name_club = $request->getParam('name_club');
    $club_code = $request->getParam('club_code');
    $meeting_date = $request->getParam('meeting_date');
    $meeting_hour = $request->getParam('meeting_hour');
    $id_region = $request->getParam('id_region');
    $id_zone = $request->getParam('id_zone');
    date_default_timezone_set('America/Costa_Rica');
    $today = date('Y-m-d h:i:s');

    $sql = "INSERT INTO tb_clubs (id_club, name_club, club_code, creation_date, meeting_date, meeting_hour, id_region, id_zone)
    VALUES (null, '$name_club', '$club_code', '$today', '$meeting_date', '$meeting_hour', '$id_region', '$id_zone');";
 
 echo $sql;

    try {
        $db = new db($selecteddb);
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");
        if ($resultado = mysqli_query($link, $sql)) {
            $result = 1;
            $message = "Club agregado exitosamente!";
        } else {
            $result = 0;
            $message = "No ha sido posible agregar el Club!";
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

// PUT Editar un club
$app->put('/api/club_edit/{id}', function(Request $request, Response $response){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //
    $id_club = $request->getAttribute('id');
    $name_club = $request->getParam('name_club');
    $club_code = $request->getParam('club_code');
    $meeting_date = $request->getParam('meeting_date');
    $meeting_hour = $request->getParam('meeting_hour');
    $id_region = $request->getParam('id_region');
    $id_zone = $request->getParam('id_zone');

    $sql = "UPDATE tb_clubs SET 
    name_club = '$name_club', 
    club_code = '$club_code', 
    meeting_date = '$meeting_date', 
    meeting_hour = '$meeting_hour', 
    id_region = '$id_region',
    id_zone = '$id_zone'
    WHERE id_club = $id_club
    LIMIT 1";

    try {
        $db = new db($selecteddb);
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");
        if ($resultado = mysqli_query($link, $sql)) {
            $result = 1;
            $message = "Club editado exitosamente!";
        } else {
            $result = 0;
            $message = "No ha sido posible editar el Club!";
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

// PUT Editar status de un club
$app->put('/api/club_delete/{id}', function(Request $request, Response $response){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //
    $id_club = $request->getAttribute('id');
    $status = 0;

    $sql = "UPDATE tb_clubs SET 
    status = $status
    WHERE id_club = $id_club";

    try {
        $db = new db($selecteddb);
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");
        if ($resultado = mysqli_query($link, $sql)) {
            $result = 1;
            $message = "Club eliminado exitosamente!";
        } else {
            $result = 0;
            $message = "No ha sido posible eliminar el Club!";
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