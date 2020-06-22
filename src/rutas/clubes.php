<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//$app = new \Slim\App;

// GET Obtener los clubes por filtro o la totalidad
$app->post('/api/clubes_search', function(Request $request, Response $response){
		$search = $request->getParam('search');
		$index = $request->getParam('index');

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
			LIMIT 10 OFFSET $index;";
    
    try {
        $db = new db();
        $db = $db->dbConnection();
        $resultado = $db->query($sql);
        if ($resultado->rowCount() > 0) {
            $clubs = $resultado->fetchAll(PDO::FETCH_OBJ);
            $result  = 1;
        } else {
            $result = 0;
            $message = "No se encontraron miembros!";
        }
        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
				$out['data'] = $clubs;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
});

// GET Obtener los clubes
$app->post('/api/clubes', function(Request $request, Response $response){
		$index = $request->getParam('index');

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
			LIMIT 10 OFFSET $index;";	
		
    
    try {
        $db = new db();
        $db = $db->dbConnection();
        $resultado = $db->query($sql);
        if ($resultado->rowCount() > 0) {
            $clubs = $resultado->fetchAll(PDO::FETCH_OBJ);
            $result  = 1;
        } else {
            $result = 0;
            $message = "No se encontraron miembros!";
        }
        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
				$out['data'] = $clubs;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
});

// GET Obtener la información del miembro
$app->post('/api/club', function(Request $request, Response $response){
		$id_club = $request->getParam('id_club');
    $message = '';
    $result = 0;
		$clubs = array();

		if (strlen($id_club) == 0) {
			$out['ok'] = 1;
			$out['result'] = 0;
			$out['message'] = "No se enviaron los datos necesarios!";
			$out['data'] = $clubs;
			echo json_encode($out, JSON_UNESCAPED_UNICODE);
			die();
		}
		
		$sql = "SELECT C.id_club, C.name_club, DATE_FORMAT(C.creation_date, '%Y-%m-%d') creation_date, DATE_FORMAT(SYSDATE(), '%Y') - DATE_FORMAT(C.creation_date, '%Y') creation_years, C.meeting_date, C.meeting_hour, C.id_region, R.description region_description, C.id_zone, Z.description zone_description
		FROM tb_clubs C
		INNER JOIN tb_region R ON C.id_region = R.id_region
		INNER JOIN tb_zone Z ON C.id_zone = Z.id_zone
		WHERE C.id_club = $id_club
		LIMIT 1;";	
    
    try {
        $db = new db();
        $db = $db->dbConnection();
        $resultado = $db->query($sql);
        if ($resultado->rowCount() > 0) {
            $clubs = $resultado->fetchAll(PDO::FETCH_OBJ);
            $result  = 1;
        } else {
            $result = 0;
            $message = "No se encontraron clubes con ese ID!";
        }
        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
				$out['data'] = $clubs;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
});