<?php

class sucursalesException extends Exception{}

class sucursales{
    private $_idsucursales;
    private $_nomsucursales;

    //Constructor
    public function __construct($idsucursales, $nomsucursales){
        $this->_idsucursales = $idsucursales;
        $this->_nomsucursales = $nomsucursales;
    }

    public function getIdsucursales(){
        return $this->_idsucursales;
    }

    public function setIdsucursales($idsucursales){
        if($idsucursales !== null && !is_numeric($idsucursales)){
            throw new sucursalesException("Error en Id de sucursal");
        }
        $this->_idsucursales = $idsucursales;
    }

    public function getNomsucursales(){
        return $this->_nomsucursales;
    }

    public function setNomsucursales($nomsucursales){
        if($nomsucursales !== null && strlen($nomsucursales)>50){
            throw new sucursalesException("Error en nombre de sucursal");
        }
        $this->_nomsucursales = $nomsucursales;
    }

    public function returnsucursalesAsArray(){
        $sucursales = array();
        $sucursales['idsucursales'] = $this->getIdsucursales();
        $sucursales['nomsucursales'] = $this->getNomsucursales();
        return $sucursales;
    }

}