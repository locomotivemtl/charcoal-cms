<?php

namespace Charcoal\Cms;

use DateTime;
use DateTimeInterface;
use InvalidArgumentException;


// From 'charcoal-object'
use Charcoal\Object\Content;
use Charcoal\Object\CategorizableTrait;
use Charcoal\Object\PublishableTrait;
use Charcoal\Object\RoutableTrait;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;

/**
 * Abstract CMS Event
 */
abstract class AbstractEvent extends Content implements EventInterface
{
    use CategorizableTrait;
    use MetatagTrait;
    use PublishableTrait;
    use RoutableTrait;
    use SearchableTrait;
    use TemplateableTrait;

    /**
     * @var Translation|string|null
     */
    private $title;

    /**
     * @var Translation|string|null
     */
    private $subtitle;

    /**
     * @var Translation|string|null
     */
    private $summary;

    /**
     * @var Translation|string|null
     */
    private $content;

    /**
     * @var Translation|string|null
     */
    private $image;

    /**
     * @var DateTimeInterface|null
     */
    private $startDate;

    /**
     * @var DateTimeInterface|null
     */
    private $endDate;

    /**
     * @var Translation|string|null
     */
    private $infoUrl;

    /**
     * @var string
     */
    private $infoPhone;


    /**
     * @var float|null
     */
    private $ticketPriceMin;

    /**
     * @var float|null
     */
    private $ticketPriceMax;

    /**
     * @var Translation|string|null
     */
    private $ticketSummary;

    /**
     * @var Translation|string|null
     */
    private $ticketUrl;

    /**
     * @var string
     */
    private $ticketPhone;

    /**
     * @var array
     */
    protected $keywords;

    /**
     * Section constructor.
     * @param array $data The data.
     */
    public function __construct(array $data = null)
    {
        parent::__construct($data);

        if (is_callable([ $this, 'defaultData' ])) {
            $this->setData($this->defaultData());
        }
    }

    /**
     * @see MetataTrait::canonicalUrl
     * @return string
     */
    public function canonicalUrl()
    {
        return '';
    }

    /**
     * Some dates cannot be null
     * @return void
     */
    public function verifyDates()
    {
        if (!$this['startDate']) {
            $this->setStartDate('now');
        }

        if (!$this['endDate']) {
            $this->setEndDate($this['startDate']);
        }

        if (!$this['publishDate']) {
            $this->setPublishDate('now');
        }
    }

    /**
     * @return string The date filtered for admin dual select input and others.
     */
    public function adminDateFilter()
    {
        $start = $this['startDate']->format('Y-m-d');
        $end = $this['endDate']->format('Y-m-d');

        if ($start === $end) {
            $date = $start;
        } else {
            $date = $start.' - '.$end;
        }

        return $date;
    }

    /**
     * @param  mixed $title The event title (localized).
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $this->translator()->translation($title);

        return $this;
    }

    /**
     * @return Translation|string|null
     */
    public function title()
    {
        return $this->title;
    }

    /**
     * @param  mixed $subtitle The event subtitle (localized).
     * @return self
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $this->translator()->translation($subtitle);

        return $this;
    }

    /**
     * @return Translation|string|null
     */
    public function subtitle()
    {
        return $this->subtitle;
    }

    /**
     * @param  mixed $summary The news summary (localized).
     * @return self
     */
    public function setSummary($summary)
    {
        $this->summary = $this->translator()->translation($summary);

        return $this;
    }

    /**
     * @return Translation|string|null
     */
    public function summary()
    {
        return $this->summary;
    }

    /**
     * @param  mixed $content The event content (localized).
     * @return self
     */
    public function setContent($content)
    {
        $this->content = $this->translator()->translation($content);

        return $this;
    }

    /**
     * @return Translation|string|null
     */
    public function content()
    {
        return $this->content;
    }

    /**
     * @param  mixed $image The section main image (localized).
     * @return self
     */
    public function setImage($image)
    {
        $this->image = $this->translator()->translation($image);

        return $this;
    }

    /**
     * @return Translation|string|null
     */
    public function image()
    {
        return $this->image;
    }

    /**
     * @param  string|DateTimeInterface|null $startDate Event starting date.
     * @throws InvalidArgumentException If the timestamp is invalid.
     * @return self
     */
    public function setStartDate($startDate)
    {
        if ($startDate === null || $startDate === '') {
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
     * @return DateTimeInterface|null
     */
    public function startDate()
    {
        return $this->startDate;
    }

    /**
     * @param  string|DateTimeInterface|null $endDate Event end date.
     * @throws InvalidArgumentException If the timestamp is invalid.
     * @return self
     */
    public function setEndDate($endDate)
    {
        if ($endDate === null || $endDate === '') {
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
     * @return DateTimeInterface|null
     */
    public function endDate()
    {
        return $this->endDate;
    }

    /**
     * @param mixed $url The information URL (localized).
     * @return self
     */
    public function setInfoUrl($url)
    {
        $this->infoUrl = $this->translator()->translation($url);
        return $this;
    }

    /**
     * @return Translation|null|string
     */
    public function infoUrl()
    {
        return $this->infoUrl;
    }

    /**
     * @param mixed $phone General information phone number.
     * @return self
     */
    public function setInfoPhone($phone)
    {
        $this->infoPhone = $phone;
        return $this;
    }

    /**
     * @return string|null
     */
    public function infoPhone()
    {
        return $this->infoPhone;
    }

    /**
     * @param float $price The minimum ticket price.
     * @return self
     */
    public function setTicketPriceMin($price)
    {
        $this->ticketPriceMin = (float)$price;
        return $this;
    }

    /**
     * @return float|null
     */
    public function ticketPriceMin()
    {
        return $this->ticketPriceMin;
    }

    /**
     * @param float $price The maximum ticket price.
     * @return self
     */
    public function setTicketPriceMax($price)
    {
        $this->ticketPriceMax = (float)$price;
        return $this;
    }

    /**
     * @return float|null
     */
    public function ticketPriceMax()
    {
        return $this->ticketPriceMax;
    }

    /**
     * @param mixed $summary The ticket summary / information (localized).
     * @return self
     */
    public function setTicketSummary($summary)
    {
        $this->ticketSummary = $this->translator()->translation($summary);
        return $this;
    }

    /**
     * @return Translation|null
     */
    public function ticketSummary()
    {
        return $this->ticketSummary;
    }

    /**
     * @param mixed $url The ticket URL (localized).
     * @return self
     */
    public function setTicketUrl($url)
    {
        $this->ticketUrl = $this->translator()->translation($url);
        return $this;
    }

    /**
     * @return Translation|null
     */
    public function ticketUrl()
    {
        return $this->ticketUrl;
    }

    /**
     * @param string|null $phone Tickets phone number.
     * @return self
     */
    public function setTicketPhone($phone)
    {
        $this->ticketPhone = $phone;
        return $this;
    }

    /**
     * @return string|null
     */
    public function ticketPhone()
    {
        return $this->ticketPhone;
    }

    /**
     * @return Translation|null
     */
    public function defaultMetaTitle()
    {
        return $this->title();
    }

    /**
     * @return Translation|string|null
     */
    public function defaultMetaDescription()
    {
        $content = $this->translator()->translation($this->content());
        if ($content instanceof Translation) {
            $desc = [];
            foreach ($content->data() as $lang => $text) {
                $desc[$lang] = strip_tags($text);
            }

            return $this->translator()->translation($desc);
        }

        return null;
    }

    /**
     * @return Translation|string|null
     */
    public function defaultMetaImage()
    {
        return $this->image();
    }

    /**
     * Retrieve the object's keywords.
     *
     * @return string[]
     */
    public function keywords()
    {
        return $this->keywords;
    }

    /**
     * GenericRoute checks if the route is active.
     * Default in RoutableTrait.
     *
     * @return boolean
     */
    public function isActiveRoute()
    {
        return (
            $this['active'] &&
            $this->isPublished()
        );
    }

    /**
     * {@inheritdoc}
     *
     * @return boolean
     */
    protected function preSave()
    {
        $this->verifyDates();
        $this->setSlug($this->generateSlug());

        return parent::preSave();
    }

    /**
     * {@inheritdoc}
     *
     * @param  array $properties Optional properties to update.
     * @return boolean
     */
    protected function preUpdate(array $properties = null)
    {
        $this->verifyDates();
        $this->setSlug($this->generateSlug());

        return parent::preUpdate($properties);
    }

    /**
     * @return boolean Parent postSave().
     */
    protected function postSave()
    {
        // RoutableTrait
        $this->generateObjectRoute($this['slug']);

        return parent::postSave();
    }

    /**
     * @param array|null $properties Properties.
     * @return boolean
     */
    protected function postUpdate(array $properties = null)
    {
        // RoutableTrait
        $this->generateObjectRoute($this['slug']);

        return parent::postUpdate($properties);
    }
}
