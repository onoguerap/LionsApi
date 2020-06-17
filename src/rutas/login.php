<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//$app = new \Slim\App;

// GET User for member code
$app->get('/api/login', function(Request $request, Response $response){
    $member_code = $request->getParam('member_code');
    $message = '';
    $result = 0;
    $member = array();

    $sql = "SELECT * FROM tb_members WHERE member_code = $member_code ORDER BY member_code DESC LIMIT 1";
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