<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

require_once('db.php');
require_once('../model/Ciudades.php');
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

if(array_key_exists("idciudades", $_GET)){
    $idciudades = $_GET['idciudades'];
    if($idciudades == '' || !is_numeric($idciudades)){
        $response = new Response();
        $response->setSuccess(false);
        $response->setHttpStatusCode(400);
        $response->addMessage("Id de ciudad no válido");
        $response->send();
        exit;
    }    
}

if($_SERVER['REQUEST_METHOD']==='GET'){
    if(array_key_exists("idciudades", $_GET)){
        try {
            $query = $db->prepare('select id_ciudades, nom_ciudades from ciudades where id_ciudades = :idciudades');
            $query->bindParam(':idciudades', $idciudades);
            $query->execute();

            $rowCount = $query->rowCount();

            if($rowCount === 0){
                $response = new Response();
                $response->setSuccess(false);
                $response->setHttpStatusCode(404);
                $response->addMessage("ciudad no encontrado");
                $response->send();
                exit;
            }

            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $ciudades = new ciudades($row['id_ciudades'], $row['nom_ciudades']);
                $ciudadesArray[] = $ciudades->returnciudadestoAsArray();
            }
            $returnData = array();
            $returnData['nro_filas'] = $rowCount;
            $returnData['ciudades'] = $ciudadesArray;

            $response = new Response();
            $response->setSuccess(true);
            $response->setHttpStatusCode(200);
            $response->setData($returnData);
            $response->send();
            exit;

        } catch (ciudadesException $ex) {
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
            $query = $db->prepare('select id_ciudades, nom_ciudades from ciudades');
            $query->execute();

            $rowCount = $query->rowCount();
            $ciudadesArray = array();

            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $ciudades = new ciudades($row['id_ciudades'], $row['nom_ciudades']);
                $ciudadesArray[] = $ciudades->returnciudadesAsArray();
            }

            $returnData = array();
            $returnData['filas_retornadas'] = $rowCount;
            $returnData['ciudades'] = $ciudadesArray;

            $response = new Response();
            $response->setSuccess(true);
            $response->setHttpStatusCode(200);
            $response->toCache(true);
            $response->setData($returnData);
            $response->send();
            exit;

        }catch(ciudadesException $ex){
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
        $query = $db->prepare('delete from ciudades where id_ciudades = :idciudades');
        $query->bindParam(':idciudades', $idciudades);
        $query->execute();

        $rowCount = $query->rowCount();

        if($rowCount===0){
            $response = new Response();
            $response->setSuccess(false);
            $response->setHttpStatusCode(404);
            $response->addMessage('ciudad no encontrado');
            $response->send();
            exit();
        }
        $response = new Response();
        $response->setSuccess(true);
        $response->setHttpStatusCode(200);
        $response->addMessage('ciudad eliminado');
        $response->send();
        exit();
    }catch(PDOException $ex){
        $response = new Response();
        $response->setSuccess(false);
        $response->setHttpStatusCode(500);
        $response->addMessage('Error eliminando ciudad');
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
        if(!isset($jsonData->nomciudades)){
            $response = new Response();
            $response->setSuccess(false);
            $response->setHttpStatusCode(400);
            $response->addMessage('Nombre de ciudad es obligatorio');
            $response->send();
            exit();
        }
        $newciudades = new ciudades(null, $jsonData->nomciudades);
        $query = $db->prepare('insert into ciudades (nom_ciudades) values (:nomciudades)');
        $query->bindParam(':nomciudades', $jsonData->nomciudades, PDO::PARAM_STR);
        $query->execute();
        $rowCount = $query->rowCount();
        
        if($rowCount===0){
            $response = new Response();
            $response->setSuccess(false);
            $response->setHttpStatusCode(400);
            $response->addMessage('Falló creación de ciudades');
            $response->send();
            exit();
        }
        $lastIdciudades = $db->lastInsertId();

        $response = new Response();
        $response->setSuccess(true);
        $response->setHttpStatusCode(201);
        $response->addMessage('ciudad creado');
        $response->setData($lastIdciudades);
        $response->send();
        exit();
    }catch(ciudadesException $ex){
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