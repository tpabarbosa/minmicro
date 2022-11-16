<?php

namespace MinMicro\Pages;

use MinMicro\Models\Components\AlphabeticNav;
use MinMicro\Models\MdCategory;
use MinMicro\Models\MdPage;
use MinMicro\Pages\MdContent;

class Home extends AbstractPage
{
    const TEMPLATE = 'pages/home.twig';
    const ROUTE = 'route.home';

    public function render()
    {
        $data = [
            'alphabeticNav' => new AlphabeticNav($this->locale),
            'links' => $this->linksToView($this->config['HOME_PAGE_LINKS']),
            $data['has_format_date'] = isset($this->twig->getFilters()['format_date'])
        ];
        // var_dump($data);
        // die();
        $this->view(self::TEMPLATE, $data);
    }

    protected function linksToView($links)
    {
        $l = [];
        if (!isset($links[$this->langAlias])) {
            return $l;
        }

        foreach ($links[$this->langAlias] as $link) {
            $fileOrDir = MdContent::fileFullPath($link, $this->config, $this->langAlias);
            // var_dump($fileOrDir);
            try {
                if (is_file($fileOrDir . '.md')) {
                    $page = new MdPage($fileOrDir . ".md");
                    $l['pages'][] = [
                        'text' => $page->title,
                        'url' => $this->baseUrl . '/'. $link,
                        'page' => $page
                    ];
                } elseif (is_dir($fileOrDir)) {
                    $category = new MdCategory($fileOrDir);
                    $l['categories'][] = [
                        'text' => $category->page->title,
                        'url' => $this->baseUrl . '/'. $link,
                        'numOfPages' => $category->numOfPages,
                    ];
                }
            } catch (\Exception $e) {
                // var_dump($e);
            }
        }
        // var_dump($l);die();
        return $l;
    }

    protected function routeTranslationsCb($lang)
    {
        return $this->locale->getTranslation(self::ROUTE, $lang);
    }
}
