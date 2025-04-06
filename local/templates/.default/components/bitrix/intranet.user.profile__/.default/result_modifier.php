<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
    die();
}

$this->__folder = sprintf('%s/templates/%s', $this->__component->__path, $this->__name);
$this->__file = sprintf('%s/templates/%s/template.php', $this->__component->__path, $this->__name);
$this->IncludeLangFile();
$this->__hasCSS = true;
$this->__hasJS = true;

$this->addExternalJs('/local/js/itrack.custom/intranet.user.profile.ext/script.js');
$this->addExternalCss('/local/js/itrack.custom/intranet.user.profile.ext/style.css');
\Bitrix\Main\UI\Extension::load(['ui.mustache']);
print '<input type="hidden" id="ic_iupe_userid" value="'.$arResult["User"]["ID"].'" />';
//print $this->__folder;
include($_SERVER['DOCUMENT_ROOT'].$this->__folder.'/result_modifier.php');