<?php
//error_reporting(E_ALL & ~E_NOTICE);
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define('NO_AGENT_CHECK', true);
define("STATISTIC_SKIP_ACTIVITY_CHECK", true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if (!class_exists('LocalFwink')) {
    \Bitrix\Main\Loader::includeModule('local.fwink');
}

$app = new LocalFwink();
$app->exec();

exit();