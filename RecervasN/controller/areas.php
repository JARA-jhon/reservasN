<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

require_once('db.php');
require_once('../model/Areas.php');
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

if(array_key_exists("idArea", $_GET)){
    $idarea = $_GET['idArea'];
    if($idarea == '' || !is_numeric($idarea)){
        $response = new Response();
        $response->setSuccess(false);
        $response->setHttpStatusCode(400);
        $response->addMessage("Id de Area no válido");
        $response->send();
        exit;
    }    
}

if($_SERVER['REQUEST_METHOD']==='GET'){
    if(array_key_exists("idArea", $_GET)){
        try {
            $query = $db->prepare('select id_area, nom_area from area where id_area = :idarea');
            $query->bindParam(':idarea', $idarea);
            $query->execute();

            $rowCount = $query->rowCount();

            if($rowCount === 0){
                $response = new Response();
                $response->setSuccess(false);
                $response->setHttpStatusCode(404);
                $response->addMessage("Area no encontrado");
                $response->send();
                exit;
            }

            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $area = new Area($row['is_area'], $row['area']);
                $areaArray[] = $area->returnareaAsArray();
            }
            $returnData = array();
            $returnData['nro_filas'] = $rowCount;
            $returnData['areas'] = $areaArray;

            $response = new Response();
            $response->setSuccess(true);
            $response->setHttpStatusCode(200);
            $response->setData($returnData);
            $response->send();
            exit;

        } catch (AreaException $ex) {
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
            $query = $db->prepare('select id_area, nom_area from area');
            $query->execute();

            $rowCount = $query->rowCount();
            $areasArray = array();

            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $area = new Area($row['id_area'], $row['nom_area']);
                $areasArray[] = $area->returnareatoAsArray();
            }

            $returnData = array();
            $returnData['filas_retornadas'] = $rowCount;
            $returnData['areas'] = $areasArray;

            $response = new Response();
            $response->setSuccess(true);
            $response->setHttpStatusCode(200);
            $response->toCache(true);
            $response->setData($returnData);
            $response->send();
            exit;

        }catch(AreaException $ex){
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
        $query = $db->prepare('delete from area where id_area = :idarea');
        $query->bindParam(':idarea', $idarea);
        $query->execute();

        $rowCount = $query->rowCount();

        if($rowCount===0){
            $response = new Response();
            $response->setSuccess(false);
            $response->setHttpStatusCode(404);
            $response->addMessage('Area no encontrada');
            $response->send();
            exit();
        }
        $response = new Response();
        $response->setSuccess(true);
        $response->setHttpStatusCode(200);
        $response->addMessage('Area eliminado');
        $response->send();
        exit();
    }catch(PDOException $ex){
        $response = new Response();
        $response->setSuccess(false);
        $response->setHttpStatusCode(500);
        $response->addMessage('Error eliminando area');
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
        if(!isset($jsonData->nomarea)){
            $response = new Response();
            $response->setSuccess(false);
            $response->setHttpStatusCode(400);
            $response->addMessage('Nombre de area es obligatorio');
            $response->send();
            exit();
        }
        $newarea = new Area(null, $jsonData->nomarea);
        $query = $db->prepare('insert into area (nom_area) values (:nomarea)');
        $query->bindParam(':nomarea', $jsonData->nomarea, PDO::PARAM_STR);
        $query->execute();
        $rowCount = $query->rowCount();
        
        if($rowCount===0){
            $response = new Response();
            $response->setSuccess(false);
            $response->setHttpStatusCode(400);
            $response->addMessage('Falló creación de area');
            $response->send();
            exit();
        }
        $lastIdarea = $db->lastInsertId();

        $response = new Response();
        $response->setSuccess(true);
        $response->setHttpStatusCode(201);
        $response->addMessage('area creado');
        $response->setData($lastIdarea);
        $response->send();
        exit();
    }catch(AreaException $ex){
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