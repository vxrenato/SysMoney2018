<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace core\mvc\view;

/**
 * Description of HtmlPage
 *
 * @author jlgregorio81
 */
abstract class HtmlPage {

    protected $model;
    protected $htmlFile = null;
    //..dados de paginação
    protected $sqlData;
    protected $regPerPage;
    protected $currentPage;
    protected $previousPage;
    protected $nextPage;
    protected $lastPage;

    public function __construct($model = null, $sqlData = null, $regPerPage = null, $currentPage = null, $previousPage = null, $nextPage = null, $lastPage = null) {
        $this->model = $model;
        $this->sqlData = $sqlData;
        $this->regPerPage = $regPerPage;
        $this->previousPage = $previousPage;
        $this->currentPage = $currentPage;
        $this->nextPage = $nextPage;
        $this->lastPage = $lastPage;
    }

    /**
     * Método para desenhar o topo da página - definido pelo arquivo top.phtml.
     */
    protected function drawHeader() {
        require_once 'header.phtml';
    }

    /**
     * Método para desenhar o rodapé da página - definido pelo arquivo bottom.phtml.
     */
    protected function drawFooter() {
        require_once 'footer.phtml';
    }

    public function show() {
        $this->drawHeader();
        require_once $this->htmlFile;
        $this->drawFooter();
    }

    public function getModel() {
        return $this->model;
    }

    public function setModel($model) {
        $this->model = $model;
    }

    public function getHtmlFile() {
        return $this->htmlFile;
    }

    public function getSqlData() {
        return $this->sqlData;
    }

    public function getRegPorPag() {
        return $this->regPerPage;
    }

    public function getCurrentPage() {
        return $this->currentPage;
    }

    public function getPreviousPage() {
        return $this->previousPage;
    }

    public function getNextPage() {
        return $this->nextPage;
    }

    public function getLastPage() {
        return $this->lastPage;
    }

    public function setHtmlFile($htmlFile) {
        $this->htmlFile = $htmlFile;
    }

    public function setSqlData($dadosSql) {
        $this->sqlData = $dadosSql;
    }

    public function setRegPerPage($regPorPag) {
        $this->regPerPage = $regPorPag;
    }

    public function setCurrentPage($pagAtual) {
        $this->currentPage = $pagAtual;
    }

    public function setPreviousPage($pagAnterior) {
        $this->previousPage = $pagAnterior;
    }

    public function setNextPage($pagProxima) {
        $this->nextPage = $pagProxima;
    }

    public function setLastPage($pagUltima) {
        $this->lastPage = $pagUltima;
    }

    /**
     * 
     * @param array $header Um array de strings com os nomes dos campos a ser exibido no cabeçalho     
     * @param array $arrayGetters Um array de strings com os nomes dos métodos gets para pegar os respectivos dados do cabeçalho
     * @param array $arrayObj Um array de objetos com os models que serão usados para criar a tabela/lista
     * @param string $controller o nome do controlador para criar o link de recuperação de um objeto único
     */
    public function createList($arrayObj, $header, $arrayGetters, $controller) {
        //..se houver array de objetos, então...
        if ($arrayObj) {
            echo "<table border=\"1\">";
            echo "<tr>";
            //..cria o cabeçalho percorrendo o array header e criando as células.
            foreach ($header as $field) {
                echo "<th>$field</th>";
            }
            echo "<th>Editar/Excluir</th>";
            echo "</tr>";
            foreach ($arrayObj as $obj) {
                echo "<tr>";
                foreach ($arrayGetters as $getter) {
                    echo "<td>";
                    if (method_exists($obj, $getter)) {
                        echo call_user_func(array($obj, $getter));                       
                    } else {                        
                        echo "<pre style=\"color:red\">Objeto ou método inválido!</pre>";
                    }
                    echo "</td>";
                }
                echo "<td><a href=\"Request.php?class={$controller}&method=showView&id={$obj->getId()}\">" .
                "<img style=\"margin-left: 50%;\" src=\"" . \core\Application::ICON_OPEN . "\">"
                . "</a></td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        $this->objectNotFound();
        $this->createPagination($controller);
    }

    public function objectNotFound() {
        if (!$this->model && $_POST) {
            echo "<img class=\"myicon float-left\" src=\"" . \core\Application::ICON_NOT_FOUND . "\">";
            echo "<h1><small>" . \core\Application::MSG_NOT_FOUND . "</small></h1>";
        }
    }

    public function createPagination($controller) {
        if ($this->model) {
            echo "<div>";
            echo "<ul>";
            echo "<li><a href=\"Request.php?class=$controller&method=showList&page=1\">Primeiro</a></li>";
            echo "<li><a href=\"Request.php?class=$controller&method=showList&page={$this->previousPage}\">Anterior</a></li>";
            echo "<li><strong>Página {$this->currentPage} de {$this->lastPage}</strong></li>";
            echo "<li><a href=\"Request.php?class=$controller&method=showList&page={$this->nextPage}\">Próximo</a></li>";
            echo "<li><a href=\"Request.php?class=$controller&method=showList&page={$this->lastPage}\">Último</a></li>";
            echo "<ul>";
            echo "<div>";
        }
    }

}
