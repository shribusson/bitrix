<?php

namespace Local\Fwink\Controller;

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Error;
use Bitrix\Main\Loader;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Web\Json;
use Bitrix\Main\Config\Option;
use Local\Fwink\Controller\ActionFilter\Domain;

class Settings extends Controller
{
    protected function getDefaultPreFilters()
    {
        return [
            new ActionFilter\HttpMethod(
                array(ActionFilter\HttpMethod::METHOD_GET, ActionFilter\HttpMethod::METHOD_POST)
            ),
            new ActionFilter\Csrf(),
            new Domain()
        ];
    }

    public function configureActions()
    {
        return [
            'save' => []
        ];
    }

    public function saveAction(array $settings)
    {
        foreach ($settings as $param => $value) {
            if($param === 'usersCanEdit') {
                if(!empty($value)) {
                    $saveValue = implode(',',$value);
                } else {
                    $saveValue = '';
                }
                Option::set('local.fwink','users_can_edit', $saveValue);
            }
        }
    }
}