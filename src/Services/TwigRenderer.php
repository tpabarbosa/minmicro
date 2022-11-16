<?php

namespace MinMicro\Services;

use Odan\Twig\TwigAssetsExtension;
use Twig\Extension\DebugExtension;

class TwigRenderer
{
    protected $twig;

    const CACHE_PATH = '/tmp';//__DIR__ . '/../../tmp';
    const TWIG_PUBLIC_PATH = __DIR__ . '/../../public/assets';
    const TWIG_TEMPLATES_PATH = __DIR__ . '/../../templates';
    private $DEVELOPMENT_MODE;


    protected $baseUrl;

    public function __construct($baseUrl, $mode = false)
    {
        // self::clearCache();
        $this->DEVELOPMENT_MODE = $mode;
        $this->baseUrl = $baseUrl;

        $loader = new \Twig\Loader\FilesystemLoader(self::TWIG_TEMPLATES_PATH);
        $twig = new \Twig\Environment($loader, $this->getTwigOptions());

        $twig->addExtension(new TwigAssetsExtension($twig, $this->getAssetsOptions()));
        $twig->addExtension(new DebugExtension());
        // var_dump($twig->getFilters());
        $this->twig = $twig;
    }

    public function getFilters()
    {
        return $this->twig->getFilters();
    }
    // private static function clearCache()
    // {
    //     try {
    //         if (!self::TWIG_OPTIONS['cache']) {
    //             $internalCache = new TwigAssetsCache(self::CACHE_PATH);
    //             $internalCache->clearCache();

    //             $publicCache = new TwigAssetsCache(self::TWIG_PUBLIC_PATH);
    //             $publicCache->clearCache();
    //         }
    //     }
    //     catch (\Throwable $e) {
    //         // do nothing
    //     }
    // }

    public function addExtension($extension)
    {
        $this->twig->addExtension($extension);
    }

    private function getTwigOptions()
    {
        return [
            'debug' => $this->DEVELOPMENT_MODE,
            'auto-reload' => true,
            'cache' => !$this->DEVELOPMENT_MODE ? self::CACHE_PATH : false,
        ];
    }

    private function getAssetsOptions()
    {
        return [
            'path' => self::TWIG_PUBLIC_PATH,
            'url_base_path' => $this->baseUrl . '/assets/',
            'cache_path' => self::CACHE_PATH,
            'cache_name' => 'assets',
            'cache_lifetime' => -1,
            'minify' => !$this->DEVELOPMENT_MODE
        ];
    }

    public function render($template, $template_data = [])
    {
        echo $this->twig->render($template, $template_data);
    }
}
