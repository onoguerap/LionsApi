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
        $country = array();
        date_default_timezone_set('America/Costa_Rica');
        $today = date('Y-m-d h:i:s');
		
        $sql = "SELECT M.*, C.name_club
        , GROUP_CONCAT(T.description)  type_member
        , GROUP_CONCAT(T.id_type)  type_member_id
        , R.description rol_member
        , R.id_rol_member
        FROM tb_members M
        INNER JOIN tb_clubs C ON M.club_code = C.club_code
        INNER JOIN tb_type_members TM ON M.member_code = TM.member_code
        INNER JOIN tb_type T ON TM.id_type = T.id_type
        INNER JOIN tb_rol R ON M.id_rol_member = R.id_rol_member 
        WHERE M.member_code = '$member_code' 
        AND M.password = '$password'
        AND M.status = 1
        GROUP BY id_member, name_club
        ORDER BY M.member_code DESC LIMIT 1";
    
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
                    $message = 'Código aceptado';
                    $result = 1;

                    // Update last login member
                    $sql = "UPDATE tb_members
                    SET last_view = '$today
                    WHERE member_code = '$member_code'
                    LIMIT 1";
                    mysqli_query($link, $sql);
                }
            } else {
            $result  = 0;
            $message = 'Código de miembro inválido';
        }
        /* liberar el conjunto de resultados */
        mysqli_free_result($resultado);  
        }

        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
        $out['data']['member'] = $member;
        $out['data']['country'] = $country;
        $out['data']['appversion'] = 'v1.1';
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
});

