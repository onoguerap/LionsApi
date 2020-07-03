<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Http\UploadedFile;

//$app = new \Slim\App;

$container = $app->getContainer();
$container['upload_directory'] = __DIR__ . '/uploads/actividades';

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

// POST Agregar una actividad
$app->post('/api/actividad_add', function(Request $request, Response $response){
    $title = $request->getParam('title');
    $schedule = $request->getParam('schedule');
    $description = $request->getParam('description');

    $sql = "INSERT INTO tb_activities (id_activity, title, schedule, description)
    VALUES (null, :title, :schedule, :description);";

    // $this->db->beginTransaction(); , $this->db->commit(); and $this->db->rollBack();

    try {
        $db = new db();
        $db = $db->dbConnection();
        $resultado = $db->prepare($sql);

        $resultado->bindParam(':title', $title);
        $resultado->bindParam(':schedule', $schedule);
        $resultado->bindParam(':description', $description);

        $directory = $this->get('upload_directory');
        $uploadedFiles = $request->getUploadedFiles();
        // handle single input with single file upload
        if(!isset($uploadedFiles['files'])) {
            $result = 0;
            $message = "No ha sido posible agregar la actividad, imagen no enviada!";
        } else {
            $uploadedFile = $uploadedFiles['files'];

            $db->beginTransaction();

            if ($resultado->execute()) {
                //
                if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                    $filename = moveUploadedFile($directory, $uploadedFile);

                    $image_path = $directory.'/'.$filename;
                    $lastInsertId = $db->lastInsertId();
                    $sql = "UPDATE tb_activities
                    SET image_path = :image_path
                    WHERE id_activity = $lastInsertId
                    LIMIT 1";

                    $resultado = $db->prepare($sql);
                    $resultado->bindParam(':image_path', $image_path);

                    if ($resultado->execute()) {
                        $db->commit();
                        $result = 1;
                        $message = "Actividad Agregada Exitosamente!";
                    } else {
                        $result = 0;
                        $message = "No ha sido posible agregar la actividad!";
                        $db->rollBack();
                    }
                } else {
                    $result = 0;
                    $message = "No ha sido posible agregar la actividad!";
                    $db->rollBack();
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
});

// PUT Editar una actividad
$app->post('/api/actividad_edit/{id}', function(Request $request, Response $response){
    $id_activity = $request->getAttribute('id');
    $title = $request->getParam('title');
    $schedule = $request->getParam('schedule');
    $description = $request->getParam('description');
    $image_path = "";

    try {
        $db = new db();
        $db = $db->dbConnection();

        $directory = $this->get('upload_directory');
        $uploadedFiles = $request->getUploadedFiles();
        // handle single input with single file upload
        if(!isset($uploadedFiles['files']) || strlen($uploadedFiles['files']->file) == 0) {
            //Query de edicion sin cambio de imagen
            $sql = "UPDATE tb_activities SET
            title = :title,
            schedule = :schedule,
            description = :description
            WHERE id_activity = $id_activity
            LIMIT 1";

            $resultado = $db->prepare($sql);

            $resultado->bindParam(':title', $title);
            $resultado->bindParam(':schedule', $schedule);
            $resultado->bindParam(':description', $description);

        } else {
            $uploadedFile = $uploadedFiles['files'];
            //Comprobacion del upload
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                //Se mueve el file a la ubicacion
                $filename = moveUploadedFile($directory, $uploadedFile);
                //Generacion del nuevo path
                $image_path = $directory.'/'.$filename;

                //Seleccion del path actual
                $sql = "SELECT image_path
                FROM tb_activities
                WHERE id_activity = $id_activity";

                $resultado = $db->query($sql);
                $oldImagePath = $resultado->fetchAll(PDO::FETCH_OBJ);
                unlink($oldImagePath[0]->image_path);

                //Se elimina el file actual

                //Query de edicion con cambio de imagen
                $sql = "UPDATE tb_activities SET
                title = :title,
                schedule = :schedule,
                description = :description,
                image_path = :image_path
                WHERE id_activity = $id_activity
                LIMIT 1";

                $resultado = $db->prepare($sql);

                $resultado->bindParam(':title', $title);
                $resultado->bindParam(':schedule', $schedule);
                $resultado->bindParam(':description', $description);
                $resultado->bindParam(':image_path', $image_path);

            } else {
                //Fallo en el upload del file
                $out['ok'] = 1;
                $out['result'] = 0;
                $out['message'] = "No ha sido posible editar la actividad, error al guardar imagen!";
                echo json_encode($out, JSON_UNESCAPED_UNICODE);
                die();
            }
        }

        if ($resultado->execute()) {
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
});

// DELETE Editar status de una zona
$app->delete('/api/actividad_delete/{id}', function(Request $request, Response $response){
    $id_activity = $request->getAttribute('id');
    $status = 0;

    try {
        $db = new db();
        $db = $db->dbConnection();

        //Seleccion del path actual
        $sql = "SELECT image_path
        FROM tb_activities
        WHERE id_activity = $id_activity";

        $resultado = $db->query($sql);
        $oldImagePath = $resultado->fetchAll(PDO::FETCH_OBJ);
        //Eliminando imagen
        unlink($oldImagePath[0]->image_path);
        //

        $sql = "DELETE FROM tb_activities
        WHERE id_activity = '$id_activity'
        LIMIT 1";

        $resultado = $db->prepare($sql);

        if ($resultado->execute()) {
            $result = 1;
            $message = "Actividad Eliminada Exitosamente!";
        } else {
            $result = 0;
            $message = "No ha sido posible eliminar la actividad!";
        }
        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
});


function moveUploadedFile($directory, UploadedFile $uploadedFile)
{
    $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
    $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
    $filename = sprintf('%s.%0.8s', $basename, $extension);

    $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

    return $filename;
}