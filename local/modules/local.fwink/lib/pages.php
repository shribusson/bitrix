<?php

namespace Local\Fwink;

class Pages
{
	public function __construct(){}

	public function exec()
	{
        $boolExcept=false;
        if($GLOBALS['FWINK']['requestURL']['page']=='settings'){
            $boolExcept=true;
        }

        global $APPLICATION;
        $APPLICATION->ShowHead();

        \CUtil::InitJSCore(['ajax']);

        $APPLICATION->IncludeComponent(
            'local:fwink.pages',
            '',
            [
                'COMPONENT_TEMPLATE' => '',
                'SEF_URL_TEMPLATES' => [
                    'list' => '',
                    'detail' => '#ELEMENT_ID#/',
                    'add' => 'add/'
                ],
                'SEF_MODE' => 'Y'
            ]
        );
	}
}
