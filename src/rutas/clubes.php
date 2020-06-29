<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//$app = new \Slim\App;

// GET Obtener los clubes por filtro o la totalidad
$app->get('/api/clubes_search/{search}/{index}', function(Request $request, Response $response, array $args){
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
$app->get('/api/clubes/{index}', function(Request $request, Response $response, array $args){
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

// GET Obtener la información del club
$app->get('/api/club/{id_club}', function(Request $request, Response $response, array $args){
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
        $db = new db();
        $db = $db->dbConnection();
        $resultado = $db->query($sql);
        if ($resultado->rowCount() > 0) {
            $clubs = $resultado->fetchAll(PDO::FETCH_OBJ);
            //
            $resultado = $db->query($sql2);
            if ($resultado->rowCount() > 0) {
                $club_government = $resultado->fetchAll(PDO::FETCH_OBJ);
            }
            $result  = 1;
        } else {
            $result = 0;
            $message = "No se encontraron clubes con ese ID!";
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
});