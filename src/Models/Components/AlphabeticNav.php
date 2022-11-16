<?php

namespace MinMicro\Models\Components;

class AlphabeticNav
{
    private $subtitle;
    private $folder;

    public function __construct($locale)
    {
        $this->subtitle = $locale->getText('component.alphabetic.subtitle');
        $this->folder = $locale->getLocaleRouteUrl('route.minerals');
    }

    public function __get($param)
    {
        switch ($param) {
            case 'subtitle':
                return $this->subtitle;
            case 'folder':
                return $this->folder;
            default:
                throw new \Exception("ERROR: O parâmetro de AlphabeticNav: '{$param}' não existe");
        }
    }

    public function __isset($name)
    {
        return isset($this->$name);
    }
}
