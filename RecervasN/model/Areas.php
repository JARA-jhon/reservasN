<?php

class AreaException extends Exception{}

class Area{
    private $_idarea;
    private $_nomarea;

    //Constructor
    public function __construct($idarea, $nomarea){
        $this->_idarea = $idarea;
        $this->_nomarea = $nomarea;
    }

    public function getIdarea(){
        return $this->_idarea;
    }

    public function setIdarea($idarea){
        if($idarea !== null && !is_numeric($idarea)){
            throw new AreaException("Error en Id de area");
        }
        $this->_idarea = $idarea;
    }

    public function getNomarea(){
        return $this->_nomarea;
    }

    public function setNomarea($nomarea){
        if($nomarea !== null && strlen($nomarea)>50){
            throw new AreaException("Error en nombre de area");
        }
        $this->_nomarea = $nomarea;
    }

    public function returnareaAsArray(){
        $area = array();
        $area['idarea'] = $this->getIdarea();
        $area['nomarea'] = $this->getNomarea();
        return $area;
    }

}