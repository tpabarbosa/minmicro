<?php

namespace MinMicro\Pages;

use MinMicro\Models\Components\AlphabeticNav;

class Minerals extends AbstractPage
{
    const TEMPLATE = 'pages/minerals.twig';
    const ROUTE = 'route.minerals';

    protected $letter;

    public function render($letter = null)
    {
        $this->letter = $letter;
        $title = $this->locale->getText('component.alphabetic.title');

        $data = [
            'alphabeticNav' => new AlphabeticNav($this->locale),
            'See' => $this->locale->getText('See'),
            'Videos' => $this->locale->getText('Videos'),
        ];

        if (!$letter) {
            $this->header->setText($title);
        } else {
            $this->page->setTitle("{$title} - {$letter}");
            $this->header->setText("{$title} - {$letter}");
            // $this->header->setImageUrl('');
            $data['minerals'] = $this->getMineralsFromJSON($letter);
        }

        $this->header->setStyle('small');
        $this->header->withBreadcrumbLinks($this->getBreadcrumbLinks($letter));
        // die();
        $this->view(self::TEMPLATE, $data);
    }

    private function getMineralsFromJSON($letter, $recursion = true)
    {
        $path = $this->config['MINERALS_PATH'] . '/' . $letter . '.json';

        if (! file_exists($path)) {
            $list=[];
            // throw new \Exception("Minerals with Letter '{$letter}' file not found.");
        } else {
            $list = json_decode(file_get_contents($path), true);
        }

        $minerals = [];
        foreach ($list as $key => $mineral) {
            $seeAlso = $mineral['see-also'] ?? [];

            $minerals[$key] = [
                "name" => $mineral[$this->langAlias]['name'],
                "group" => $mineral[$this->langAlias]['group'],
                "seeAlso" => $recursion ? $this->getSeeAlsoLinks($list, $letter, $seeAlso) : [],
                // "pdfUrl" => $this->baseUrl . '/pdf/' . $this->langAlias . '/' . $key . ".pdf",
                "pdfUrl" => $seeAlso || !$recursion ? null : 'https://www.ufrgs.br/minmicro/' . $key . ".pdf",
                "thumbnail" => '/images/minerals/' . $key . '.jpg',
                "formula" => $mineral['formula'],
                "youtube" => $mineral['youtube'],
            ];
        }

        // if ($recursion) var_dump($minerals['Acmita']);
        return $minerals;
    }

    private function getSeeAlsoLinks($minerals, $letter, $seeAlso = [])
    {
        $links = [];

        foreach ($seeAlso as $alias) {
            $firstLetter = $alias[0];
            if ($firstLetter !== $letter) {
                $list = $this->getMineralsFromJSON($firstLetter, false);

                $linkUrl = $this->baseUrl . '/'. $this->locale->getText(self::ROUTE) . '/'. $firstLetter;

                if (isset($list[$alias])) {
                    $links[] = [
                        'name' => $list[$alias]['name'],
                        'pdfUrl' => 'https://www.ufrgs.br/minmicro/' . $alias . ".pdf",
                        'linkUrl' => $linkUrl . '#' . $alias,
                    ];
                }

            } else {
                $list = $minerals;
                if (isset($list[$alias])) {
                    $links[] = [
                        'name' => $list[$alias][$this->langAlias]['name'],
                        'pdfUrl' => 'https://www.ufrgs.br/minmicro/' . $alias . ".pdf",
                        'linkUrl' => '#' . $alias,
                    ];
                }
            }
        }
        // die();
        return $links;
    }

    protected function routeTranslationsCb($lang)
    {
        $letterUrl = "";
        if ($this->letter) {
            $letterUrl = "/" . $this->letter;
        }
        return $this->locale->getTranslation(self::ROUTE, $lang) . $letterUrl;
    }

    private function getBreadcrumbLinks($letter = null)
    {
        $links = [
            [
            'text' => $this->locale->getText('Home'),
            'url'  => '/'
            ],
            [
            'text' => $this->locale->getText('Minerals'),
            'url'  => $letter ? '/'. $this->locale->getText(self::ROUTE) : null,
            ],
        ];

        if ($letter) {
            $links[] = [
                'text' => $letter,
                'url'  => null
            ];
        }

        return $links;
    }
}
