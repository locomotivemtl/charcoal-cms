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
use \Charcoal\Cms\NewsInterface;
use \Charcoal\Cms\SearchableInterface;
use \Charcoal\Cms\SearchableTrait;

/**
 * News
 */
abstract class AbstractNews extends Content implements
    CategorizableInterface,
    MetatagInterface,
    NewsInterface,
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
     * @var TranslationString $image
     */
    private $image;

    /**
     * @var DateTime $newsDate
     */
    private $newsDate;

    /**
     * @var Collection $documents
     */
    public $documents;

    /**
     * @var TranslationString $infoUrl
     */
    private $infoUrl;

    /**
     * @param mixed $title The news title (localized).
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
     * @param mixed $subtitle The news subtitle (localized).
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
     * @param mixed $content The news content (localized).
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
     * @param mixed $image The section main image (localized).
     * @return Section Chainable
     */
    public function setImage($image)
    {
        $this->image = new TranslationString($image);
        return $this;
    }

    /**
     * @return TranslationString
     */
    public function image()
    {
        return $this->image;
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

    /**
     * @param mixed $url The info URL (news source or where to find more information; localized).
     * @return NewsInterface Chainable
     */
    public function setInfoUrl($url)
    {
        $this->infoUrl = new TranslationString($url);
        return $this;
    }

    /**
     * @return TranslationString
     */
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
     * @param string            $path     The request path.
     * @param RequestInterface  $request  PSR-7 (http) request.
     * @param ResponseInterface $response PSR-7 (http) response.
     * @throws InvalidArgumentException If the path is not a string.
     * @return callable|null Route dispatcher
     */
    public function routeHandler($path, RequestInterface $request, ResponseInterface $response)
    {
        if (!is_string($path)) {
            throw new InvalidArgumentException(
                'Route path must be a string'
            );
        }
        $match_path = $path == 'xxx';

        // Insert logic here...
        if ($match_path) {
            return function(RequestInterface $request, ResponseInterface $response) use ($path) {
                $response->write($path);
            };
        } else {
            return null;
        }
    }

    /**
     * @return TranslationString
     */
    public function defaultMetaTitle()
    {
        return $this->title();
    }

    /**
     * @return TranslationString
     */
    public function defaultMetaDescription()
    {
        return $this->content();
    }

    /**
     * @return TranslationString
     */
    public function defaultMetaImage()
    {
        return $this->image();
    }
}
