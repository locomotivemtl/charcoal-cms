<?php

namespace Charcoal\Cms;

// From 'charcoal-object'
use Charcoal\Object\Content;
use Charcoal\Object\CategorizableInterface;
use Charcoal\Object\CategorizableTrait;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;

// From 'charcoal-cms'
use Charcoal\Cms\DocumentInterface;

/**
 * Base document class.
 */
abstract class AbstractDocument extends Content implements
    CategorizableInterface,
    DocumentInterface
{
    use CategorizableTrait;

    /**
     * @var Translation|string|null $name
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
     * @param  mixed $name The document name.
     * @return self
     */
    public function setName($name)
    {
        $this->name = $this->translator()->translation($name);
        return $this;
    }

    /**
     * @return Translation|string|null
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @param  string $file The file relative path / url.
     * @return self
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
     * @param  string $path The document base path.
     * @return self
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
            return '';
        }
        return rtrim($this->basePath, '/').'/';
    }

    /**
     * @param  string $url The document base URL.
     * @return self
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
        return $p->mimetype($this->path());
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
