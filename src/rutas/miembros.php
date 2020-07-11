<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Http\UploadedFile;

//$app = new \Slim\App;

$container = $app->getContainer();
$container['upload_directory_members'] = __DIR__ . '/uploads/miembros';
$container['base_url_members'] = 'http://138.68.239.185/uploads/miembros/';

// GET Obtener los miembros por filtro
$app->get('/api/miembros_search/{search}/{index}', function(Request $request, Response $response, array $args){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //
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
	$members = array();
	
			$sql = "SELECT M.*, C.name_club 
			FROM tb_members M
			INNER JOIN tb_clubs C ON M.club_code = C.club_code
			WHERE M.name LIKE '%$search%' OR M.last_name LIKE '%$search%' OR M.cellphone LIKE '%$search%' OR C.name_club LIKE '%$search%'
			AND M.status = 1
			LIMIT 10 OFFSET $index;";	
    
    try {
        $db = new db($selecteddb);
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");
        if ($resultado = mysqli_query($link, $sql)) {
            if (mysqli_num_rows($resultado) > 0) {
                while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
                    $members[] = $row;
                }
                $message = 'Si hay zonas registradas';
                $result = 1;
            } else {
            $result  = 0;
            $message = 'No hay zonas registradas';
        }
        /* liberar el conjunto de resultados */
        mysqli_free_result($resultado);  
        }
        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
		$out['data'] = $members;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
    // Close connection
    $link->close();
});

// GET Obtener los miembros
$app->get('/api/miembros/{index}', function(Request $request, Response $response, array $args){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //
		$index = $args['index'];

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
        $db = new db($selecteddb);
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");
        if ($resultado = mysqli_query($link, $sql)) {
            if (mysqli_num_rows($resultado) > 0) {
                while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
                    $members[] = $row;
                }
                $message = 'Si hay miembros registrados';
                $result = 1;
            } else {
            $result  = 0;
            $message = 'No hay miembros registrados';
        }
        /* liberar el conjunto de resultados */
        mysqli_free_result($resultado);  
        }
        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
				$out['data'] = $members;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
    // Close connection
    $link->close();
});

// GET Obtener la información del miembro
$app->get('/api/miembro/{id_member}', function(Request $request, Response $response, array $args){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //
	$id_member = $args['id_member'];
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
		AND M.id_member = $id_member
        GROUP BY name_club
		LIMIT 1;";	
    
    try {
        $db = new db($selecteddb);
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");
        $base_url = $this->get('base_url_members');
        if ($resultado = mysqli_query($link, $sql)) {
            if (mysqli_num_rows($resultado) > 0) {
                while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
                    $row['img_url'] = $base_url.''.$row['img_url'];
                    $members[] = $row;
                }
                $message = 'Si hay miembros registrados';
                $result = 1;
            } else {
            $result  = 0;
            $message = 'No hay miembros registrados';
        }
        /* liberar el conjunto de resultados */
        mysqli_free_result($resultado);  
        }
        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
		$out['data'] = $members;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
    // Close connection
    $link->close();
});

// GET Obtener los miembros por filtro o la totalidad
$app->get('/api/birthdays', function(Request $request, Response $response){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //
		
    $message = '';
    $result = 0;
		$members = array();
		
		$sql = "SELECT CONCAT_WS(' ', name, last_name) fullname, DATE_FORMAT(birthday, '%d/%m') min_date, IF(DATE_FORMAT(birthday, '%d') = DATE_FORMAT(SYSDATE(), '%d'), 1, 0) is_today
		FROM tb_members
		WHERE DATE_FORMAT(birthday, '%m') = DATE_FORMAT(SYSDATE(), '%m')
		AND DATE_FORMAT(birthday, '%d') >= DATE_FORMAT(SYSDATE(), '%d')
		ORDER BY DATE_FORMAT(birthday, '%d/%m') ASC;";	
    
    try {
        $db = new db($selecteddb);
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");
        if ($resultado = mysqli_query($link, $sql)) {
            if (mysqli_num_rows($resultado) > 0) {
                while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
                    $members[] = $row;
                }
                $message = 'Si hay cumpleañeros';
                $result = 1;
            } else {
            $result  = 0;
            $message = 'No hay cumpleañeros';
        }
        /* liberar el conjunto de resultados */
        mysqli_free_result($resultado);  
        }
        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
		$out['data'] = $members;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
    // Close connection
    $link->close();
});

// GET Obtener los miembros de la Gobernacion
$app->get('/api/gobernacion', function(Request $request, Response $response){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //
    $message = '';
    $result = 0;
    $members = array();
		
		$sql = "SELECT CONCAT_WS(' ', M.name, M.last_name) fullname, T.description, M.img_url
		FROM tb_members M
		INNER JOIN tb_type_members TM ON M.member_code = TM.member_code
		INNER JOIN tb_type T ON TM.id_type = T.id_type
		WHERE T.isGovernment = 1
		ORDER BY T.id_type ASC;";	
    
    try {
        $db = new db($selecteddb);
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");
        $base_url = $this->get('base_url_members');
        if ($resultado = mysqli_query($link, $sql)) {
            if (mysqli_num_rows($resultado) > 0) {
                while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
                    $row['img_url'] = $base_url.''.$row['img_url'];
                    $members[] = $row;
                }
                $message = 'Si hay members registradas';
                $result = 1;
            } else {
            $result  = 0;
            $message = 'No hay members registradas';
        }
            /* liberar el conjunto de resultados */
            mysqli_free_result($resultado);  
        }

        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
        $out['data'] = $members;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
    // Close connection
    $link->close(); 
});

// GET Obtener los miembros Jefes de Zona
$app->get('/api/jefes_region', function(Request $request, Response $response){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //
		
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
        $db = new db($selecteddb);
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");
        $base_url = $this->get('base_url_members');
        if ($resultado = mysqli_query($link, $sql)) {
            if (mysqli_num_rows($resultado) > 0) {
                while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
                    $row['img_url'] = $base_url.''.$row['img_url'];
                    $members[] = $row;
                }
                $message = 'Si hay Jefes';
                $result = 1;
            } else {
            $result  = 0;
            $message = 'No hay jefes';
        }
        /* liberar el conjunto de resultados */
        mysqli_free_result($resultado);  
        }
        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
		$out['data'] = $members;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
    // Close connection
    $link->close();
});

// GET Obtener los miembros Jefes de Zona
$app->get('/api/jefes_zona', function(Request $request, Response $response){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //
		
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
        $db = new db($selecteddb);
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");
        $base_url = $this->get('base_url_members');
        if ($resultado = mysqli_query($link, $sql)) {
            if (mysqli_num_rows($resultado) > 0) {
                while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
                    $row['img_url'] = $base_url.''.$row['img_url'];
                    $members[] = $row;
                }
                $message = 'Si hay Jefes';
                $result = 1;
            } else {
            $result  = 0;
            $message = 'No hay Jefes';
        }
        /* liberar el conjunto de resultados */
        mysqli_free_result($resultado);  
        }
        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
        $out['data'] = $members;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
    // Close connection
    $link->close();
});

// GET Obtener los miembros Asesores
$app->get('/api/asesores', function(Request $request, Response $response){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //
		
    $message = '';
    $result = 0;
    $members = array();
    
    $sql = "SELECT CONCAT_WS(' ', M.name, M.last_name) fullname, T.description, TM.info, M.img_url
    FROM tb_members M
    INNER JOIN tb_type_members TM ON M.member_code = TM.member_code
    INNER JOIN tb_type T ON TM.id_type = T.id_type
    WHERE T.id_type = 12;";	
    
    try {
        $db = new db($selecteddb);
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");
        $base_url = $this->get('base_url_members');
        if ($resultado = mysqli_query($link, $sql)) {
            if (mysqli_num_rows($resultado) > 0) {
                while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
                    $row['img_url'] = $base_url.''.$row['img_url'];
                    $members[] = $row;
                }
                $message = 'Si hay Asesores';
                $result = 1;
            } else {
            $result  = 0;
            $message = 'No hay Asesores';
        }
        /* liberar el conjunto de resultados */
        mysqli_free_result($resultado);  
        }
        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
        $out['data'] = $members;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
    // Close connection
    $link->close();
});

//POST Insertar Miembro
$app->post('/api/miembro_add', function(Request $request, Response $response){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //

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
    $type_member = $request->getParam('type_member');
    $admission_date = date('Y-m-d h:i:s');

    $type_member = explode(',', $type_member);
    
    $message = '';
    $result = 0;  
    
    $sql = "INSERT INTO tb_members (id_member, name, last_name, birthday, member_code, club_code
    , email, phone, cellphone, id_rol_member, gender, admission_date, id_zone, password) 
    VALUES (NULL, '$name', '$last_name', '$birthday', '$member_code', '$club_code', '$email', '$phone', '$cellphone', '$id_rol_member', '$gender'
    , '$admission_date', '$id_zone', '0000');";	
        
    try {
        $db = new db($selecteddb);
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");
        $directory = $this->get('upload_directory_members');
        $uploadedFiles = $request->getUploadedFiles();
        // handle single input with single file upload
        if(!isset($uploadedFiles['files']) || strlen($uploadedFiles['files']->file) == 0) {
            if ($resultado = mysqli_query($link, $sql)) {
                foreach ($type_member as $value) {
                    # code...
                    $sql = "INSERT INTO tb_type_members
                    (id_type, member_code) VALUES ('$id_type', '$member_code')";

                    $resultado = mysqli_query($link, $sql);
                }
                $result = 1;
                $message = "Miembro Agregado Exitosamente!";
            } else {
                $result = 0;
                $message = "No ha sido posible agregar el miembro!";
            }
        } else {
            $uploadedFile = $uploadedFiles['files'];

            mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
            if ($resultado = mysqli_query($link, $sql)) {
                //
                if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                    $filename = moveUploadedFileMembers($directory, $uploadedFile);

                    // $image_path = $directory.'/'.$filename;
                    $image_path = $filename;
                    $lastInsertId = mysqli_insert_id($link);
                    $sql = "UPDATE tb_members
                    SET img_url = '$image_path'
                    WHERE id_member = $lastInsertId
                    LIMIT 1";

                    if ($resultado = mysqli_query($link, $sql)) {
                        foreach ($type_member as $value) {
                            # code...
                            $sql = "INSERT INTO tb_type_members
                            (id_type, member_code) VALUES ('$id_type', '$member_code')";
        
                            $resultado = mysqli_query($link, $sql);
                        }
                        mysqli_commit($link);
                        $result = 1;
                        $message = "Miembro Agregado Exitosamente!";
                    } else {
                        $result = 0;
                        $message = "No ha sido posible agregar el miembro 1!";
                        mysqli_rollback($link);
                    }
                } else {
                    $result = 0;
                    $message = "No ha sido posible agregar el miembro 2!";
                    mysqli_rollback($link);
                }
                //
            } else {
                mysqli_rollback($link);
                $result = 0;
                $message = "No ha sido posible agregar el miembro 3!";
            }
        }
        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
    // Close connection
    $link->close();
});

// PUT Editar un miembro
$app->post('/api/miembro_edit/{id}', function(Request $request, Response $response){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //
    $id_member = $request->getAttribute('id');
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
    $password = $request->getParam('password');
    $type_member = $request->getParam('type_member');
    $admission_date = date('Y-m-d h:i:s');

    $type_member = explode(',', $type_member);

    $message = '';
    $result = 0;

    try {
        $db = new db($selecteddb);
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");
        $directory = $this->get('upload_directory_members');
        //$directory = $this->get('base_url_members');
        $uploadedFiles = $request->getUploadedFiles();
        // handle single input with single file upload
        if(!isset($uploadedFiles['files']) || strlen($uploadedFiles['files']->file) == 0) {
            //Query de edicion sin cambio de imagen
            $sql = "UPDATE tb_members SET
            name = '$name',
            last_name = '$last_name',
            birthday = '$birthday',
            member_code = '$member_code',
            club_code = '$club_code',
            email = '$email',
            phone = '$phone',
            cellphone = '$cellphone',
            id_rol_member = '$id_rol_member',
            gender = '$gender',
            admission_date = '$admission_date',
            id_zone = '$id_zone',
            password = '$password'
            WHERE id_member = $id_member
            LIMIT 1";

        } else {
            $uploadedFile = $uploadedFiles['files'];
            //Comprobacion del upload
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                //Se mueve el file a la ubicacion
                $filename = moveUploadedFileMembers($directory, $uploadedFile);

                //Seleccion del path actual
                $sql = "SELECT img_url
                FROM tb_members
                WHERE id_member = $id_member";

                $resultado = mysqli_query($link, $sql);
                $row = mysqli_fetch_array($resultado, MYSQLI_ASSOC);
                if(!$row['img_url'] == 'default.png'){
                    unlink($directory.'/'.$row['img_url']);
                }
                mysqli_free_result($resultado);

                //Se elimina el file actual

                //Query de edicion con cambio de imagen
                $sql = "UPDATE tb_members SET
                name = '$name',
                last_name = '$last_name',
                birthday = '$birthday',
                member_code = '$member_code',
                club_code = '$club_code',
                email = '$email',
                phone = '$phone',
                cellphone = '$cellphone',
                id_rol_member = '$id_rol_member',
                gender = '$gender',
                admission_date = '$admission_date',
                id_zone = '$id_zone',
                password = '$password',
                img_url = '$filename'
                WHERE id_member = $id_member
                LIMIT 1";

            } else {
                //Fallo en el upload del file
                $out['ok'] = 1;
                $out['result'] = 0;
                $out['message'] = "No ha sido posible editar el miembro, error al guardar imagen!";
                echo json_encode($out, JSON_UNESCAPED_UNICODE);
                die();
            }
        }

        if ($resultado = mysqli_query($link, $sql)) {
            //Eliminan relaciones de tipo actuales
            $sql = "DELETE FROM tb_type_members 
            WHERE member_code = $member_code";

            if(!$resultado = mysqli_query($link, $sql)){
                $out['ok'] = 1;
                $out['result'] = 0;
                $out['message'] = "No se han podido eliminar las relaciones de tipo miembro";
                echo json_encode($out, JSON_UNESCAPED_UNICODE);
                die();
            }

            //Se ingresan las nuevas
            foreach ($type_member as $value) {
                # code...
                $sql = "INSERT INTO tb_type_members
                (id_type, member_code) VALUES ('$id_type', '$member_code')";

                $resultado = mysqli_query($link, $sql);
            }
            $result = 1;
            $message = "Miembro Editado Exitosamente!";
        } else {
            $result = 0;
            $message = "No ha sido posible editar el miembro!";
        }
        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
    // Close connection
    $link->close();
});

// PUT Editar status de un miembro
$app->put('/api/miembro_delete/{id}', function(Request $request, Response $response){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //
    $id_member = $request->getAttribute('id');
    $status = 0;

    try {
        $db = new db($selecteddb);
        $link = $db->dbConnection();
        mysqli_query($link, "SET NAMES 'utf8'");

        $sql = "UPDATE tb_members
        SET status = 0
        WHERE id_member = $id_member
        LIMIT 1";

        if ($resultado = mysqli_query($link, $sql)) {
            $result = 1;
            $message = "Miembro eliminado exitosamente!";
        } else {
            $result = 0;
            $message = "No ha sido posible eliminar el Miembro!";
        }
        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
    // Close connection
    $link->close();
});

function moveUploadedFileMembers($directory, UploadedFile $uploadedFile)
{
    $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
    $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
    $filename = sprintf('%s.%0.8s', $basename, $extension);

    $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

    return $filename;
}