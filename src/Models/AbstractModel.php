<?php

namespace MinMicro\Models;

use Exception;

abstract class AbstractModel
{
    protected $PUBLIC_PROPERTIES = [];

    public function __isset($name)
    {
        return $this->magicCb('isset', $name);
    }

    public function __get($name)
    {
        return $this->magicCb('get', $name);
    }

    private function magicCb($type, $name)
    {
        $class = get_class($this);
        if (!$this->PUBLIC_PROPERTIES) {
            throw new Exception("ERROR: A classe '{$class}' deve definir uma propriedade: 'PUBLIC_PROPERTIES' com um array de propriedades públicas.");
        }

        foreach ($this->PUBLIC_PROPERTIES as $property) {
            if ($property === $name) {
                return $type === 'isset' ? true : $this->$property;
            }
            if (strpos($property, '.*') !== false) {
                $prop = str_replace('.*', '', $property);
                if (isset($this->$prop[$name])) {
                    return $type === 'isset' ? true : $this->$prop[$name];
                }
            }
        }

        return $type === 'isset' ? false : null;
        // throw new Exception("ERROR: O parâmetro '{$name}' de '{$class}' não existe");
    }
}
