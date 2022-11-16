<?php

namespace MinMicro\Pages;

class Error extends AbstractPage
{
    const TEMPLATE = 'pages/error.twig';
    const ROUTE_NOT_FOUND = 'route.notfound';
    const ROUTE_SERVER_ERROR = 'route.servererror';

    public function render($type = 'notfound')
    {
        $this->type = $type;

        $this->header->setImageUrl('');

        $this->header->setText($this->locale->getText("{$type}.title"));
        $data = [
            'message' => $this->locale->getText("{$type}.message"),
        ];

        $this->view(self::TEMPLATE, $data);
    }

    protected function routeTranslationsCb($lang)
    {
        if ($this->type === 'notfound') {
            return $this->locale->getTranslation(self::ROUTE_NOT_FOUND, $lang);
        }
        return $this->locale->getTranslation(self::ROUTE_SERVER_ERROR, $lang);
    }
}
