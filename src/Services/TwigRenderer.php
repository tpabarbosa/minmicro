<?php

namespace MinMicro\Services;

use Odan\Twig\TwigAssetsExtension;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\TwigFunction;

class TwigRenderer
{
    protected $twig;

    const CACHE_PATH = __DIR__ . '/../../tmp';//'/tmp';//
    const TWIG_PUBLIC_PATH = __DIR__ . '/../../tmp/assets';//'/tmp/assets';//__DIR__ . '/../../public/assets';
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


        if ($this->DEVELOPMENT_MODE) {
            if (!is_dir(self::CACHE_PATH)) {
                mkdir(self::CACHE_PATH);
            }
            if (!is_dir(self::TWIG_PUBLIC_PATH)) {
                mkdir(self::TWIG_PUBLIC_PATH);
            }


            $twig->addExtension(new TwigAssetsExtension($twig, $this->getAssetsOptions()));
            $twig->addExtension(new DebugExtension());
        }

        $twig->addFunction(new TwigFunction('call_filter_if_it_exists', function ($env, $input, $filter, ...$args) {
            $filter = $env->getFilter($filter);

            if (!$filter) {
                return $input;
            }

            return $filter->getCallable()($env, $input, ...$args);
        }, ['needs_environment' => true]));


        $twig->addFunction(new TwigFunction('call_function_if_it_exists', function ($env, $func, ...$args) {

            $func = $env->getFunction($func);
            $test = $func->getCallable()(...$args);

            if (!$func) {
                return '';
            }

            return $test;
        }, ['needs_environment' => true]));

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
            'debug' => !!$this->DEVELOPMENT_MODE,
            'auto-reload' => true,
            'cache' => !$this->DEVELOPMENT_MODE ? self::CACHE_PATH : false,
        ];
    }

    private function getAssetsOptions()
    {
        return [
            'path' => self::TWIG_PUBLIC_PATH,
            'path_chmod' => -1,
            'url_base_path' => $this->baseUrl . '/assets/',
            'cache_path' => self::CACHE_PATH,
            'cache_name' => 'cache-assets',
            'cache_lifetime' => 0,
            'minify' => !$this->DEVELOPMENT_MODE
        ];
    }

    public function render($template, $template_data = [])
    {
        echo $this->twig->render($template, $template_data);
    }
}
