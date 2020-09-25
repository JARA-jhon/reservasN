<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

require_once('db.php');
require_once('../model/Sucursales.php');
require_once('../model/Response.php');

//Conectar a la base de datos
try{
    $db = DB::conectarDB();
}catch(PDOException $ex){
    $response = new Response();
    $response->setSuccess(false);
    $response->setHttpStatusCode(500);
    $response->addMessage("Error de conexión a la BD");
    $response->send();
    exit;
}

if(array_key_exists("idsucursales", $_GET)){
    $idsucursales = $_GET['idsucursales'];
    if($idsucursales == '' || !is_numeric($idsucursales)){
        $response = new Response();
        $response->setSuccess(false);
        $response->setHttpStatusCode(400);
        $response->addMessage("Id de sucursal no válido");
        $response->send();
        exit;
    }    
}

if($_SERVER['REQUEST_METHOD']==='GET'){
    if(array_key_exists("idsucursales", $_GET)){
        try {
            $query = $db->prepare('select id_sucursales, nom_sucursales from sucursales where id_sucursales = :idsucursales');
            $query->bindParam(':idsucursales', $idsucursales);
            $query->execute();

            $rowCount = $query->rowCount();

            if($rowCount === 0){
                $response = new Response();
                $response->setSuccess(false);
                $response->setHttpStatusCode(404);
                $response->addMessage("sucursal no encontrado");
                $response->send();
                exit;
            }

            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $sucursales = new sucursales($row['id_sucursales'], $row['nom_sucursales']);
                $sucursalesArray[] = $sucursales->returnsucursalesAsArray();
            }
            $returnData = array();
            $returnData['nro_filas'] = $rowCount;
            $returnData['sucursales'] = $sucursalesArray;

            $response = new Response();
            $response->setSuccess(true);
            $response->setHttpStatusCode(200);
            $response->setData($returnData);
            $response->send();
            exit;

        } catch (sucursalesException $ex) {
            $response = new Response();
            $response->setSuccess(false);
            $response->setHttpStatusCode(500);
            $response->addMessage($ex->getMessage());
            $response->send();
            exit;
        }catch(PDOException $ex){
            $response = new Response();
            $response->setSuccess(false);
            $response->setHttpStatusCode(500);
            $response->addMessage("Error conectando a Base de Datos");
            $response->send();
            exit;
        }
    }else{
        try{
            $query = $db->prepare('select id_sucursales, nom_sucursales from sucursales');
            $query->execute();

            $rowCount = $query->rowCount();
            $sucursalesArray = array();

            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $sucursales = new sucursales($row['id_sucursales'], $row['nom_sucursales']);
                $sucursalesArray[] = $sucursales->returnsucursalesAsArray();
            }

            $returnData = array();
            $returnData['filas_retornadas'] = $rowCount;
            $returnData['sucursales'] = $sucursalesArray;

            $response = new Response();
            $response->setSuccess(true);
            $response->setHttpStatusCode(200);
            $response->toCache(true);
            $response->setData($returnData);
            $response->send();
            exit;

        }catch(sucursalesException $ex){
            $response = new Response();
            $response->setSuccess(false);
            $response->setHttpStatusCode(400);
            $response->addMessage($ex->getMessage());
            $response->send();
            exit;
        }catch(PDOException $ex){
            $response = new Response();
            $response->setSuccess(false);
            $response->setHttpStatusCode(500);
            $response->addMessage("Error conectando a Base de Datos");
            $response->send();
            exit;
        }
    }
}elseif($_SERVER['REQUEST_METHOD']==='DELETE'){
    try{
        $query = $db->prepare('delete from sucursales where id_sucursales = :idsucursales');
        $query->bindParam(':idsucursales', $idsucursales);
        $query->execute();

        $rowCount = $query->rowCount();

        if($rowCount===0){
            $response = new Response();
            $response->setSuccess(false);
            $response->setHttpStatusCode(404);
            $response->addMessage('sucursal no encontrado');
            $response->send();
            exit();
        }
        $response = new Response();
        $response->setSuccess(true);
        $response->setHttpStatusCode(200);
        $response->addMessage('sucursal eliminado');
        $response->send();
        exit();
    }catch(PDOException $ex){
        $response = new Response();
        $response->setSuccess(false);
        $response->setHttpStatusCode(500);
        $response->addMessage('Error eliminando sucursal');
        $response->send();
        exit();
    }
}elseif($_SERVER['REQUEST_METHOD'] === 'POST'){
    try{
        if($_SERVER['CONTENT_TYPE'] !== 'application/json'){
            $response = new Response();
            $response->setSuccess(false);
            $response->setHttpStatusCode(400);
            $response->addMessage('Content Type no corresponde a formato JSON');
            $response->send();
            exit();
        }   
        $rawPOSTData = file_get_contents('php://input');
        if(!$jsonData = json_decode($rawPOSTData)){
            $response = new Response();
            $response->setSuccess(false);
            $response->setHttpStatusCode(400);
            $response->addMessage('Request Body no corresponde a formato JSON');
            $response->send();
            exit();
        }
        if(!isset($jsonData->nomsucursales)){
            $response = new Response();
            $response->setSuccess(false);
            $response->setHttpStatusCode(400);
            $response->addMessage('Nombre de sucursales es obligatorio');
            $response->send();
            exit();
        }
        $newsucursales = new sucursales(null, $jsonData->nomsucursales);
        $query = $db->prepare('insert into sucursales (nom_sucursales) values (:nomsucursales)');
        $query->bindParam(':nomsucursales', $jsonData->nomsucursales, PDO::PARAM_STR);
        $query->execute();
        $rowCount = $query->rowCount();
        
        if($rowCount===0){
            $response = new Response();
            $response->setSuccess(false);
            $response->setHttpStatusCode(400);
            $response->addMessage('Falló creación de sucursal');
            $response->send();
            exit();
        }
        $lastIdsucursales = $db->lastInsertId();

        $response = new Response();
        $response->setSuccess(true);
        $response->setHttpStatusCode(201);
        $response->addMessage('sucursales creado');
        $response->setData($lastIdsucursales);
        $response->send();
        exit();
    }catch(sucursalesException $ex){
        $response = new Response();
        $response->setSuccess(false);
        $response->setHttpStatusCode(400);
        $response->addMessage($ex->getMessage());
        $response->send();
        exit();
    }catch(PDOException $ex){
        $response = new Response();
        $response->setSuccess(false);
        $response->setHttpStatusCode(500);
        $response->addMessage('Falló conexión a BD');
        $response->send();
        exit();
    }
}