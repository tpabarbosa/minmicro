<?php

namespace MinMicro\Models;

use Exception;

class Link extends AbstractModel
{
    protected $url;
    protected $text;
    protected $icon;
    protected $active;

    const PUBLIC_PROPERTIES = ['url', 'text', 'icon', 'active'];

    public function __construct($text, $url, $icon = '', $active = false)
    {
        $this->PUBLIC_PROPERTIES = self::PUBLIC_PROPERTIES;
        $this->url = $url;
        $this->text = $text;
        $this->icon = $icon;
        $this->active = $active;
    }

    public function setActive($value)
    {
        $this->active = $value;
        return $this;
    }

    public static function createLinks($links = [], $baseUrl = '')
    {
        $l = [];

        foreach ($links as $link) {
            $l[] = new Link(
                $link['text'],
                $link['url'] !== null ? $baseUrl . '/' . $link['url'] : '',
                $link['icon'] ?? '',
                $link['active'] ?? false
            );
        }
        return $l;
    }
}
