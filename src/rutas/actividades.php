<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Http\UploadedFile;

//$app = new \Slim\App;
$container = $app->getContainer();
$container['upload_directory_activities'] = __DIR__ . '/uploads/actividades';
$container['base_url_activities'] = 'http://138.68.239.185/uploads/actividades/';

// GET Obtener los miembros por filtro o la totalidad
$app->get('/api/actividades', function(Request $request, Response $response){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //
    $message = '';
    $activities = array();

    $sql = "SELECT * 
		FROM tb_activities 
		WHERE DATE_FORMAT(schedule, '%Y-%m-%d') >= DATE_FORMAT(SYSDATE(), '%Y-%m-%d')
		ORDER BY schedule DESC";

    try {
        $db = new db($selecteddb);
        $link = $db->dbConnection();
        if ($resultado = mysqli_query($link, $sql)) {
            if (mysqli_num_rows($resultado) > 0) {
                while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
                    $activities[] = $row;
                }
                $message = 'Si hay actividades registradas';
                $result = 1;
            } else {
            $result  = 0;
            $message = 'No hay actividades registradas';
        }
        /* liberar el conjunto de resultados */
        mysqli_free_result($resultado);  
        }

        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
        $out['data'] = $activities;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
    // Close connection
    $link->close();
});

// POST Agregar una actividad
$app->post('/api/actividad_add', function(Request $request, Response $response){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //
    $title = $request->getParam('title');
    $schedule = $request->getParam('schedule');
    $description = $request->getParam('description');

    $sql = "INSERT INTO tb_activities (id_activity, title, schedule, description)
    VALUES (null, '$title', '$schedule', '$description');";

    // $this->db->beginTransaction(); , $this->db->commit(); and $this->db->rollBack();

    try {
 
        $db = new db($selecteddb);
        $link = $db->dbConnection();

        // $directory = $this->get('upload_directory_activities');
        $directory = $this->get('base_url_activities');
        $uploadedFiles = $request->getUploadedFiles();
        // handle single input with single file upload
        if(!isset($uploadedFiles['files']) || strlen($uploadedFiles['files']->file) == 0) {
            $result = 0;
            $message = "No ha sido posible agregar la actividad, imagen no enviada!";
        } else {
            $uploadedFile = $uploadedFiles['files'];

            mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);

            if ($resultado = mysqli_query($link, $sql)) {
                //
                if ($uploadedFile->getError() === UPLOAD_ERR_OK) {

                   
                    $filename = moveUploadedFileActivities($directory, $uploadedFile);

                    $image_path = $directory.''.$filename;
                    echo $image_path;
                    $lastInsertId = mysqli_insert_id($link);
                    echo $lastInsertId;
                    $sql = "UPDATE tb_activities
                    SET image_path = '$image_path'
                    WHERE id_activity = $lastInsertId
                    LIMIT 1";

                    if ($resultado = mysqli_query($link, $sql)) {
                        mysqli_commit($link);
                        $result = 1;
                        $message = "Actividad Agregada Exitosamente!";
                    } else {
                        $result = 0;
                        $message = "No ha sido posible agregar la actividad!";
                        mysqli_rollback($link);
                    }
                } else {
                    $result = 0;
                    $message = "No ha sido posible agregar la actividad!";
                    mysqli_rollback($link);
                }
                //
            } else {
                $db->rollBack();
                $result = 0;
                $message = "No ha sido posible agregar la actividad!";
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

// PUT Editar una actividad
$app->post('/api/actividad_edit/{id}', function(Request $request, Response $response){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //
    $id_activity = $request->getAttribute('id');
    $title = $request->getParam('title');
    $schedule = $request->getParam('schedule');
    $description = $request->getParam('description');
    $image_path = "";

    try {
        $db = new db($selecteddb);
        $link = $db->dbConnection();

        // $directory = $this->get('upload_directory_activities');
        $directory = $this->get('base_url_activities');
        $uploadedFiles = $request->getUploadedFiles();
        // handle single input with single file upload
        if(!isset($uploadedFiles['files']) || strlen($uploadedFiles['files']->file) == 0) {
            //Query de edicion sin cambio de imagen
            $sql = "UPDATE tb_activities SET
            title = '$title',
            schedule = '$schedule',
            description = '$description'
            WHERE id_activity = $id_activity
            LIMIT 1";

            // $resultado = $db->prepare($sql);

            // $resultado->bindParam(':title', $title);
            // $resultado->bindParam(':schedule', $schedule);
            // $resultado->bindParam(':description', $description);

        } else {
            $uploadedFile = $uploadedFiles['files'];
            //Comprobacion del upload
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                //Se mueve el file a la ubicacion
                $filename = moveUploadedFileActivities($directory, $uploadedFile);
                //Generacion del nuevo path
                $image_path = $directory.''.$filename;

                //Seleccion del path actual
                $sql = "SELECT image_path
                FROM tb_activities
                WHERE id_activity = $id_activity";

                $resultado = mysqli_query($link, $sql);
                $row = mysqli_fetch_array($resultado, MYSQLI_ASSOC);
                // $oldImagePath = $resultado->fetchAll(PDO::FETCH_OBJ);
                unlink($row['image_path']);
                mysqli_free_result($resultado);

                //Se elimina el file actual

                //Query de edicion con cambio de imagen
                $sql = "UPDATE tb_activities SET
                title = '$title',
                schedule = '$schedule',
                description = '$description',
                image_path = '$image_path'
                WHERE id_activity = $id_activity
                LIMIT 1";

                $resultado = mysqli_query($link, $sql);

                // $resultado->bindParam(':title', $title);
                // $resultado->bindParam(':schedule', $schedule);
                // $resultado->bindParam(':description', $description);
                // $resultado->bindParam(':image_path', $image_path);

            } else {
                //Fallo en el upload del file
                $out['ok'] = 1;
                $out['result'] = 0;
                $out['message'] = "No ha sido posible editar la actividad, error al guardar imagen!";
                echo json_encode($out, JSON_UNESCAPED_UNICODE);
                die();
            }
        }

        if ($resultado = mysqli_query($link, $sql)) {
            $result = 1;
            $message = "Actividad Editada Exitosamente!";
        } else {
            $result = 0;
            $message = "No ha sido posible editar la actividad!";
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

// DELETE Editar status de una zona
$app->delete('/api/actividad_delete/{id}', function(Request $request, Response $response){
    //Seteo del pais o cuenta
    $selecteddb = json_decode($request->getHeaderLine('Country'));
    //
    $id_activity = $request->getAttribute('id');
    $status = 0;

    try {
        $db = new db($selecteddb);
        $link = $db->dbConnection();

        //Seleccion del path actual
        $sql = "SELECT image_path
        FROM tb_activities
        WHERE id_activity = $id_activity";

        $resultado = mysqli_query($link, $sql);
        $row = mysqli_fetch_array($resultado, MYSQLI_ASSOC);
        // $oldImagePath = $resultado->fetchAll(PDO::FETCH_OBJ);
        //Eliminando imagen
        unlink($row['image_path']);
        //

        $sql = "DELETE FROM tb_activities
        WHERE id_activity = '$id_activity'
        LIMIT 1";

       
        if ($resultado = mysqli_query($link, $sql)) {
            $result = 1;
            $message = "Actividad eliminada exitosamente!";
        } else {
            $result = 0;
            $message = "No ha sido posible eliminar la Actividad!";
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


function moveUploadedFileActivities($directory, UploadedFile $uploadedFile)
{
    $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
    $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
    $filename = sprintf('%s.%0.8s', $basename, $extension);

    $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

    return $filename;
}
