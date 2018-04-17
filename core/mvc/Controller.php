<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace core\mvc;

use core\Application;
use core\dao\Dao;
use core\mvc\view\Message;
use core\util\Session;
use Exception;

/**
 * Controlador genérico. Recebe as requisições do cliente.
 *
 * @author jlgre_000
 */
abstract class Controller {

    protected $view;
    protected $viewList;
    protected $viewRpt;
    
    protected $criteria;
    protected $orderBy;

    /**
     * O modelo que o controller manipulará
     * @var Model
     */
    protected $model;

    /**
     * O objeto DAO que será usado.
     * @var Dao 
     */
    protected $dao;

    /**
     * O array GET
     * @var $_GET
     */
    protected $get;

    /**
     * o array POST
     * @var $_POST
     */
    protected $post;
    protected $request;

    /**
     * Construtor
     */
    public function __construct() {
        $this->post = $_POST;
        $this->get = $_GET;
        $this->request = $_REQUEST;
    }

    /**
     * Pega os dados da model e alimenta a View - abstrato, pois será implementados nos herdeiros.
     */
    public abstract function viewToModel();

    /**
     * Pega os dados da view e insere no Banco de Dados.
     */
    public function insertUpdate() {
        try {
            //..alimenta o model com os dados da View
            $this->viewToModel();
            //..seta o modelo atualizado no objeto DAO            
            $this->dao->setModel($this->model);
            //..invoca o método insertUpdate para persistir o Model
            $id = $this->dao->insertUpdate('seq_users');
            //..cria uma view com mensagem de scuesso.
            //$msg = new Message(Application::MSG_TITLE_DEFAULT, Application::MSG_SUCCESS, Application::ICON_SUCCESS);
            return $id;
        } catch (Exception $ex) {
            throw $ex;
            
        } 
    }

    /**
     * Exclui um objeto do banco de dados mediante um id
     */
    public function delete() {
        try {
            //..alimenta o model com os dados da view
            $this->viewToModel();
            //..invoca o método delete passando por parâmetro o id do modelo
            $this->dao->delete($this->model->getId());
            //..cria uma view de suecesso
            $msg = new Message(Application::MSG_TITLE_DEFAULT, Application::MSG_SUCCESS, Application::ICON_SUCCESS);
        } catch (Exception $ex) {
            //..caso ocorra algum erro, cria uma view de erro
            $msg = new Message(Application::MSG_TITLE_DEFAULT, Application::MSG_ERROR, Application::ICON_ERROR);
        } finally {
            //..exibie a view criada.
            $msg->show();
        }
    }

    /**
     * Exibe a view.
     */
    public function showView() {
        try {            
            //..verifica se há algum parâmetro no get
            if ($this->get) {                
                if (isset($this->get['id'])) { //..verifica se existe uma variável id no get                    
                    $id = $this->get['id']; //..pega o id 
                    //..recupera o modelo fazendo uma consulta no bando por id
                    $this->model = $this->dao->findById($id);                    
                }
            }
            //..se recuperou um model, então...
            if ($this->model) {                
                $this->view->setModel($this->model);
            } else {
                //..senão cria a view com mensagem de não encontrado
                $this->view = new Message(Application::MSG_TITLE_DEFAULT, Application::MSG_NOT_FOUND, Application::ICON_NOT_FOUND);
            }
        } catch (Exception $ex) {
            //..se acontecer algum problema, cria uma view com mensagem de erro
            $this->view = new Message(Application::MSG_TITLE_DEFAULT, Application::MSG_ERROR, Application::ICON_ERROR);
        } finally {
            //..exibe a view criada.
            $this->view->show();
        }
    }

    public function showViewRpt(){
        $this->viewRpt->show();
    }

    /**
     * O método run é executado sempre que uma requisição for feita via post. Verifica-se o parâmetro passado e toma-se a decisão:
     */
    public function run() {
        //..pega o valor da variável comando que vem por post.
        $command = strtolower($this->post['command']);
        //..verifica o valor e invoca os métodos corretos.
        switch ($command) {
            case 'salvar':
                $this->insertUpdate();
                break;
            case 'excluir':
                $this->delete();
                break;
            case 'novo':
                $this->showView();
                break;
            case 'listar':
                $this->showList();
                break;
            default:
                break;
        }
    }

    /**
     * Exibe a view de listagem (ou pesquisa)
     */
    public function showList() {
        //var_dump($this->post);
        if (isset($this->post['page']) || isset($this->get['page'])) {
            if ($this->post) {                
                $this->doListing($this->criteria, $this->orderBy);
            }
            if ($this->get) {
                $this->doPagination();
            }
        } else {
            //..destrói todas as sessões gravadas e cria uma view limpa.
            Session::destroySession('sqlData');
            Session::destroySession('criteria');
            Session::destroySession('orderBy');
            Session::destroySession('limit');
            Session::destroySession('lastPage');
            $this->viewList->show();
        }        
    }

    private function doListing($criteria, $orderBy) {
        //..pega os dados de pesquisa vindos do form
        $sqlData = null;
        foreach ($this->post['data'] as $datum) {
            $sqlData[] = $datum;
        }        
        //..grava sessões com os dados da consulta para preservar os dados no form e fazer as navegaçao por página,                
        Session::createSession('sqlData', $sqlData); //..os dados da pesquisa
        //..limite de registros por página
        $limit = $this->post['limit'];

        //..grava uma sessão com o critério
        Session::createSession('criteria', $criteria);
        //..cria um dao e verifica a qtde registros que a query irá retornar
        //..a variável count grava a qtde de registros que a query retorna.        
        $count = $this->dao->selectCount($criteria);
        
        //..grava uma sessão com a ordenação dos registros
        Session::createSession('orderBy', $orderBy);

        //..grava uma sessão com a qtde de registros por página
        Session::createSession('limit', $limit);
        //..grava uma sessão com a última página:
        //..divide a qtde de registros pelo limite e arredonda para cima.
        
        Session::createSession('lastPage', ceil($count / $limit));
    }

    private function doPagination() {
        //..a navegação por página envolve requisições via get 
        //..em que serão envidadas o número da página que se deseja navegar, então...                    
        //..pega o nº da página que foi requisitada e grava numa sessão        
        $currentPage = $this->get['page'];
        //..paga o limite, isto é, qtde máxima de registros por página que está gravado em uma sessão
        $limit = Session::getSession('limit');
        //..calcula qual é o índice dos registros para ser exibido (offset)
        $offSet = $currentPage == 1 ? 0 : ($currentPage - 1) * Session::getSession('limit');
        //..calcula a próxima página:
        $lastPage = Session::getSession('lastPage');
        $nextPage = $currentPage + 1 < $lastPage ? $currentPage + 1 : $lastPage;
        //..calcula a página anterior:              
        $previousPage = $currentPage <= 1 ? 1 : $currentPage - 1;
        //..faz a consulta passando o critério, o limite dos dados e o offset
        $data = $this->dao->selectAll(Session::getSession('criteria'), 
                        Session::getSession('orderBy'), null, $limit, $offSet);
        //..cria a view com os parâmetros de paginação. 
        $this->viewList->setModel($data);        
        $this->viewList->setSqlData(Session::getSession('sqlData'));
        $this->viewList->setRegPerPage($limit);
        $this->viewList->setCurrentPage($currentPage);
        $this->viewList->setPreviousPage($previousPage);
        $this->viewList->setNextPage($nextPage);
        $this->viewList->setLastPage($lastPage);
        $this->viewList->show();
    }
    
    

}
