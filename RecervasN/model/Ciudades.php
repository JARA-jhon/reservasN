<?php

class ciudadesException extends Exception{}

class ciudades{
    private $_idciudades;
    private $_nomciudades;

    //Constructor
    public function __construct($idciudades, $nomciudades){
        $this->_idciudades = $idciudades;
        $this->_nomciudades = $nomciudades;
    }

    public function getIdciudades(){
        return $this->_idciudades;
    }

    public function setIdciudades($idciudades){
        if($idciudades !== null && !is_numeric($idciudades)){
            throw new ciudadesException("Error en Id de ciudad");
        }
        $this->_idciudades = $idciudades;
    }

    public function getNomciudades(){
        return $this->_nomciudades;
    }

    public function setNomciudades($nomciudades){
        if($nomciudades !== null && strlen($nomciudades)>50){
            throw new ciudadesException("Error en nombre de ciudades");
        }
        $this->_nomciudades = $nomciudades;
    }

    public function returnciudadesAsArray(){
        $ciudades = array();
        $ciudades['idciudades'] = $this->getIdciudades();
        $ciudades['nomciudades'] = $this->getNomciudades();
        return $ciudades;
    }

}