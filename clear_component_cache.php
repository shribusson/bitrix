<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

global $USER_FIELD_MANAGER;

// Чистим весь кэш userfields
$USER_FIELD_MANAGER->CleanCache();
\Bitrix\Main\Application::getInstance()->getManagedCache()->cleanDir('userfield');

// Очистка HTML-кэша
if (defined("BX_COMP_MANAGED_CACHE")) {
    $GLOBALS["CACHE_MANAGER"]->CleanDir();
}

// Очистка файлового кэша
$cache = \Bitrix\Main\Data\Cache::createInstance();
$cache->cleanDir();

// Дополнительно
Bitrix\Main\Data\Cache::clearCache(true);

// Ответ
echo "Все типы кэша пользовательских полей очищены.";

