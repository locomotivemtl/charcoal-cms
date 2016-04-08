<?php

namespace Charcoal\Cms;

use \DateTime;
use \DateTimeInterface;
use \InvalidArgumentException;

use \Psr\Http\Message\RequestInterface;
use \Psr\Http\Message\ResponseInterface;

// Dependencies from `charcoal-translation`
use \Charcoal\Translation\TranslationString;

// Dependencies from `charcoal-base`
use \Charcoal\Object\Content;
use \Charcoal\Object\CategorizableInterface;
use \Charcoal\Object\CategorizableTrait;
use \Charcoal\Object\PublishableInterface;
use \Charcoal\Object\PublishableTrait;

// Dependencies from `charcoal-app`
use \Charcoal\App\Routable\RoutableInterface;
use \Charcoal\App\Routable\RoutableTrait;

// Intra-module (`charcoal-cms`) dependencies
use \Charcoal\Cms\MetatagInterface;
use \Charcoal\Cms\SearchableInterface;

/**
* News
*/
class News extends Content implements
    CategorizableInterface,
    MetatagInterface,
    PublishableInterface,
    RoutableInterface,
    SearchableInterface
{
    use CategorizableTrait;
    use PublishableTrait;
    use MetatagTrait;
    use RoutableTrait;
    use SearchableTrait;

    /**
    * @var TranslationString $title
    */
    private $title;

    /**
    * @var TranslationString $title
    */
    private $subtitle;

    /**
    * @var TranslationString $content
    */
    private $content;

    /**
    * @var array $contentStructure
    */
    private $contentStructure;

    /**
    * @var DateTime $newsDate
    */
    private $newsDate;

    /**
    * @var TranslationString $image
    */
    private $image;

    /**
    * @var Collection $documents
    */
    public $documents;

    /**
    * @var TranslationString $infoUrl
    */
    private $infoUrl;

    /**
    * CategorizableTrait > categoryType()
    *
    * @return string
    */
    public function categoryType()
    {
        return 'charcoal/cms/news-category';
    }

    /**
    * @param mixed $title
    * @return TranslationString
    */
    public function setTitle($title)
    {
        $this->title = new TranslationString($title);
        return $this;
    }

    /**
    * @return TranslationString
    */
    public function title()
    {
        return $this->title;
    }

    /**
    * @param mixed $subbtitle
    * @return Event Chainable
    */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = new TranslationString($subtitle);
        return $this;
    }

    /**
    * @return TranslationString
    */
    public function subtitle()
    {
        return $this->subtitle;
    }

    /**
    * @param mixed $content
    * @return Event Chainable
    */
    public function setContent($content)
    {
        $this->content = new TranslationString($content);
        return $this;
    }

    /**
    * @return TranslationString
    */
    public function content()
    {
        return $this->content;
    }

    /**
     * @param mixed $newsDate The news date.
     * @throws InvalidArgumentException If the timestamp is invalid.
     * @return ObjectRevision Chainable
     */
    public function setNewsDate($newsDate)
    {
        if ($newsDate === null) {
            $this->newsDate = null;
            return $this;
        }
        if (is_string($newsDate)) {
            $newsDate = new DateTime($newsDate);
        }
        if (!($newsDate instanceof DateTimeInterface)) {
            throw new InvalidArgumentException(
                'Invalid "Revision Date" value. Must be a date/time string or a DateTimeInterface object.'
            );
        }
        $this->newsDate = $newsDate;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function newsDate()
    {
        return $this->newsDate;
    }

    public function setInfoUrl($url)
    {
        $this->infoUrl = new TranslationString($url);
        return $this;
    }

    public function infoUrl()
    {
        return $this->infoUrl;
    }

    /**
    * MetatagTrait > canonical_url
    *
    * @return string
    * @todo
    */
    public function canonicalUrl()
    {
        return '';
    }

    /**
    * RoutableInterface > handle_route()
    *
    * @param string $path
    * @param RequestInterface $request
    * @param ResponseInterface $response
    * @throws InvalidArgumentException
    * @return callable|null Route dispatcher
    */
    public function routeHandler($path, RequestInterface $request, ResponseInterface $response)
    {
        if (!is_string($path)) {
            throw new InvalidArgumentExeption(
                'Route path must be a string'
            );
        }
        $match_path = $path == 'xxx'; // Insert logic here...
        if($match_path) {
            return function(RequestInterface $request, ResponseInterface $response) use ($path) {
                $response->write($path);
            };
        }
        else {
            return null;
        }
    }

}
