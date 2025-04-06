<?php

namespace Local\Fwink;

class Page implements \Bitrix\Main\Engine\Response\ContentArea\ContentAreaInterface
{
    protected $template;

    public function __construct($template = '')
    {
        $this->template = $template;
    }

    public function setTemplate($template)
    {
        $this->template = $template;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function getHtml()
    {
        $html = '';
        if(!empty($this->template)) {
            if (file_exists(__DIR__ . '/templates/' . $this->template . '.mustache')) {
                $html = file_get_contents(__DIR__ . '/templates/' . $this->template . '.mustache');
            }
        }
        return $html;
    }
}
