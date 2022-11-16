<?php

namespace MinMicro\Pages;

use MinMicro\Models\MdCategory;
use MinMicro\Models\MdPage;

class MdContent extends AbstractPage
{
    const TEMPLATE = 'pages/md.content.twig';

    protected $metadata;
    protected $content;
    protected $dir;
    protected $route;
    // protected $langAlias;
    protected $dirLinks;
    protected $mdPage;

    public function render($route = null, $lang = null)
    {
        var_dump($route);
        var_dump($lang);
        if (!is_string($route)) {
            $route = array_filter($route);
            var_dump($route);
            $route = join('/', $route);
        }

        $this->route = $route;

        $minerals = $this->locale->getText('route.minerals');
        var_dump($route);
        var_dump("{$minerals}(\/[A-Z])?");
        preg_match("/^{$minerals}(\/[A-Z])?$/", $route, $matches);
        var_dump($matches);die();
        if (isset($matches[1]) && $matches[1]) {
            $page = new Minerals($this->langAlias, $this->twig, $this->locale, $this->config);
            $page->render($matches[1]);
        }

        $fileFullPath = $this->getFileFullPath($route);

        $data = [];
        if (is_dir($fileFullPath)) {
            $mdCategory = new MdCategory($fileFullPath, false);

            $data['links'] = $this->parseLinks($mdCategory);

            $this->mdPage = $mdCategory->page;
        } elseif (is_file($fileFullPath . '.md')) {
            $this->mdPage = new MdPage($fileFullPath . '.md');
        } else {
            $notFound = new Error($this->langAlias, $this->twig, $this->locale, $this->config);
            return $notFound->render();
        }

        $this->setHeader();
        $this->setPage();

        $data['content'] = $this->mdPage->content;
        $this->view(self::TEMPLATE, $data);
    }

    protected function setPage()
    {
        $this->page->setDescription($this->mdPage->description);
        $this->page->setKeywords($this->mdPage->keywords);
        $this->page->setTitle($this->mdPage->title);
    }

    protected function setHeader()
    {
        $this->header->setStyle($this->mdPage->headerStyle);
        $this->header->setText($this->mdPage->title);
        $this->header->setImageUrl($this->mdPage->headerImageUrl);
        $this->header->setImageAlt($this->mdPage->headerImageAlt);
        $this->header->withBreadcrumbLinks($this->getBreadcrumbLinks());
    }

    protected function routeTranslationsCb($lang)
    {
        $alias = $this->locale->getAlias($lang);
        $route = $this->mdPage->$alias ?? null;
        if ($route && $route[0] !== '/') {
            $route = '/' . $route;
        }
        return $route;
    }

    private function getFileFullPath($route)
    {
        $base = $this->config['PAGES_PATH'] . '/' . $this->langAlias;

        return $base . '/' . $route;
    }

    public static function fileFullPath($route, $config, $langAlias)
    {
        $base = $config['PAGES_PATH'] . '/' . $langAlias;

        return $base . '/' . $route;
    }

    private function getBreadcrumbLinks()
    {
        $path = '';
        $links = [
            [
            'text' => $this->locale->getText('Home'),
            'url'  => '/'
            ]
        ];

        $route = ltrim($this->route, '/');
        $route = explode('/', $route);

        $path = $this->getFileFullPath('');
        for ($i=0; $i < count($route)-1; $i++) {
            $path .= '/' . $route[$i];
            $links[] = [
                'text' => MdPage::getTitle($path. '/' . 'index.md', true),
                'url' => $links[$i]['url'] . '/'. $route[$i]
            ];
        }

        $links[] = [
            'text' => $this->mdPage->title,
            'url' => null
        ];

        return $links;
    }

    private function parseLinks($mdCategory)
    {
        $links = [];
        foreach ($mdCategory->pages as $page) {
            $links['pages'][] = [
                'text' => $page->title,
                'url' => $this->baseUrl . '/'. $this->route .'/' . $page->basename,
                'page' => $page
            ];
        }
        foreach ($mdCategory->subCategories as $category) {
            $links['categories'][] = [
                'text' => $category->page->title,
                'url' => $this->baseUrl . '/'. $this->route .'/' . $category->basename,
                'numOfPages' => $category->numOfPages,
            ];
        }

        return $links;
    }
}
