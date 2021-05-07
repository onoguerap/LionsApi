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

// Rutas para login
require '../src/rutas/login.php';
// Rutas para clubes
require '../src/rutas/clubes.php';
// Rutas para miembros
require '../src/rutas/miembros.php';
// Rutas para actividades
require '../src/rutas/actividades.php';
// Rutas para regiones
require '../src/rutas/regiones.php';
// Rutas para zonas
require '../src/rutas/zonas.php';
// Rutas para roles
require '../src/rutas/roles.php';
// Rutas para tipos
require '../src/rutas/tipos.php';
// Rutas para tipos
require '../src/rutas/paises.php';
// GRILL API
// Rutas para test
require '../src/rutas_grill/grill.php';

$app->run();

