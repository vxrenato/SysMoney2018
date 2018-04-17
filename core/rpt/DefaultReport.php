<?php
namespace core\rpt;

require_once('core/vendor/tcpdf/tcpdf.php');

use TCPDF;
use core\Application;

abstract class DefaultReport extends TCPDF
{

    protected $data; //..dados do relatório;

    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false, $pdfa = false)
    {
        parent::__construct($orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false, $pdfa = false);    
        $this->SetMargins(20,20);
        $this->setHeaderMargin(5);
    }

    public function Header()
    {
        $this->Image(Application::APP_ICON, 15, 5, 15, 0, 'PNG');
        $this->SetFont('helvetica', 'B', 20);
        $this->SetX(35);
        $this->Write(7, Application::APP_NAME);
    }


    public function Footer()
    {
        $this->SetY(-10);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 2, date('d/m/Y H:i') . " - Página {$this->getAliasNumPage()}"
            . " de {$this->getAliasNbPages()}.", 'T R', true, 'R');
    }

    //..desenhar o cabeçalho da tabela de exibição do relatório.
    public abstract function tableHeader();
    
        //..criar o corpo do relatório
    public abstract function body();

    public function Output($name = 'doc.pdf', $dest = 'I')
    {
        $this->AddPage();
        $this->tableHeader();
        $this->body();
        ob_start();
        parent::Output($name, $dest);
        ob_end_flush();
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data){
        $this->data = $data;
    }

}