<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//$app = new \Slim\App;

// GET Obtener los miembros por filtro o la totalidad
$app->get('/api/actividades', function(Request $request, Response $response){
    $message = '';
    $activities = array();

    $sql = "SELECT * 
		FROM tb_activities 
		WHERE DATE_FORMAT(schedule, '%Y-%m-%d') >= DATE_FORMAT(SYSDATE(), '%Y-%m-%d')
		ORDER BY schedule DESC";

    try {
        $db = new db();
        $db = $db->dbConnection();
        $resultado = $db->query($sql);
        if ($resultado->rowCount() > 0) {
            $activities = $resultado->fetchAll(PDO::FETCH_OBJ);
            $result = 1;
        } else {
            $result  = 0;
            $message = 'No hay actividades registradas';
        }

        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
        $out['data'] = $activities;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
});