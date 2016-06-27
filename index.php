<?php
header('Content-Type: application/json');
//error_reporting(E_ALL);
//ini_set("display_errors", 1);
/*
 * API de información de servicios de Venezuela
 * Creado por William Cabrera (aka willicab) <cabrerawilliam@gmail.com>
 * Versión 0.2
 * 
 * Este código está liberado bajo los términos de la WTFPL versión 2
 * o superior, puede conseguir una copia en http://www.wtfpl.net/about/
 */
require "vendor/autoload.php";

Flight::map('notFound', function(){
    Flight::halt(404, '{"error":true, "descripcion":"página no encontrada"}');
});

Flight::route('/', function(){
    echo 'hello world!';
});

Flight::route("GET /cantv(/@recurso(/@param1(/@param2)))", function($recurso="", $param1="", $param2=""){
    if ($recurso == "") Flight::halt(400, '{"error":true, "descripcion":"no se ha indicado el recurso"}');
    require "clases/cantv.php";
    Flight::register("cantv", "Cantv");
    switch($recurso) {
        case "deuda":
            if ($param1 == "") Flight::halt(400, '{"error":true, "descripcion":"No se ha indicado el código de area"}');
            if ($param2 == "") Flight::halt(400, '{"error":true, "descripcion":"No se ha indicado el número de teléfono"}');
            print Flight::cantv()->obtenerDeuda($param1, $param2);
            break;
        default:
            Flight::halt(400, '{"error":true, "descripcion":"El recurso indicado no existe"}');
    }
});

Flight::route("GET /cne(/@recurso(/@param1(/@param2)))", function($recurso="", $param1="", $param2=""){
    if ($recurso == "") Flight::halt(400, '{"error":true, "descripcion":"no se ha indicado el recurso"}');
    require "clases/cne.php";
    Flight::register("cne", "Cne");
    switch($recurso) {
        case "elector":
            if ($param1 == "") Flight::halt(400, '{"error":true, "descripcion":"No se ha indicado la nacionalidad"}');
            if ($param2 == "") Flight::halt(400, '{"error":true, "descripcion":"No se ha indicado el número de cédula de identidad"}');
            print Flight::cne()->obtenerElector($param1, $param2);
            break;
        default:
            Flight::halt(400, '{"error":true, "descripcion":"El recurso indicado no existe"}');
    }
});

Flight::route("GET /corpoelec(/@recurso(/@param1))", function($recurso="", $param1=""){
    if ($recurso == "") Flight::halt(400, '{"error":true, "descripcion":"no se ha indicado el recurso"}');
    require "clases/corpoelec.php";
    Flight::register("corpoelec", "Corpoelec");
    switch($recurso) {
        case "deuda":
            if ($param1 == "") Flight::halt(400, '{"error":true, "descripcion":"No se ha indicado el NIC"}');
            print Flight::corpoelec()->obtenerDeuda($param1);
            break;
        default:
            Flight::halt(400, '{"error":true, "descripcion":"El recurso indicado no existe"}');
    }
});

Flight::route("GET /ivss(/@recurso(/@param1(/@param2(/@param3(/@param4(/@param5))))))", function($recurso="", $param1="", $param2="", $param3="", $param4="", $param5=""){
    if ($recurso == "") Flight::halt(400, '{"error":true, "descripcion":"no se ha indicado el recurso"}');
    require "clases/ivss.php";
    Flight::register("ivss", "Ivss");
    switch($recurso) {
        case "cuenta":
            if ($param1 == "") Flight::halt(400, '{"error":true, "descripcion":"No se ha indicado la nacionalidad"}');
            if ($param2 == "") Flight::halt(400, '{"error":true, "descripcion":"No se ha indicado el número de cédula de identidad"}');
            if ($param3 == "") Flight::halt(400, '{"error":true, "descripcion":"No se ha indicado el día de nacimiento"}');
            if ($param4 == "") Flight::halt(400, '{"error":true, "descripcion":"No se ha indicado el mes de nacimiento"}');
            if ($param5 == "") Flight::halt(400, '{"error":true, "descripcion":"No se ha indicado el año de nacimiento"}');
            print Flight::ivss()->obtenerCuenta($param1, $param2, $param3, $param4, $param5);
            break;
        default:
            Flight::halt(400, '{"error":true, "descripcion":"El recurso indicado no existe"}');
    }
});

Flight::route("GET /seniat(/@recurso(/@param1))", function($recurso="", $param1=""){
    if ($recurso == "") Flight::halt(400, '{"error":true, "descripcion":"no se ha indicado el recurso"}');
    require "clases/seniat.php";
    Flight::register("seniat", "Seniat");
    switch($recurso) {
        case "contribuyente":
            if ($param1 == "") Flight::halt(400, '{"error":true, "descripcion":"No se ha indicado el RIF"}');
            print Flight::seniat()->obtenerContribuyente($param1);
            break;
        default:
            Flight::halt(400, '{"error":true, "descripcion":"El recurso indicado no existe"}');
    }
});

Flight::start();
