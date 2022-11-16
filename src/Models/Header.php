<?php

namespace MinMicro\Models;

use Exception;
use MinMicro\Models\Link;

class Header extends AbstractModel
{
    const STYLES = ['small', 'big', 'none'];
    const STYLE_DEFAULT = 'big';

    const PUBLIC_PROPERTIES = ['title.*', 'breadcrumbLinks'];

    protected $breadcrumbLinks = [];
    protected $defaultImageUrl;
    protected $defaultImageAlt;
    protected $title;
    protected $defaultText;

    public function __construct($locale, $config)
    {
        $this->PUBLIC_PROPERTIES = self::PUBLIC_PROPERTIES;

        $this->baseUrl = $locale->getLocalePathOrUrl($config['BASE_URL']);
        $this->title['style'] = self::STYLE_DEFAULT;

        $this->defaultText = $locale->getText('default.header.text');
        $this->title['text'] = $this->defaultText;

        $this->defaultImageUrl = $config['SITE']['DEFAULT_HEADER_IMAGE_URL'];
        $this->title['imageUrl'] = $this->defaultImageUrl;

        $this->defaultImageAlt = $locale->getText('default.header.image.alt');
        $this->title['imageAlt'] = $this->defaultImageAlt;
    }

    public function setStyle($style = null)
    {

        if ($style && !in_array($style, self::STYLES)) {
            throw new Exception("ERROR: O estilo '{$style}' não existe em Header");
        }

        $this->title['style'] = $style ?? self::STYLE_DEFAULT;
        return $this;
    }

    public function setText($text = null)
    {
        $this->text = $text;
        $this->title['text'] = $text ?? $this->defaultText;
        return $this;
    }

    public function setImageUrl($imageUrl = null)
    {
        $this->title['imageUrl'] = $imageUrl ?? $this->defaultImageUrl;
        return $this;
    }

    public function setImageAlt($imageAlt = null)
    {
        $this->title['imageAlt'] = $imageAlt ?? $this->defaultImageAlt;
        return $this;
    }

    public function withBreadcrumbLinks($links = [])
    {
        $links = Link::createLinks($links, $this->baseUrl);
        $this->breadcrumbLinks = $links;
        return $this;
    }
}
