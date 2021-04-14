<?php

use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require __DIR__ . '/init.php';

$document_type = 'terms';
$title = $_LANG['ghosting']['text']['terms'];
if(isset($_GET['policy']))
{
    $document_type = 'policy';
    $title = $_LANG['ghosting']['text']['policy'];
}
if(isset($_GET['contract']))
{
    $document_type = 'contract';
    $title = $_LANG['ghosting']['text']['contract'];
}
    
    
$ca = new ClientArea();

$ca->setPageTitle($title);

$ca->addToBreadCrumb('index.php', Lang::trans('globalsystemname'));
$ca->addToBreadCrumb('document.php', $title);

$ca->initPage();
Menu::addContext();


$ca->assign('document_type', $document_type);
$ca->setTemplate('document');

$ca->output();