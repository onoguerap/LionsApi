<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Http\UploadedFile;

// GET Obtener los roles
$app->get('/api/roles', function(Request $request, Response $response, array $args){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //

    $message = '';
    $roles = array();

    $sql = "SELECT * 
    FROM tb_rol";
    
    try {
        
        $db = new db($selecteddb);
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");
        if ($resultado = mysqli_query($link, $sql)) {
            if (mysqli_num_rows($resultado) > 0) {
                while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
                    $roles[] = $row;
                }
                $message = 'Si hay roles registradas';
                $result = 1;
            } else {
            $result  = 0;
            $message = 'No hay roles registradas';
        }
        /* liberar el conjunto de resultados */
        mysqli_free_result($resultado);  
        }

        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
        $out['data'] = $roles;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
    // Close connection
    $link->close();   
});