<?php

use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require __DIR__ . '/init.php';

$ca = new ClientArea();

$ca->setPageTitle($_LANG['ghosting']['text']['about_us']['title']);

$ca->addToBreadCrumb('index.php', Lang::trans('globalsystemname'));
$ca->addToBreadCrumb('about_us.php', $_LANG['ghosting']['text']['about_us']['title']);

$ca->initPage();
Menu::addContext();



$ca->setTemplate('about_us');

$ca->output();