<?php
define("NOT_CHECK_PERMISSIONS",true);
use Bitrix\Main\Loader;
use Bitrix\Main\Routing\RoutingConfigurator;
use Bitrix\Main\Web\Json;

Loader::includeModule('local.fwink');

return function (RoutingConfigurator $routes) {
    $routes->prefix('local/apps/api/chart')->group(function (RoutingConfigurator $routes) {
        $routes->post('post', [\Local\Fwink\Controller\Post::class, 'externalList']);
        $routes->post('department', [\Local\Fwink\Controller\Block::class, 'externalList']);
        $routes->post('structure', [\Local\Fwink\Controller\Block::class, 'externalStructure']);
    });
};