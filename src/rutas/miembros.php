<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//$app = new \Slim\App;

// GET Obtener los miembros por filtro o la totalidad
$app->get('/api/miembros', function(Request $request, Response $response){
		$search = $request->getParam('search');
		$lastSearch = $request->getParam('lastSearch');
		$limit = $request->getParam('limit');

		if ($search == $lastSearch){
			$limit = $limit + 10;
		} else {
			$limit = 10;
		}
		
    $message = '';
    $result = 0;
		$members = array();
		if (strlen($search) > 0 && $search != '') {
			$sql = "SELECT M.*, C.name_club
			FROM tb_members M
			INNER JOIN tb_clubs C ON M.club_code = C.club_code
			WHERE M.name LIKE '%$search%' OR M.last_name LIKE '%$search%' OR M.cellphone LIKE '%$search%' OR C.name_club LIKE '%$search%'
			AND M.status = 1
			LIMIT $limit;";
		} else {
			$sql = "SELECT M.*, C.name_club 
			FROM tb_members M
			INNER JOIN tb_clubs C ON M.club_code = C.club_code
			WHERE M.status = 1 LIMIT $limit;";	
		}
    
    try {
        $db = new db();
        $db = $db->dbConnection();
        $resultado = $db->query($sql);
        if ($resultado->rowCount() > 0) {
            $members = $resultado->fetchAll(PDO::FETCH_OBJ);
            $result  = 1;
        } else {
            $result = 0;
            $message = "No se encontraron miembros!";
        }
        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
				$out['data'] = $members;
				$out['lastSearch'] = $search;
				$out['limit'] = $limit;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
});

// GET Obtener la información del miembro
$app->get('/api/miembro', function(Request $request, Response $response){
		$id_member = $request->getParam('id_member');
    $message = '';
    $result = 0;
		$members = array();

		if (strlen($id_member) == 0) {
			$out['ok'] = 1;
			$out['result'] = 0;
			$out['message'] = "No se enviaron los datos necesarios!";
			$out['data'] = $members;
			echo json_encode($out, JSON_UNESCAPED_UNICODE);
			die();
		}
		
		$sql = "SELECT M.*, C.name_club, T.description type_member, R.description rol_member
		FROM tb_members M
		INNER JOIN tb_clubs C ON M.club_code = C.club_code
		INNER JOIN tb_type_members T ON M.id_type_member = T.id_type_member
    INNER JOIN tb_rol R ON M.id_rol_member = R.id_rol_member
		WHERE M.status = 1
		AND M.id_member = $id_member
		LIMIT 1;";	
    
    try {
        $db = new db();
        $db = $db->dbConnection();
        $resultado = $db->query($sql);
        if ($resultado->rowCount() > 0) {
            $members = $resultado->fetchAll(PDO::FETCH_OBJ);
            $result  = 1;
        } else {
            $result = 0;
            $message = "No se encontraron miembros con ese ID!";
        }
        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
				$out['data'] = $members;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
});

// GET Obtener los miembros por filtro o la totalidad
$app->get('/api/birthdays', function(Request $request, Response $response){
		
    $message = '';
    $result = 0;
		$members = array();
		
		$sql = "SELECT CONCAT_WS(' ', name, last_name) fullname, DATE_FORMAT(birthday, '%d/%m') min_date, IF(DATE_FORMAT(birthday, '%d') = DATE_FORMAT(SYSDATE(), '%d'), 1, 0) is_today
		FROM tb_members
		WHERE DATE_FORMAT(birthday, '%m') = DATE_FORMAT(SYSDATE(), '%m')
		AND DATE_FORMAT(birthday, '%d') >= DATE_FORMAT(SYSDATE(), '%d')
		ORDER BY DATE_FORMAT(birthday, '%d/%m') ASC;";	
    
    try {
        $db = new db();
        $db = $db->dbConnection();
        $resultado = $db->query($sql);
        if ($resultado->rowCount() > 0) {
            $members = $resultado->fetchAll(PDO::FETCH_OBJ);
            $result  = 1;
        } else {
            $result = 0;
            $message = "No se encontraron cumpleañeros!";
        }
        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
				$out['data'] = $members;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
});