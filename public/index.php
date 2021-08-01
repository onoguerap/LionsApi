<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require '../src/config/db.php';
require '../src/config/grilldb.php';

$app = new \Slim\App;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: *");

// Rutas Nuevas
// Ruta Test
require '../src/rutas/test.php';


// Rutas para login
// require '../src/rutas/login.php';
// GRILL API
// Rutas para test
require '../src/rutas_grill/grill.php';

$app->run();

