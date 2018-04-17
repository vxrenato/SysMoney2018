<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace core\mvc;

/**
 * Description of Model
 *
 * @author jlgregorio81
 */
abstract class Model {
    //put your code here
    protected $id;
    
    public function __construct($id = null) {
        $this->id = $id;
    }
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public abstract function show();
    
}
