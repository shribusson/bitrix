<?php

namespace Local\Fwink\Controller\ActionFilter;

use Bitrix\Main\Context;
use Bitrix\Main\Engine\ActionFilter\Base;
use Bitrix\Main\Error;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Local\Fwink\Service\TokenManager;
use Local\Fwink\Tables\PortalTable;

class Domain extends Base
{
    public function onBeforeAction(Event $event)
    {
        $sign = Context::getCurrent()->getRequest()->get('sign');
        $found = false;
        if(!empty($sign)) {
            $tokenManager = TokenManager::getInstance();
            if($tokenManager->decode($sign)) {
                $domain = $tokenManager->getDomain();
                if (!empty($domain)) {
                    // todo: как и везде вынести в отдельное место
                    $_SERVER_SERVER_NAME = $_SERVER['SERVER_NAME'];
                    $arPortal = PortalTable::query()
                        ->setSelect(['ID', 'DOMAIN'])
                        ->setFilter(["source" => $_SERVER_SERVER_NAME, "consumer" => $domain])
                        ->fetch();
                    if ((int)$arPortal['ID'] > 0) {
                        $GLOBALS['FWINK']['ID_PORTAL'] = $arPortal['ID'];
                        $GLOBALS['FWINK']['DOMAIN'] = $arPortal['DOMAIN'];
                        $found = true;
                    }
                }
            }
        }
        if(!$found){
            $this->addError(new Error('wrong sign'));
        }

        if(!$this->errorCollection->isEmpty()) {
            Context::getCurrent()->getResponse()->setStatus(401);
            return new EventResult(EventResult::ERROR, null, null, $this);
        }

        return null;
    }
}