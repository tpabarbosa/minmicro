<?php

namespace MinMicro\Models;

use ParsedownExtra;
use ParsedownExtraPlugin;

class MdPage extends AbstractModel
{
    const METADATA_REGEX = '/---(.*)---/msU';
    const TITLE_REGEX = '/# (.*)/';

    protected $metadata;
        // title;
        // thumbnail;
        // description;
        // keywords;
        // headerStyle;
        // headerImageUrl;
        // headerImageAlt;

    protected $breadcrumbLinks;
    protected $mdContent;
    protected $content;
    protected $excerpt;
    protected $createdAt;
    protected $updatedAt;
    protected $fileFullPath;
    protected $basename;

    const PUBLIC_PROPERTIES = ['basename', 'breadcrumbLinks', 'content', 'excerpt', 'metadata.*', 'createdAt', 'updatedAt'];

    public function __construct($fileFullPath)
    {
        $this->PUBLIC_PROPERTIES = self::PUBLIC_PROPERTIES;
        $this->fileFullPath = $fileFullPath;
        $this->basename = pathinfo($this->fileFullPath, PATHINFO_FILENAME);
        $this->parseFileData();
    }

    private static function getRawFileContent($file)
    {
        if (!is_file($file)) {
            throw new \Exception('File not found');
        }

        return file_get_contents($file);
    }

    private function parseFileData()
    {
        if (!is_file($this->fileFullPath)) {
            throw new \Exception('File not found');
        }

        $rawFileContent = self::getRawFileContent($this->fileFullPath);

        $this->metadata = $this->getMetadata($rawFileContent);
        $this->metadata['title'] = self::getTitle($rawFileContent);

        $this->mdContent = $this->getMdContent($rawFileContent);

        $this->content = $this->parseContent();

        $this->excerpt = $this->getExcerpt();

        $this->createdAt = filectime($this->fileFullPath);
        $this->updatedAt = filemtime($this->fileFullPath);
    }

    private function getRawMetadata($rawFileContent)
    {
        preg_match(self::METADATA_REGEX, $rawFileContent, $metadata);
        return $metadata[1] ?? '';
    }

    private function getMetadata($rawMetadata)
    {
        $metadata = $this->getRawMetadata($rawMetadata);

        $parsedMetadata = [];
        $lines = preg_split("/\\r\\n|\\r|\\n/", $metadata);
        foreach ($lines as $line) {
            if (preg_match('/^(\w+):(.*)$/', $line, $matches)) {
                $parsedMetadata[$matches[1]] = trim($matches[2]);
            }
        }

        return $parsedMetadata;
    }

    public static function getTitle($content, $from_file = false)
    {
        if ($from_file) {
            $rawFileContent = self::getRawFileContent($content);
        } else {
            $rawFileContent = $content;
        }

        preg_match(self::TITLE_REGEX, $rawFileContent, $title);
        return $title[1] ?? '';
    }

    private function getMdContent($rawFileContent)
    {
        $mdContent = preg_replace(self::METADATA_REGEX, '', $rawFileContent, 1);
        $mdContent = preg_replace(self::TITLE_REGEX, '', $mdContent, 1);
        $mdContent = preg_replace('/\A[\r\n]+|[\r\n]+$/', '', $mdContent);
        return $mdContent;
    }

    private function parseContent()
    {
        $Parsedown = new ParsedownExtra();

        return $Parsedown->text($this->mdContent);
    }

    private function getExcerpt()
    {
        $excerpt = strip_tags($this->content);
        if (strlen($excerpt) < 200) {
            return $excerpt;
        }
        $excerpt = substr($excerpt, 0, 200);
        $excerpt = substr($excerpt, 0, strrpos($excerpt, ' '));
        return $excerpt . '...';
    }
}
