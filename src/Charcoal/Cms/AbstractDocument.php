<?php

namespace Charcoal\Cms;

// Module `charcoal-base` dependencies
use \Charcoal\Object\Content;
use \Charcoal\Object\CategorizableInterface;
use \Charcoal\Object\CategorizableTrait;

// Module `charcoal-translation` dependencies
use \Charcoal\Translation\TranslationString;

// Intra-module `charcoal-cms` depdencies
use \Charcoal\Cms\DocumentInterface;

/**
 * Base document class.
 */
abstract class AbstractDocument extends Content implements
    CategorizableInterface,
    DocumentInterface
{
    use CategorizableTrait;

    /**
     * @var TranslationString $name
     */
    private $name;

    /**
     * @var string $file
     */
    private $file;

    /**
     * @var string $basePath
     */
    private $basePath;

    /**
     * @var string $baseUrl
     */
    private $baseUrl;

    /**
     * @param mixed $name The document name.
     * @return DocumentInterface Chainable
     */
    public function setName($name)
    {
        $this->name = new TranslationString($name);
        return $this;
    }

    /**
     * @return TranslationString
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @param string $file The file relative path / url.
     * @return DocumentInterface Chainable
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * @return string
     */
    public function file()
    {
        return $this->file;
    }

    /**
     * @param string $path The document base path.
     * @return DocumentInterface Chainable
     */
    public function setBasePath($path)
    {
        $this->basePath = $path;
        return $this;
    }

    /**
     * Get the base path, with a trailing slash.
     *
     * @return string
     */
    public function basePath()
    {
        if (!$this->basePath) {
            $p = $this->property('file');
            return '';
        }
        return rtrim($this->basePath, '/').'/';
    }

    /**
     * @param string $url The document base URL.
     * @return DocumentInterface Chainable
     */
    public function setBaseUrl($url)
    {
        $this->baseUrl = $url;
        return $this;
    }

    /**
     * Get the base url, with a trailing slash.
     *
     * @return string
     */
    public function baseUrl()
    {
        return rtrim($this->baseUrl, '/').'/';
    }

    /**
     * @return string
     */
    public function path()
    {
        return $this->basePath().$this->file();
    }

    /**
     * @return string
     */
    public function url()
    {
        return $this->baseUrl().$this->file();
    }

    /**
     * @return string
     */
    public function mimetype()
    {
        $p = $this->property('file');
        return $p->mimetype($this->file());
    }

    /**
     * Get the fiqlename (basename; without any path segment).
     *
     * @return string
     */
    public function filename()
    {
        return basename($this->file());
    }

    /**
     * Get the document's file size, in bytes.
     *
     * @return integer
     */
    public function filesize()
    {
        return filesize($this->path());
    }
}
