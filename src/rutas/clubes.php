<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//$app = new \Slim\App;

// GET Todos los clubes
$app->get('/api/clubes', function(Request $request, Response $response){
    $sql = "SELECT * FROM tb_clubs";
    try {
        $db = new db();
        $db = $db->dbConnection();
        $resultado = $db->query($sql);
        if ($resultado->rowCount() > 0) {
            $clubs = $resultado->fetchAll(PDO::FETCH_OBJ);
            echo json_encode($clubs);
        } else {
            echo json_encode("No hay clubes para mostrar!");
        }
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
});