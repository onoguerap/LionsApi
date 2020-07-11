<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//$app = new \Slim\App;
// GET User for member code
$app->get('/api/login/{member_code}/{password}', function(Request $request, Response $response, array $args)  {
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //
		$member_code = $args['member_code'];
        $password = $args['password'];
        $message = '';
        $result = 0;
		$member = array();
		
        $sql = "SELECT * 
        FROM tb_members 
        WHERE member_code = $member_code 
        AND password = '$password'
        AND status = 1
        ORDER BY member_code DESC LIMIT 1";
    
    try {
        $db = new db($selecteddb);
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");
        $directory = $this->get('base_url_members');

        if ($resultado = mysqli_query($link, $sql)) {
            if (mysqli_num_rows($resultado) > 0) {
                $row = mysqli_fetch_array($resultado, MYSQLI_ASSOC);
                $member = $row;
                $member['img_url'] = $directory.''.$member['img_url'];
                $sql = "SELECT *
                FROM tb_countries
                WHERE id_country = $selecteddb";

                if ($resultado = mysqli_query($link, $sql)) {
                    $row = mysqli_fetch_array($resultado, MYSQLI_ASSOC);
                    $country = $row;
                    $message = 'Usuario Logueado';
                    $result = 1;
                }
            } else {
            $result  = 0;
            $message = 'Usuario NO Logueado';
        }
        /* liberar el conjunto de resultados */
        mysqli_free_result($resultado);  
        }

        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
        $out['data']['member'] = $member;
        $out['data']['country'] = $country;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
});

