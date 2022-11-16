<?php

namespace MinMicro\Models;

use Exception;

class Footer
{

    const DEFAULT_CREDITS = "© %s - Todos os direitos reservados <br><br>Desenvolvimento do Website - Tatiana P.A. Barbosa";

    private $logoUrl;
    private $credits;

    public function __construct($config)
    {
        $this->logoUrl = $config['BASE_URL'].$config['SITE']['LOGO_URL'];
        $this->credits = sprintf(self::DEFAULT_CREDITS, date('Y'));
    }

    public function __get($param)
    {
        switch ($param) {
            case 'logoUrl':
                return $this->logoUrl;
            case 'credits':
                return $this->credits;
            default:
                throw new Exception("ERROR: O parâmetro de Footer: '{$param}' não existe");
        }
    }

    public function __isset($name)
    {
        return isset($this->$name);
    }

    public function setLogoUrl($logoUrl)
    {
        $this->logoUrl = $logoUrl;
        return $this;
    }

    public function setCredits($credits)
    {
        $this->credits = $credits;
        return $this;
    }
}
