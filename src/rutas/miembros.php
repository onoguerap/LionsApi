<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//$app = new \Slim\App;

// GET Obtener los miembros por filtro
$app->post('/api/miembros_search', function(Request $request, Response $response){
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
		$members = array();
	
			$sql = "SELECT M.*, C.name_club 
			FROM tb_members M
			INNER JOIN tb_clubs C ON M.club_code = C.club_code
			WHERE M.name LIKE '%$search%' OR M.last_name LIKE '%$search%' OR M.cellphone LIKE '%$search%' OR C.name_club LIKE '%$search%'
			AND M.status = 1
			LIMIT 10 OFFSET $index;";	
    
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
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
});

// GET Obtener los miembros
$app->post('/api/miembros', function(Request $request, Response $response){
		$index = $request->getParam('index');

		if (!isset($index)){
			$index = 0;
		}
		
    $message = '';
    $result = 0;
		$members = array();
	
			$sql = "SELECT M.*, C.name_club 
			FROM tb_members M
			INNER JOIN tb_clubs C ON M.club_code = C.club_code
			WHERE M.status = 1 LIMIT 10 OFFSET $index;";	
    
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
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
});

// GET Obtener la información del miembro
$app->post('/api/miembro', function(Request $request, Response $response){
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
		
		$sql = "SELECT M.*, C.name_club
    , GROUP_CONCAT(T.description)  type_member
    , R.description rol_member
		FROM tb_members M
		INNER JOIN tb_clubs C ON M.club_code = C.club_code
		INNER JOIN tb_type_members TM ON M.member_code = TM.member_code
		INNER JOIN tb_type T ON TM.id_type = T.id_type
		INNER JOIN tb_rol R ON M.id_rol_member = R.id_rol_member
		WHERE M.status = 1
		AND M.id_member = 911
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
$app->post('/api/birthdays', function(Request $request, Response $response){
		
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

// GET Obtener los miembros de la Gobernacion
$app->post('/api/gobernacion', function(Request $request, Response $response){
		
    $message = '';
    $result = 0;
		$members = array();
		
		$sql = "SELECT CONCAT_WS(' ', M.name, M.last_name) fullname, T.description
		FROM tb_members M
		INNER JOIN tb_type_members TM ON M.member_code = TM.member_code
		INNER JOIN tb_type T ON TM.id_type = T.id_type
		WHERE T.isGovernment = 1
		ORDER BY T.id_type ASC;";	
    
    try {
        $db = new db();
        $db = $db->dbConnection();
        $resultado = $db->query($sql);
        if ($resultado->rowCount() > 0) {
            $members = $resultado->fetchAll(PDO::FETCH_OBJ);
            $result  = 1;
        } else {
            $result = 0;
            $message = "No se encontraron miembros de gobernación!";
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

// GET Obtener los miembros Jefes de Zona
$app->post('/api/jefes_region', function(Request $request, Response $response){
		
    $message = '';
    $result = 0;
		$members = array();
		
		$sql = "SELECT CONCAT_WS(' ', M.name, M.last_name) fullname, IF(T.id_type = 10, CONCAT_WS(' - ', T.description, SUBSTRING(M.id_zone,1,1)) ,CONCAT_WS(' - ', T.description, M.id_zone)) description, M.img_url
		FROM tb_members M
		INNER JOIN tb_type_members TM ON M.member_code = TM.member_code
		INNER JOIN tb_type T ON TM.id_type = T.id_type
		WHERE T.id_type = 10
		ORDER BY SUBSTRING(M.id_zone,1,1) ASC";	
    
    try {
        $db = new db();
        $db = $db->dbConnection();
        $resultado = $db->query($sql);
        if ($resultado->rowCount() > 0) {
            $members = $resultado->fetchAll(PDO::FETCH_OBJ);
            $result  = 1;
        } else {
            $result = 0;
            $message = "No se encontraron miembros de gobernación!";
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

// GET Obtener los miembros Jefes de Zona
$app->post('/api/jefes_zona', function(Request $request, Response $response){
		
    $message = '';
    $result = 0;
		$members = array();
		
		$sql = "SELECT CONCAT_WS(' ', M.name, M.last_name) fullname, IF(T.id_type = 10, CONCAT_WS(' - ', T.description, SUBSTRING(M.id_zone,1,1)) ,CONCAT_WS(' - ', T.description, M.id_zone)) description, M.img_url
		FROM tb_members M
		INNER JOIN tb_type_members TM ON M.member_code = TM.member_code
		INNER JOIN tb_type T ON TM.id_type = T.id_type
		WHERE T.id_type = 11
		ORDER BY M.id_zone ASC";	
    
    try {
        $db = new db();
        $db = $db->dbConnection();
        $resultado = $db->query($sql);
        if ($resultado->rowCount() > 0) {
            $members = $resultado->fetchAll(PDO::FETCH_OBJ);
            $result  = 1;
        } else {
            $result = 0;
            $message = "No se encontraron miembros de gobernación!";
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

// GET Obtener los miembros Asesores
$app->post('/api/asesores', function(Request $request, Response $response){
		
    $message = '';
    $result = 0;
		$members = array();
		
		$sql = "SELECT CONCAT_WS(' ', M.name, M.last_name) fullname, T.description, TM.info, M.img_url
		FROM tb_members M
		INNER JOIN tb_type_members TM ON M.member_code = TM.member_code
		INNER JOIN tb_type T ON TM.id_type = T.id_type
		WHERE T.id_type = 12;";	
    
    try {
        $db = new db();
        $db = $db->dbConnection();
        $resultado = $db->query($sql);
        if ($resultado->rowCount() > 0) {
            $members = $resultado->fetchAll(PDO::FETCH_OBJ);
            $result  = 1;
        } else {
            $result = 0;
            $message = "No se encontraron miembros de gobernación!";
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

//POST Insertar Miembro
$app->post('/api/agregar_miembro', function(Request $request, Response $response){
		$name = $request->getParam('name');
		$last_name = $request->getParam('last_name');
		$birthday = $request->getParam('birthday');
		$member_code = $request->getParam('member_code');
		$club_code = $request->getParam('club_code');
		$email = $request->getParam('email');
		$phone = $request->getParam('phone');
		$cellphone = $request->getParam('cellphone');
		$id_rol_member = $request->getParam('id_rol_member');
		$gender = $request->getParam('gender');
		$id_zone = $request->getParam('id_zone');
		$id_type_member = $request->getParam('id_type_member');
		$admission_date = date('Y-m-d h:i:s');

    $message = '';
    $result = 0;
		
		
		$sql = "INSERT INTO tb_members (id_member, name, last_name, birthday, member_code, club_code
		, email, phone, cellphone, id_rol_member, gender, admission_date, id_zone, last_view, status, id_type_member, img_url) 
		VALUES (:id_member, :name, :last_name, :birthday, :member_code, :club_code, :email, :phone, :cellphone, :id_rol_member, :gender
		, :admission_date, :id_zone, :last_view, :status, :id_type_member, img_url);";	
    
    try {
        $db = new db();
        $db = $db->dbConnection();
				$resultado = $db->prepare($sql);
				
				$resultado->bindParam(':id_member', null);
				$resultado->bindParam(':name', $name);
				$resultado->bindParam(':last_name', $last_name);
				$resultado->bindParam(':birthday', $birthday);
				$resultado->bindParam(':member_code', $member_code);
				$resultado->bindParam(':club_code', $club_code);
				$resultado->bindParam(':email', $email);
				$resultado->bindParam(':phone', $phone);
				$resultado->bindParam(':cellphone', $cellphone);
				$resultado->bindParam(':id_rol_member', $id_rol_member);
				$resultado->bindParam(':gender', $gender);
				$resultado->bindParam(':admission_date', $admission_date);
				$resultado->bindParam(':id_zone', $id_zone);
				$resultado->bindParam(':last_view', null);
				$resultado->bindParam(':status', null);
				$resultado->bindParam(':id_type_member', $id_type_member);
				$resultado->bindParam(':img_url', null);
			
				//uncompleted

        if ($resultado->rowCount() > 0) {
            $members = $resultado->fetchAll(PDO::FETCH_OBJ);
            $result  = 1;
        } else {
            $result = 0;
            $message = "No se encontraron miembros de gobernación!";
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