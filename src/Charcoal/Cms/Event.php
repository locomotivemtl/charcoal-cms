<?php

namespace Charcoal\Cms;

use \DateTime;
use \DateTimeInterface;
use \Exception;
use \InvalidArgumentException;

use \Charcoal\Translation\TranslationString;
use \Charcoal\Object\Content;
use \Charcoal\Object\CategorizableInterface;
use \Charcoal\Object\CategorizableTrait;
use \Charcoal\Object\PublishableInterface;
use \Charcoal\Object\PublishableTrait;
use \Charcoal\Object\RoutableInterface;
use \Charcoal\Object\RoutableTrait;

use \Charcoal\Cms\MetatagInterface;
use \Charcoal\Cms\SearchableInterface;

/**
*
*/
class Event extends Content implements
    CategorizableInterface,
    MetatagInterface,
    PublishableInterface,
    RoutableInterface,
    SearchableInterface
{
    use CategorizableTrait;
    use PublishableTrait;
    use MetatagTrait;
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
    * @var Collection $blocks
    */
    private $blocks;

    /**
    * @var DateTime $start_date
    */
    private $start_date;
    /**
    * @var DateTime $start_date
    */
    private $end_date;

    /**
    * @var TranslationString $thumbnail
    */
    private $thumbnail;
    /**
    * @var TranslationString $image
    */
    private $image;

    /**
    * @var Collection $documents
    */
    private $documents;

    /**
    * CategorizableTrait > category_type()
    *
    * @return string
    */
    public function category_type()
    {
        return 'charcoal/cms/event-category';
    }

    /**
    * @param mixed $title
    * @return TranslationString
    */
    public function set_title($title)
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
    public function set_subtitle($subtitle)
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
    public function set_content($content)
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
    * @return Collection
    */
    public function blocks()
    {
        if ($this->blocks === null) {
            $this->blocks = $this->load_blocks();
        }
        return $this->blocks;
    }

    /**
    * @return Collection
    */
    public function load_blocks()
    {
        // @todo
    }

    /**
    * @param string|DateTime $start_date
    * @throws InvalidArgumentException
    */
    public function set_start_date($start_date)
    {
        if ($start_date === null) {
            $this->start_date = null;
            return $this;
        }
        if (is_string($start_date)) {
            try {
                $start_date = new DateTime($start_date);
            } catch (Exception $e) {
                throw new InvalidArgumentException(
                    'Invalid start date: '.$e->getMessage()
                );
            }
        }
        if (!($start_date instanceof DateTimeInterface)) {
            throw new InvalidArgumentException(
                'Invalid "Start Date" value. Must be a date/time string or a DateTime object.'
            );
        }
        $this->start_date = $start_date;
        return $this;
    }

    /**
    * @return DateTime|null
    */
    public function start_date()
    {
        return $this->start_date;
    }

    /**
    * @param string|DateTime $end_date
    * @throws InvalidArgumentException
    */
    public function set_end_date($end_date)
    {
        if ($end_date === null) {
            $this->end_date = null;
            return $this;
        }
        if (is_string($end_date)) {
            try {
                $end_date = new DateTime($end_date);
            } catch (Exception $e) {
                throw new InvalidArgumentException(
                    'Invalid end date: '.$e->getMessage()
                );
            }
        }
        if (!($end_date instanceof DateTimeInterface)) {
            throw new InvalidArgumentException(
                'Invalid "End Date" value. Must be a date/time string or a DateTime object.'
            );
        }
        $this->end_date = $end_date;
        return $this;
    }

    /**
    * @return DateTime|null
    */
    public function end_date()
    {
        return $this->end_date;
    }

    /**
    * MetatagTrait > canonical_url
    *
    * @return string
    * @todo
    */
    public function canonical_url()
    {
        return '';
    }
}
