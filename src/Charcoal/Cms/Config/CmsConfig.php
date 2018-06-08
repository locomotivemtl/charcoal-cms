<?php

namespace Charcoal\Cms\Config;

// From 'charcoal-config'
use Charcoal\Config\AbstractConfig;

// From 'charcoal-core'
use Charcoal\Model\ModelInterface;

// From 'charcoal-cms'
use Charcoal\Cms\Config\EventConfig;
use Charcoal\Cms\Config\NewsConfig;
use Charcoal\Cms\Config\SectionConfig;

/**
 * Class Config
 */
class CmsConfig extends AbstractConfig
{
    /**
     * @var string $defaultFromEmail
     */
    protected $defaultFromEmail;

    /**
     * @var array $homeNews
     */
    protected $homeNews;

    /**
     * @var array $homeEvents
     */
    protected $homeEvents;

    /**
     * @var NewsConfig $newsConfig
     */
    protected $newsConfig;

    /**
     * @var EventConfig $eventConfig
     */
    protected $eventConfig;

    /**
     * @var SectionConfig $sectionConfig
     */
    protected $sectionConfig;

    /**
     * @var string $contactCategoryObj Must conform Cms\\Support\\Interface\\ContactCategoryInterface.
     */
    protected $contactCategoryObj;

    /**
     * @var string $defaultContactCategory The default contact category to fallback.
     */
    protected $defaultContactCategory;

    /**
     * @var string $contactObj Must conform Cms\\Support\\Interface\\ContactInterface.
     */
    protected $contactObj;

    /**
     * @var array $dateFormats
     */
    protected $dateFormats = [];

    /**
     * @var array $timeFormats
     */
    protected $timeFormats = [];

    // ==========================================================================
    // INIT
    // ==========================================================================

    /**
     * @param ModelInterface $model The object model.
     * @return void
     */
    public function addModel(ModelInterface $model)
    {
        $this->setData($model->data());
    }

    // ==========================================================================
    // SETTERS
    // ==========================================================================

    /**
     * @param mixed $defaultFromEmail The default email for contact forms.
     * @return self
     */
    public function setDefaultFromEmail($defaultFromEmail)
    {
        $this->defaultFromEmail = $defaultFromEmail;

        return $this;
    }

    /**
     * @param mixed $homeNews The news to display on home page.
     * @return self
     */
    public function setHomeNews($homeNews)
    {
        $this->homeNews = $homeNews;

        return $this;
    }

    /**
     * @param mixed $homeEvents The events to display on home page.
     * @return self
     */
    public function setHomeEvents($homeEvents)
    {
        $this->homeEvents = $homeEvents;

        return $this;
    }

    /**
     * @param array $newsConfig The news configuration object.
     * @return $this
     */
    public function setNews(array $newsConfig)
    {
        if (!$this->newsConfig) {
            $this->newsConfig = new NewsConfig();
        }

        $this->newsConfig->setData($newsConfig);

        return $this;
    }

    /**
     * @param array $eventConfig The event configuration object.
     * @return $this
     */
    public function setEvent(array $eventConfig)
    {
        if (!$this->eventConfig) {
            $this->eventConfig = new EventConfig();
        }

        $this->eventConfig->setData($eventConfig);

        return $this;
    }

    /**
     * @param array $sectionConfig The section configuration object.
     * @return $this
     */
    public function setSection(array $sectionConfig)
    {
        if (!$this->sectionConfig) {
            $this->sectionConfig = new SectionConfig();
        }

        $this->sectionConfig->setData($sectionConfig);

        return $this;
    }

    /**
     * @param string $contactCategory Must conform City\\Support\\Interface\\ContactCategoryInterface.
     * @return self
     */
    public function setContactCategoryObj($contactCategory)
    {
        $this->contactCategoryObj = $contactCategory;

        return $this;
    }

    /**
     * @param string $contactObj Must conform City\\Support\\Interface\\ContactInterface.
     * @return self
     */
    public function setContactObj($contactObj)
    {
        $this->contactObj = $contactObj;

        return $this;
    }

    /**
     * @param string $defaultContactCategory The default contact category fallback.
     * @return self
     */
    public function setDefaultContactCategory($defaultContactCategory)
    {
        $this->defaultContactCategory = $defaultContactCategory;

        return $this;
    }

    /**
     * @param array $dateFormats Formats for full dates.
     * @return self
     */
    public function setDateFormats(array $dateFormats)
    {
        $this->dateFormats = array_replace_recursive(
            $this->dateFormats,
            $dateFormats
        );

        return $this;
    }

    /**
     * @param array $timeFormats Formats for time.
     * @return self
     */
    public function setTimeFormats(array $timeFormats)
    {
        $this->timeFormats = array_replace_recursive(
            $this->timeFormats,
            $timeFormats
        );

        return $this;
    }

    // ==========================================================================
    // GETTERS
    // ==========================================================================

    /**
     * @return mixed
     */
    public function defaultFromEmail()
    {
        return $this->defaultFromEmail;
    }

    /**
     * @return mixed
     */
    public function homeNews()
    {
        return $this->homeNews;
    }

    /**
     * @return mixed
     */
    public function homeEvents()
    {
        return $this->homeEvents;
    }

    /**
     * @return NewsConfig
     */
    public function newsConfig()
    {
        return $this->newsConfig;
    }

    /**
     * @return EventConfig
     */
    public function eventConfig()
    {
        return $this->eventConfig;
    }

    /**
     * @return SectionConfig
     */
    public function sectionConfig()
    {
        return $this->sectionConfig;
    }

    /**
     * @return string
     */
    public function contactCategoryObj()
    {
        return $this->contactCategoryObj;
    }

    /**
     * @return string
     */
    public function contactObj()
    {
        return $this->contactObj;
    }

    /**
     * @return string
     */
    public function defaultContactCategory()
    {
        return $this->defaultContactCategory;
    }

    /**
     * @return array
     */
    public function dateFormats()
    {
        return $this->dateFormats;
    }

    /**
     * @return array
     */
    public function timeFormats()
    {
        return $this->timeFormats;
    }
}
