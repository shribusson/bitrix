<?php


namespace Local\Fwink;


class Request
{
	const ACTION_EVENT = 'event';

	public $token;
	public $requestDomain;

	public $DOMAIN;
	public $PROTOCOL;
	public $LANG;
	public $APP_SID;
	public $AUTH_ID;
	public $AUTH_EXPIRES;
	public $REFRESH_ID;
	public $member_id;
	public $PLACEMENT;
	public $PLACEMENT_OPTIONS;
	public $auth;
	public $event;
	public $data;
	public $mode;

	public function __construct()
	{
		$request=\Bitrix\Main\Application::getInstance()->getContext()->getRequest();
		foreach($request->getValues() as $key=>$item) {
			if(property_exists($this, $key)) {
				$this->$key = $item;
			}
		}
		$this->init();
	}

	public function init()
	{
		if(!empty($this->AUTH_ID)) {
			$this->token = $this->AUTH_ID;
		} elseif(!empty($this->auth) && !empty($this->auth['access_token'])) {
			$this->token = $this->auth['access_token'];
		}

		if(!empty($this->DOMAIN)) {
			$this->requestDomain = $this->DOMAIN;
		} elseif(!empty($this->auth) && !empty($this->auth['domain'])) {
			$this->requestDomain = $this->auth['domain'];
		}
	}

	public function check()
	{
		if(empty($this->token) || empty($this->requestDomain)) {
			return false;
		}
		if(!empty($this->action) && $this->action === self::ACTION_EVENT) {
			if(empty($this->event)) {
				return false;
			}
		}

		return true;
	}
}
