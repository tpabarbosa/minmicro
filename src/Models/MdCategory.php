<?php

namespace MinMicro\Models;

use Exception;

class MdCategory extends AbstractModel
{
    protected $page;
    protected $dirFullPath;
    protected $pages;
    protected $subCategories;
    protected $numOfPages;
    protected $basename;

    const PUBLIC_PROPERTIES = ['basename', 'page', 'pages', 'subCategories', 'numOfPages'];

    public function __construct($dirFullPath, $skipRecursion = true)
    {
        $this->skipRecursion = $skipRecursion;
        $this->PUBLIC_PROPERTIES = self::PUBLIC_PROPERTIES;
        $this->dirFullPath = $dirFullPath;
        $this->basename = pathinfo($this->dirFullPath, PATHINFO_FILENAME);
        $this->page = new MdPage($dirFullPath . '/index.md');
        $this->parseDirData();
    }

    private function parseDirData()
    {
        $pages = [];
        $categories = [];
        $fileList = glob($this->dirFullPath . '/*');
        foreach ($fileList as $filename) {
            if ($filename !== $this->dirFullPath . '/index.md') {
                if (is_file($filename)) {
                    $pages[] = new MdPage($filename);
                }
                if (!$this->skipRecursion && is_dir($filename) && is_file($filename . '/index.md')) {
                    $categories[] = new MdCategory($filename, true);
                }
            }
        }
        $this->pages = $pages;
        $this->subCategories = $categories;
        $this->numOfPages = $this->getNumOfPages();
    }

    private function getNumOfPages()
    {
        $fileList = glob($this->dirFullPath . '/*.md');
        $hasIndex = glob($this->dirFullPath . '/index.md');

        return count($fileList) - count($hasIndex);
    }
}
