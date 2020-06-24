<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//$app = new \Slim\App;

// GET User for member code
$app->get('/api/login/{member_code}/{password}', function(Request $request, Response $response, array $args){
		$member_code = $args['member_code'];
		$password = $args['password'];
    $message = '';
    $result = 0;
		$member = array();
		
		if ($password != 0) {
			$sql = "SELECT * 
			FROM tb_members 
			WHERE member_code = $member_code 
			AND password = $password
			AND id_rol_member = 1 OR id_rol_member = 2
			ORDER BY member_code DESC LIMIT 1";
		} else {
			$sql = "SELECT * 
			FROM tb_members 
			WHERE member_code = $member_code 
			AND id_rol_member = 3
			ORDER BY member_code DESC LIMIT 1";
		}

    
    try {
        $db = new db();
        $db = $db->dbConnection();
        $resultado = $db->query($sql);
        if ($resultado->rowCount() > 0) {
            $member = $resultado->fetchAll(PDO::FETCH_OBJ);
            $result = 1; 
        } else {
            $result  = 0;
            $message = 'El número de miembro es Inválido';
        }

        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
        $out['data'] = $member;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
});