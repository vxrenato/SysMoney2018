<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace core\mvc\view;

/**
 * Página HTML para exibir mensagens ao usuário.
 *
 * @author jlgre_000
 */
class Message extends HtmlPage {
    
    /**
     * A mensagem que será exibida
     * @var string 
     */
    private $title;
    private $message;
    private $icon;
    
    /**
     * Construtor
     * @param string $message A mensagem que será exibida. Recomenda-se usar as constantes definidas em \core\Application.
     */
    function __construct($title, $message, $icon) {
        $this->title = $title;
        $this->message = $message;
        $this->htmlFile = 'message.phtml';
        $this->icon = $icon;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getMessage() {
        return $this->message;
    }

    public function getIcon() {
        return $this->icon;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    public function setIcon($icon) {
        $this->icon = $icon;
    }


        
 

//put your code here
}
