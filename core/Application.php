<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace core;

/**
 * Description of Application
 *
 * @author jlgre_000
 */
class Application {
    

    const APP_NAME = 'SysMoney';
        
    //..ícone da aplicação.
    const APP_ICON = 'core/img/sysmoney.png';

    
    const ICON_NEW = 'core/img/new.png';
    const ICON_SAVE = 'core/img/save.png';
    const ICON_DELETE = 'core/img/delete.png';
    const ICON_SEARCH = 'core/img/search.png';
    const ICON_SUCCESS = 'core/img/success.png';
    const ICON_ERROR = 'core/img/error.png';
    const ICON_WARNING = 'core/img/warning.png';
    const ICON_NOT_FOUND = 'core/img/notfound.png';
    const ICON_EXIT = 'core/img/exit.png';
    const ICON_OPEN = 'core/img/open.png';
    
    const MSG_TITLE_DEFAULT = 'Informação do Sistema';
    
    const MSG_SUCCESS = 'Operação realizada com sucesso!';
    const MSG_ERROR = 'Erro durante a operação...';
    const MSG_NOT_FOUND = 'Objeto não encontrado.';
     
    //..config de email.
    const EMAIL = ''; //..email usado para enviar as msgs
    const EMAIL_NAME = 'SysMoney - Teste'; //..nome que aparecerá ao destinatário
    const EMAIL_PASSWD = ''; //..senha do e-mail
    
    /**
     * Inicia a aplicação - página inicial
     */
    public static function start(){
        (new \app\view\index\Index())->show();
    }
    
    
}
