<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '../../../bootstrap.php';

use NFePHP\EFDReinf\Event;
use NFePHP\Common\Certificate;
use JsonSchema\Validator;

$config = [
    'tpAmb' => 3, //tipo de ambiente 1 - Produção; 2 - Produção restrita - dados reais;3 - Produção restrita - dados fictícios.
    'verProc' => '0_1_2', //Versão do processo de emissão do evento. Informar a versão do aplicativo emissor do evento.
    'eventoVersion' => '1_02_00', //versão do layout do evento
    'serviceVersion' => '1_02_00',//versão do webservice
    'empregador' => [
        'tpInsc' => 1,  //1-CNPJ, 2-CPF
        'nrInsc' => '99999999', //numero do documento
        'nmRazao' => 'Razao Social'
    ],    
    'transmissor' => [
        'tpInsc' => 1,  //1-CNPJ, 2-CPF
        'nrInsc' => '99999999999999' //numero do documento
    ]
];
$configJson = json_encode($config, JSON_PRETTY_PRINT);

$std = new \stdClass();
$std->sequencial = 1;
$std->indretif = 1;
$std->nrrecibo = '737373737373737';
$std->perapur = '2017-11';

$std->ideestabobra = new \stdClass();
$std->ideestabobra->tpinscestab = 1;
$std->ideestabobra->nrinscestab = '12345678901234';
$std->ideestabobra->indobra = '0';
$std->ideestabobra->cnpjprestador = '12345678901234';
$std->ideestabobra->vlrtotalbruto = 456.92;
$std->ideestabobra->vlrtotalbaseret = 45.80;
$std->ideestabobra->vlrtotalretprinc = 5;
$std->ideestabobra->vlrtotalretadic = 54.35;
$std->ideestabobra->vlrtotalnretprinc = 345.66;
$std->ideestabobra->vlrtotalnretadic = 345.90;
$std->ideestabobra->indcprb = 0;

$std->ideestabobra->nfs[0] = new \stdClass();
$std->ideestabobra->nfs[0]->serie = '001';
$std->ideestabobra->nfs[0]->numdocto = '265465';
$std->ideestabobra->nfs[0]->dtemissaonf = '2017-01-22';
$std->ideestabobra->nfs[0]->vlrbruto = 200.00;
$std->ideestabobra->nfs[0]->obs = 'observacao pode ser nula';

$std->ideestabobra->nfs[0]->infotpserv[0] = new \stdClass();
$std->ideestabobra->nfs[0]->infotpserv[0]->tpservico = '123456789';
$std->ideestabobra->nfs[0]->infotpserv[0]->vlrbaseret = 234.90;
$std->ideestabobra->nfs[0]->infotpserv[0]->vlrretencao = 12.75;
$std->ideestabobra->nfs[0]->infotpserv[0]->vlrretsub = 34.55;
$std->ideestabobra->nfs[0]->infotpserv[0]->vlrnretprinc = 2345.75;
$std->ideestabobra->nfs[0]->infotpserv[0]->vlrservicos15 = 22;
$std->ideestabobra->nfs[0]->infotpserv[0]->vlrservicos20 = 33;
$std->ideestabobra->nfs[0]->infotpserv[0]->vlrservicos25 = 44;
$std->ideestabobra->nfs[0]->infotpserv[0]->vlradicional = 5;
$std->ideestabobra->nfs[0]->infotpserv[0]->vlrnretadic = 1.55;

$std->ideestabobra->infoprocretpr[0] = new \stdClass();
$std->ideestabobra->infoprocretpr[0]->tpprocretprinc = 1;
$std->ideestabobra->infoprocretpr[0]->nrprocretprinc = 'ZYX987';
$std->ideestabobra->infoprocretpr[0]->codsuspprinc = '12345678901234';
$std->ideestabobra->infoprocretpr[0]->valorprinc = 200.98;

$std->ideestabobra->infoprocretad[0] = new \stdClass();
$std->ideestabobra->infoprocretad[0]->tpprocretadic = 1;
$std->ideestabobra->infoprocretad[0]->nrprocretadic = 'ACB21';
$std->ideestabobra->infoprocretad[0]->codsuspadic = '12345678901234';
$std->ideestabobra->infoprocretad[0]->valoradic = 1000.23;

try {
    
   //carrega a classe responsavel por lidar com os certificados
    $content     = file_get_contents('expired_certificate.pfx');
    $password    = 'associacao';
    $certificate = Certificate::readPfx($content, $password);
    
    //cria o evento e retorna o XML assinado
    $xml = Event::evtServTom(
        $configJson,
        $std,
        $certificate,
        '2017-08-03 10:37:00'
    )->toXml();
    
    //$xml = Evento::r2010($json, $std, $certificate)->toXML();
    //$json = Event::evtServTom($configjson, $std, $certificate)->toJson();
    
    header('Content-type: text/xml; charset=UTF-8');
    echo $xml;
    
} catch (\Exception $e) {
    echo $e->getMessage();
}