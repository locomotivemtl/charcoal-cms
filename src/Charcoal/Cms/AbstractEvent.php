<?php

namespace Charcoal\Cms;

use \DateTime;
use \DateTimeInterface;
use \Exception;
use \InvalidArgumentException;

use \Psr\Http\Message\RequestInterface;
use \Psr\Http\Message\ResponseInterface;

// Module `charcoal-base` dependencies
use \Charcoal\Object\Content;
use \Charcoal\Object\CategorizableInterface;
use \Charcoal\Object\CategorizableTrait;
use \Charcoal\Object\PublishableInterface;
use \Charcoal\Object\PublishableTrait;

// Dependencies from `charcoal-app`
use \Charcoal\App\Routable\RoutableInterface;
use \Charcoal\App\Routable\RoutableTrait;

// Module `charcoal-translation` depdencies
use \Charcoal\Translation\TranslationString;

// Intra-module (`charcoal-cms`) dependencies
use \Charcoal\Cms\MetatagInterface;
use \Charcoal\Cms\SearchableInterface;

/**
 *
 */
abstract class AbstractEvent extends Content implements
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
     * @var TranslationString $image
     */
    private $image;

    /**
     * @var DateTime $startDate
     */
    private $startDate;
    /**
     * @var DateTime $startDate
     */
    private $endDate;

    /**
     * @param mixed $title The event title (localized).
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
     * @param mixed $subtitle The event subtitle (localized).
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
     * @param mixed $content The event content (localized).
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
     * @param string|DateTime $startDate Event starting date.
     * @throws InvalidArgumentException If the timestamp is invalid.
     * @return EventInterface Chainable
     */
    public function setStartDate($startDate)
    {
        if ($startDate === null) {
            $this->startDate = null;
            return $this;
        }
        if (is_string($startDate)) {
            $startDate = new DateTime($startDate);
        }
        if (!($startDate instanceof DateTimeInterface)) {
            throw new InvalidArgumentException(
                'Invalid "Start Date" value. Must be a date/time string or a DateTime object.'
            );
        }
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function startDate()
    {
        return $this->startDate;
    }

    /**
     * @param string|DateTime $endDate Event end date.
     * @throws InvalidArgumentException If the timestamp is invalid.
     * @return EventInterface Chainable
     */
    public function setEndDate($endDate)
    {
        if ($endDate === null) {
            $this->endDate = null;
            return $this;
        }
        if (is_string($endDate)) {
            $endDate = new DateTime($endDate);
        }
        if (!($endDate instanceof DateTimeInterface)) {
            throw new InvalidArgumentException(
                'Invalid "End Date" value. Must be a date/time string or a DateTime object.'
            );
        }
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function endDate()
    {
        return $this->endDate;
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
