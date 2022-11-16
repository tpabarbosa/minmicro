<?php

namespace MinMicro\Models;

use Exception;
use MinMicro\Models\Link;

class Site
{

    // const LOGO_URL = 'https://www.ufrgs.br/minmicro/logo.jpg';
    // const NAVBAR_LINKS = ["About" => [], "Minerals" => [], "Contact" => []];

    private $baseUrl;
    private $name;
    private $logoUrl;
    private $navbarLinks = [];
    private $locale;
    private $langName;
    private $langAlias;

    public function __construct($locale, $config)
    {
        $this->locale = $locale;
        $this->langAlias = $locale->getAlias();
        $this->langName = $locale->getName();
        $this->baseUrl = $locale->getLocalePathOrUrl($config['BASE_URL']);
        $this->name = $locale->getText('site.name');
        $this->logoUrl = $config['SITE']['LOGO_URL'];
        $this->withNavbarLinks($config['SITE']['NAVBAR_LINKS'], $this->baseUrl);
    }

    public function __get($param)
    {
        switch ($param) {
            case 'baseUrl':
                return $this->baseUrl;
            case 'name':
                return $this->name;
            case 'logoUrl':
                return $this->logoUrl;
            case 'navbarLinks':
                return $this->navbarLinks;
            case 'langAlias':
                return $this->langAlias;
            case 'langName':
                return $this->langName;
            default:
                throw new Exception("ERROR: O parâmetro de Site: '{$param}' não existe");
        }
    }

    public function __isset($name)
    {
        return isset($this->$name);
    }

    public function withNavbarLinks($links = [])
    {

        $links = array_map(function ($key, $value) {
            $routeKey = strtolower($key);
            return [
                'url' =>  $this->locale->getText("route.{$routeKey}"),
                'text' => $this->locale->getText($key),
                'icon' => $value['icon'] ?? '',
                'active' => $value['active'] ?? ''
            ];
        }, array_keys($links), array_values($links));


        $links = Link::createLinks($links, $this->baseUrl);
        $this->navbarLinks = $links;

        return $this;
    }
}
