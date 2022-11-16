<?php

return [
    'BASE_URL' => $_ENV['BASE_URL'] ?? "http://192.168.0.161:8084",
    'DEVELOPMENT_MODE' => !!$_ENV['DEVELOPMENT_MODE'] ?? false,
    'LOCALE' => [
        'PATH' => __DIR__ . '/../locales',
        'ALLOWED' => ['pt-BR', 'en-US', 'es-ES'],
        'DEFAULT' => 'pt-BR',
        'ALIAS' => [
            'pt-BR' => 'pt',
            'en-US' => 'en',
            'es-ES' => 'es'
        ],
        'NAMES' => [
            'pt-BR' => 'Português',
            'en-US' => 'English',
            'es-ES' => 'Español'
        ]
    ],
    'SITE' => [
        'LOGO_URL' => '/images/logoUFRGS.jpg',
        'NAVBAR_LINKS' => [
            'About' => [],
            'Minerals' => [],
            'Contact' => [],
        ],
        'DEFAULT_HEADER_IMAGE_URL' => "https://i.ytimg.com/vi/MqtsfSxOzOY/hqdefault.jpg?sqp=-oaymwEcCNACELwBSFXyq4qpAw4IARUAAIhCGAFwAcABBg==&rs=AOn4CLDIgM4RE5bMzTNBPqt8Vw2wSHEf8w"
    ],
    'HOME_PAGE_LINKS' => [
        'pt' => [
            'porque-usar-microscopia',
            'luz-refletida-que-mineral-e-esse',
            'luz-transmitida-que-mineral-e-esse',
            'minerais-com-fluorescencia',
            'como-fazer-e-ver-laminas-delgadas-em-casa',
            'posts',
            'downloads'
        ],
    ],
    'PAGES_PATH' => __DIR__ . '/../pages',
    'MINERALS_PATH' => __DIR__ . '/../minerals'
];
