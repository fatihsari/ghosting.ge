<?php

use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require __DIR__ . '/init.php';

$ca = new ClientArea();
$ca->setPageTitle($_LANG['ghosting']['text']['pro_service']['title']);

$ca->addToBreadCrumb('index.php', Lang::trans('globalsystemname'));
$ca->addToBreadCrumb('custom_service.php', $_LANG['ghosting']['text']['pro_service']['title']);

$ca->initPage();
Menu::addContext();



$ca->setTemplate('custom_service');

$ca->output();