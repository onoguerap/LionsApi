<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require '../src/config/db.php';

$app = new \Slim\App;

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


$app->run();