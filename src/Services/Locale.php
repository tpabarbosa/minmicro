<?php

namespace MinMicro\Services;

use Twig\Extra\Intl\IntlExtension;

class Locale
{
    public $config;
    /**
     * @var array
     */
    protected $language = [];

    /**
     * Throw Exceptions?
     *
     * @var bool
     */
    public $exceptions = true;

    /**
     * Current Locale
     *
     * @var string
     */
    protected $current;

    /**
     * Set New Transalion from JSON file
     *
     * @param  string  $name
     * @param  string  $path
     */
    private function setTranslationsFromJSON(string $name): void
    {
        $path = $this->config['PATH'] . '/' . $name . '.json';

        if (! file_exists($path)) {
            throw new \Exception('Translation file not found.');
        }

        if (! \in_array($name, $this->config['ALLOWED'])) {
            throw new \Exception("Locale '${name}' not allowed.");
        }

        $translations = json_decode(file_get_contents($path), true);
        $this->language[$name] = $translations;
    }

    public function __construct(array $config, array $options = [])
    {
        $this->baseUrl = $config['BASE_URL'];

        if (! $config) {
            throw new \Exception('Locale Configuration is missing');
        }

        $this->config = $config['LOCALE'];
        $this->current = $this->config['DEFAULT'];


        if (\array_key_exists('exceptions', $options)) {
            $this->exceptions = $options['exceptions'];
        } else {
            $this->exceptions = true;
        }

        foreach ($this->config['ALLOWED'] as $locale) {
            $this->setTranslationsFromJSON($locale);
        }
    }

    /**
     * Change Current Locale
     *
     * @param $name
     *
     * @throws Exception
     */
    public function setCurrent(string $name = null): self
    {
        if (! $name) {
            $name = $this->current;
            return $this;
        }

        if (! \array_key_exists($name, $this->language)) {
            $key = array_search($name, $this->config['ALIAS']);
            if (! $key) {
                throw new \Exception('Locale not found');
            }
            $name = $key;
        }

        $this->current = $name;
        try {
            \Locale::setDefault($this->current);
        } catch (\Exception $e) {

        }

        return $this;
    }

    public function setFormatter($twig)
    {
        try {
            $formatter = new \IntlDateFormatter($this->current, \IntlDateFormatter::LONG, \IntlDateFormatter::SHORT);
            $twig->addExtension(new IntlExtension($formatter));
        } catch (\Exception $e) {
        }
        return $this;
    }

    /**
     * Get Text by Locale
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     *
     * @throws Exception
     */
    public function getText(string $key, $placeholders = [])
    {
        $current = '{{'.$key.'}}';

        if (! \array_key_exists($key, $this->language[$this->current])) {
            if ($this->exceptions) {
                throw new \Exception('Key named "'.$key.'" not found');
            }

            return $current;
        }

        $translation = $this->language[$this->current][$key];

        foreach ($placeholders as $placeholderKey => $placeholderValue) {
            $translation = str_replace('{{'.$placeholderKey.'}}', $placeholderValue, $translation);
        }

        return $translation;
    }

    public function getCurrent()
    {

        return $this->current;
    }

    public function getDefault()
    {
        return $this->config['DEFAULT'];
    }

    public function getAlias($lang = null)
    {
        if (! $lang) {
            return $this->config['ALIAS'][$this->current];
        }

        return $this->config['ALIAS'][$lang];
    }

    public function getName($lang = null)
    {
        if (! $lang) {
            return $this->config['NAMES'][$this->current];
        }

        return $this->config['NAMES'][$lang];
    }

    public function getFlag()
    {
        return $this->config['FLAG'][$this->current];
    }

    public function getTranslation($key, $lang)
    {
        if (! \array_key_exists($key, $this->language[$lang])) {
            if ($this->exceptions) {
                throw new \Exception('Key named "'.$key.'" not found');
            }

            return '{{'.$key.'}}';
        }

        return $this->language[$lang][$key];
    }

    public function getLocalePathOrUrl($base, $lang = null)
    {
        if (! $lang) {
            $lang = $this->current;
        }

        if ($lang !== $this->config['DEFAULT']) {
            $base .= '/' . $this->getAlias($lang);
        } else {
            // if (!$base) {
            //     $base .= '/';
            // }
        }
        return $base;
    }

    public function getLocaleRegex()
    {
        $regex = [];
        foreach ($this->config['ALLOWED'] as $locale) {
            if ($locale !== $this->config['DEFAULT']) {
                $regex[] = $this->getAlias($locale);
            }
        }
        return join('|', $regex);
    }

    public function getRegex($key)
    {
        $regex = [];
        foreach ($this->config['ALLOWED'] as $locale) {
            if ($locale !== $this->config['DEFAULT']) {
                $regex[] = $this->language[$locale][$key];
            }
        }
        return join('|', $regex);
    }

    public function getLocaleRouteUrl($route)
    {
        return $this->getLocalePathOrUrl($this->baseUrl) . '/' . $this->getText($route);
    }
}
