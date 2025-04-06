<?php

namespace Local\Fwink\Service;

use Local\Fwink\Tables\PortalTable;

class TokenManager
{
    private static $instance;
    private $tokenData;

    private function __clone(){}
    private function __wakeup(){}

    public static function getInstance(): self
    {
        if(!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }


    public function getPublicToken()
    {
        // todo: replace globals
        $tokenData = [
            'domain' => $GLOBALS['FWINK']['DOMAIN'],
            'auth' => $GLOBALS['FWINK']['requestURL']['AUTH_ID']
        ];
        
        $token = base64_encode(json_encode($tokenData));
        $randstring=substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'),1,4);
        
        return \Local\Fwink\SimpleEncDecriptStringToNumber::encrypt($token).$randstring;
    }

    public function checkExternalToken($token)
    {
        $salt = 'app-chart';
        $dbDomain = PortalTable::query()
            ->where('ID', 1) // hardcode
            ->setSelect(['DOMAIN'])
            ->exec();
        $arDomain = $dbDomain->fetch();
        $checkToken = md5($arDomain['DOMAIN'].$salt);
        return $token === $checkToken;
    }
    
    public function decode($token)
    {
        $tokenData = json_decode(base64_decode((new \Local\Fwink\SimpleEncDecriptStringToNumber)->decrypt($token)), true);
        if(is_array($tokenData)) {
            $this->tokenData = $tokenData;
            return true;
        }
        return false;
    }

    public function getDomain()
    {
        return $this->tokenData['domain'];
    }

    public function getAuth()
    {
        return $this->tokenData['auth'];
    }

    private function __construct()
    {
        // todo: replace globals
        $this->tokenData = [
            'domain' => $GLOBALS['FWINK']['DOMAIN'] ?? '',
            'auth' => $GLOBALS['FWINK']['requestURL']['AUTH_ID'] ?? ''
        ];
    }
}