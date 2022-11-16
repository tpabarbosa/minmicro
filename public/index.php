<?php

use Bramus\Router\Router;
use Dotenv\Dotenv;
use MinMicro\Pages\Error;
use MinMicro\Pages\Home;
use MinMicro\Pages\MdContent;
use MinMicro\Pages\Minerals;
use MinMicro\Services\Locale;
use MinMicro\Services\TwigRenderer;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

// try {
    $config = require(__DIR__ . '/../src/config.php');

    $twig = new TwigRenderer($config['BASE_URL'], $config['DEVELOPMENT_MODE']);
    $locale = new Locale($config);
    $router = new Router();

    $localeRegex = $locale->getLocaleRegex();
    $minerals = $locale->getRegex('route.minerals');

    // Rotas Home
    $router->setNamespace('\MinMicro\Pages');
    $router->get("/({$localeRegex})?", function ($lang) use ($twig, $locale, $config) {
        $page = new Home($lang, $twig, $locale, $config);
        $page->render();
    });

    $router->get("/assets(/.*.(css|js))", function ($file, $type) use ($twig, $locale, $config) {
        $filename = __DIR__ . "/../tmp/assets/{$file}{$type}";
        var_dump($filename);
        $fileType = $type === 'js' ? 'text/javascript' : 'text/css';
        var_dump($fileType);
        if (is_file($filename)) {
            var_dump(file_get_contents($filename));
            $fd = fopen($filename, 'rb');
            ob_clean();
            header('Content-Type:' . $fileType);
            while (!feof($fd)) {
                echo fread($fd, 1048576);
                ob_flush();
            }
            fclose($fd);
            die();
        } else {
            $notFound = new Error('pt', $twig, $locale, $config);
            $notFound->render();
        }
    });

    // Rotas Minerais
    $router->get("/{$locale->getTranslation('route.minerals', 'pt-BR')}(/[A-Z])?", function ($letter) use ($twig, $locale, $config) {
        var_dump($letter);
        var_dump($_SERVER); die();

        $page = new Minerals('pt', $twig, $locale, $config);
        $page->render($letter);
    });

    $router->get("/({$localeRegex})/({$minerals})(/[A-Z])?", function ($lang, $route, $letter) use ($twig, $locale, $config) {
        var_dump($letter);
        var_dump($_SERVER); die();
        $page = new Minerals($lang, $twig, $locale, $config);
        $page->render($letter);
    });


    // Rotas Conteúdo Personalizado (Articles)
    $router->get("/({$localeRegex})/(([0-9A-Za-z-]+\/?)+)", function ($lang, ...$file) use ($twig, $locale, $config) {
        var_dump($_SERVER); die();
        $page = new MdContent($lang, $twig, $locale, $config);
        $page->render($file, $lang);
    });

    $router->get('/(([0-9A-Za-z-]+\/?)+)', function (...$file) use ($twig, $locale, $config) {
        //var_dump($_SERVER);
        $page = new MdContent('pt', $twig, $locale, $config);
        $page->render($file, 'pt');
    });

    // Rotas 404
    $router->set404('/en(/.*)?', function () use ($twig, $locale, $config) {
        $page = new Error('en', $twig, $locale, $config);
        $page->render('notfound');
    });

    $router->set404(function () use ($twig, $locale, $config) {
        $page = new Error('pt', $twig, $locale, $config);
        $page->render('notfound');
    });

    $router->run();
// } catch (Exception $e) {
//     try {
//         $page = new Error('pt', $twig, $locale, $config);
//         $page->render('servererror');
//     } catch (Throwable $t) {
//         echo 'Um erro inesperado aconteceu. Por favor, tente novamente mais tarde.';
//     }
// }

    die();
