<?php

namespace MinMicro\Pages;

use MinMicro\Models\Footer;
use MinMicro\Models\Header;
use MinMicro\Models\Page;
use MinMicro\Models\Site;

abstract class AbstractPage
{
    protected $twig;
    protected $site;
    protected $page;
    protected $footer;
    protected $lang;
    protected $locale;
    protected $langAlias;
    protected $baseUrl;
    protected $config;

    public function __construct($lang, $twig, $locale, $config)
    {
        $this->twig = $twig;
        $this->config = $config;

        $locale->setCurrent($lang)->setFormatter($this->twig);
        $this->locale = $locale;
        $this->langAlias = $this->locale->getAlias();

        // var_dump();die();

        $baseUrl = $locale->getLocalePathOrUrl($config['BASE_URL']);

        $this->baseUrl = $baseUrl;
        $this->site = new Site($locale, $config);
        $this->page = new Page($locale);
        $this->header = new Header($locale, $config);
        $this->footer = new Footer($config);
    }

    protected function view($template, $template_data = [])
    {
        $data = [
            'site' => $this->site,
            'page' => $this->page,
            'header' => $this->header,
            'footer' => $this->footer,
            'translatedUrls' => $this->getRouteTranslations()
        ];

        $this->twig->render($template, array_merge($data, $template_data));
    }

    public function getRouteUrl($route)
    {
        return $this->baseUrl . '/' . $route;
    }

    abstract public function render();

    abstract protected function routeTranslationsCb($lang);

    protected function getRouteTranslations()
    {
        $translations = [];

        foreach ($this->locale->config['ALLOWED'] as $lang) {
            $url = $this->routeTranslationsCb($lang);
            if ($url && $url[0] !== '/') {
                $url = '/'. $url;
            }
            $final = $this->locale->getLocalePathOrUrl('', $lang) . $url;
            if (($final && $final[0] !== '/') || $final === '') {
                $final = '/' . $final;
            }
            $translations[$this->locale->getAlias($lang)] = [
                'url' => $url === null ? '' : $final,
                'name' => $this->locale->getName($lang)
            ];
        }
        return $translations;
    }
}
