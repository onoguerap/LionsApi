<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Http\UploadedFile;

// GET Obtener los tipos
$app->get('/api/paises', function(Request $request, Response $response, array $args){
   
    $message = '';
    $result = 0;
    $paises = array();
    try{
         $country['id'] = '1';
         $country['name'] = 'cr';
         $country['flag'] = 'cr';
         $country['country']['Port']['id'] = '1';
         $country['country']['Port']['name'] = 'Costa Rica'; 

         $paises[] = $country;
         $result = 1;

        //  $country['id'] = '2';
        //  $country['name'] = 'hn';
        //  $country['flag'] = 'hn';
        //  $country['country']['Port']['id'] = '2';
        //  $country['country']['Port']['name'] = 'Honduras'; 

        //  $paises[] = $country;

        //  $country['id'] = '3';
        //  $country['name'] = 'pn';
        //  $country['flag'] = 'pn';
        //  $country['country']['Port']['id'] = '3';
        //  $country['country']['Port']['name'] = 'Panamá'; 

        //  $paises[] = $country;

        $out['ok'] = 1;
        $out['result'] = $result;
        $out['message'] = $message;
        $out['data'] = $paises;
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo '{"error" : {"text":'.$e.getMessage().'}';
    }
});