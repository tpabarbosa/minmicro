<?php

namespace MinMicro\Models;

use Exception;

class Page extends AbstractModel
{
    protected $title;
    protected $description;
    protected $keywords;
    protected $langAlias;
    private $defaultTitle;
    private $defaultDescription;
    private $defaultKeywords;

    const PUBLIC_PROPERTIES = ['title', 'description', 'keywords', 'langAlias'];

    public function __construct($locale)
    {
        $this->PUBLIC_PROPERTIES = self::PUBLIC_PROPERTIES;
        $this->langAlias = $locale->getAlias();
        $this->defaultTitle = $locale->getText('default.page.title');
        $this->defaultDescription = $locale->getText('default.page.description');
        $this->defaultKkeywords = $locale->getText('default.page.keywords');

        $this->title = $this->defaultTitle;
        $this->description = $this->defaultDescription;
        $this->keywords = $this->defaultKeywords;
    }

    public function setTitle($title = null)
    {
        $this->title = $title ?? $this->defaultTitle;
        return $this;
    }

    public function setDescription($description = null)
    {
        $this->description = $description ?? $this->defaultDescription;
        return $this;
    }

    public function setKeywords($keywords = null)
    {
        $this->keywords = $keywords ?? $this->defaultKeywords;
        return $this;
    }
}
