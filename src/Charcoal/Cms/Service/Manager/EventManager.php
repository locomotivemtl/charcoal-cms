<?php

namespace Charcoal\Cms\Service\Manager;

use DateTime;
use DateTimeInterface;
use Exception;

// From 'charcoal-core'
use Charcoal\Model\Collection;
use Charcoal\Model\Model;

// From 'charcoal-object'
use Charcoal\Object\CategoryInterface;
use Charcoal\Object\CategoryTrait;

// From 'charcoal-cms'
use Charcoal\Cms\Config\CmsConfig;
use Charcoal\Cms\EventCategory;
use Charcoal\Cms\EventInterface;
use Charcoal\Cms\Service\Loader\EventLoader;

/**
 * Event manager
 */
class EventManager extends AbstractManager
{
    use CategoryTrait;

    /** @var EventInterface $currentEvent The current event. */
    private $currentEvent;

    /** @var integer $currentPage The current Page. */
    private $currentPage;

    /** @var integer $numPerPage Events by page. */
    private $numPerPage = 0;

    /** @var integer $numPage How many pages. */
    private $numPage;

    /** @var boolean $entryCycle Does the pager can cycle indefinitely. */
    private $entryCycle = false;

    /** @var EventInterface $nextEvent */
    private $nextEvent;

    /** @var EventInterface $prevEvent */
    private $prevEvent;

    /** @var integer $page The page number. */
    private $page = 0;

    /** @var integer $category Id for category. */
    private $category = 0;

    /** @var EventInterface[] $all All the events. */
    private $all = [];

    /** @var EventInterface[] $entries The event collection. */
    private $entries = [];

    /** @var EventInterface[] $archive The archive events collection. */
    private $archive = [];

    /** @var EventInterface $entry An event. */
    private $entry;

    /** @var object $objType The event object model. */
    private $objType;

    /** @var string $featIdent The config ident for featured events. */
    private $featIdent;

    /** @var EventInterface[] $featList The config ident for featured events. */
    private $featList = [];

    /** @var EventLoader $loader The event loader provider. */
    private $loader;

    /** @var array $mapEvents The events mapped per [year][month][date]. */
    private $mapEvents = [];

    /** @var datetime $date Datetime filter */
    private $date;

    /** @var mixed $year Year filter. */
    private $year;

    /** @var mixed $month Month filter. */
    private $month;

    /** @var mixed $day Day filter. */
    private $day;

    /**
     * EventManager constructor.
     * @param array $data The Data.
     * @throws Exception When $data index is not set.
     */
    public function __construct(array $data)
    {
        parent::__construct($data);

        if (!isset($data['event/loader'])) {
            throw new Exception('Event Loader must be defined in the EventManager constructor.');
        }

        $this->setLoader($data['event/loader']);

        /** @var CmsConfig $eventConfig */
        $eventConfig = $this->adminConfig()->eventConfig();

        // City.json
        $this->setNumPerPage($eventConfig->get('numPerPage'));
        $this->setEntryCycle($eventConfig->get('entryCycle'));
        $this->setObjType($eventConfig->get('objType'));
        $this->setCategoryItemType($eventConfig->get('category'));
        $this->setFeatIdent($eventConfig->get('configFeatIdent'));
    }

    /**
     * To be displayed events list.
     * @return mixed The event collection.
     */
    public function entries()
    {
        // Used loader
        $loader = $this->loader()->upcoming();

        // Pagination
        $page = $this->page();

        // Filters
        $cat = $this->category();
        $date = $this->date();
        $year = $this->year();
        $month = $this->month();
        $day = $this->day();

        // Basicly.
        if ($year && $month && $day && !$date) {
            $date = new DateTime($year.'-'.$month.'-'.$day);
        }

        // Category is still valid.
        $extraSql = '';
        if ($cat) {
            $extraSql = '
            AND
                \''.$cat.'\' IN (category)';
        }

        // Get event from specific date.
        if ($date) {
            $loader = $this->loader()->all();
            $proto = $this->loader()->proto();
            $table = $proto->source()->table();
            $q = 'SELECT * FROM '.$table.'
                WHERE
                    \''.$date->format('Y-m-d').'\'
                BETWEEN
                    DATE(start_date) AND DATE(end_date)
                AND
                    active = 1'.$extraSql;

            $collection = $loader->loadFromQuery($q);

            return $collection;
        }

        // YEAR only filter.
        if ($year && !$month) {
            $loader = $this->loader()->all();
            $proto = $this->loader()->proto();
            $table = $proto->source()->table();
            $q = 'SELECT * FROM '.$table.'
                WHERE
                    \''.$this->year().'\' = YEAR(start_date)
                OR
                    \''.$this->year().'\' = YEAR(end_date)
                AND
                    active = 1'.$extraSql;

            $collection = $loader->loadFromQuery($q);

            return $collection;
        }

        // Year AND month filter.
        if ($year && $month) {
            $between = new DateTime($year.'-'.$month.'-01');
            $loader = $this->loader()->all();
            $proto = $this->loader()->proto();
            $table = $proto->source()->table();
            $q = 'SELECT * FROM '.$table.'
                WHERE
                    \''.$between->format('Y-m-d H:i:s').'\'
                BETWEEN
                    CAST(CONCAT(YEAR(start_date), \'-\', MONTH(start_date), \'-\', 01) AS DATETIME)
                AND
                    CAST(CONCAT(YEAR(end_date), \'-\', MONTH(end_date), \'-\', 01) AS DATETIME)
                AND
                    active = 1'.$extraSql;

            $collection = $loader->loadFromQuery($q);

            return $collection;
        }

        if (isset($this->entries[$cat])) {
            if (isset($this->entries[$cat][$page])) {
                return $this->entries[$cat][$page];
            }
        }

        if ($this->category()) {
            $loader->addFilter('category', $this->category(), [ 'operator' => 'in' ]);
        }

        if ($this->numPerPage()) {
            $loader->setPage($page);
            $loader->setNumPerPage($this->numPerPage());
        }
        $this->entries[$cat][$page] = $loader->load();

        return $this->entries[$cat][$page];
    }

    /**
     * @param integer|null $id The event id.
     * @return mixed
     */
    public function entry($id = null)
    {
        if (!$id) {
            return $this->currentEvent();
        }

        if (!isset($this->entry[$id])) {
            /** @var Model $model */
            $model = $this->modelFactory();
            /** @var EventInterface $entry */
            $entry = $model->create($this->objType())->loadfrom('id', $id);
            $this->entry[$id] = $entry->id() ? $entry : $this->currentEvent();
        }

        return $this->entry[$id];
    }

    /**
     * All available events
     * @return EventInterface[]|Collection The events collection
     */
    public function all()
    {
        if ($this->all) {
            return $this->all;
        }

        $this->all = $this->loader()->all()
            ->addOrder('start_date', 'asc')->load();

        return $this->all;
    }

    /**
     * @return CategoryInterface[]|Collection The category collection.
     */
    public function loadCategoryItems()
    {
        /** @var Model $model */
        $model = $this->modelFactory();
        $proto = $model->create($this->categoryItemType());
        $loader = $this->collectionLoader()->setModel($proto);
        $loader->addFilter('active', true);

        return $loader->load();
    }

    /**
     * @param integer $id The category id.
     * @return CategoryInterface|EventCategory
     */
    public function categoryItem($id)
    {
        $category = $this->modelFactory()->create($this->categoryItemType());

        return $category->load($id);
    }

    /**
     * Get featured events from config objects with custom filters as options.
     * @return mixed
     * @param array $options The options for the collection loader.
     * @throws Exception When featured news ident is not valid.
     */
    public function featList(array $options = [])
    {
        if ($this->featList) {
            return $this->featList;
        }

        $loader = $this->loader()->published();
        $ident = $this->featIdent();
        $config = $this->adminConfig();

        if (!$ident || !method_exists($config, $ident)) {
            throw new Exception(sprintf(
                'The featured news ident "%s" doesn\'t exist the class "%s"',
                $ident,
                get_class($config)
            ));
        }
        $ids = $config->{$ident}();

        if (!$ids) {
            return null;
        }

        $ids = explode(',', $ids);

        $loader->addFilter('id', $ids, [ 'operator' => 'in' ])
            ->addOrder('id', 'values', [ 'values' => $ids ]);

        if (count($options) > 0) {
            foreach ($options as $key => $option) {
                switch ($key) {
                    case 'filters':
                        $filters = $option;
                        foreach ($filters as $f) {
                            $filter[] = $f['property'] ?: '';
                            $filter[] = $f['value'] ?: '';
                            $filter[] = $f['options'] ?: '';
                            $filter = join(',', $filter);

                            $loader->addFilter($filter);
                        }
                        break;
                    case 'page':
                        $loader->setPage($option);
                        break;
                    case 'numPerPage':
                        $loader->setNumPerPage($option);
                        break;
                }
            }
        }

        $this->featList = $loader->load();

        return $this->featList;
    }

    /**
     * @return EventInterface[]|Collection
     */
    public function archive()
    {
        $page = $this->page();
        $cat = $this->category();
        if (isset($this->archive[$cat])) {
            if (isset($this->archive[$cat][$page])) {
                return $this->archive[$cat][$page];
            }
        }

        $loader = $this->loader()->archive();

        if ($this->category()) {
            $loader->addFilter('category', $this->category(), [ 'operator' => 'in' ]);
        }
        if ($this->numPerPage()) {
            $loader->setPage($this->page());
            $loader->setNumPerPage($this->numPerPage());
        }

        $this->archive[$cat][$page] = $loader->load();

        return $this->archive[$cat][$page];
    }

    /**
     * Get the latest event.
     * @return EventInterface|array The latest event.
     */
    public function latest()
    {
        $entries = $this->entries();

        if (isset($entries[0])) {
            return $entries[0];
        } else {
            // NO EVENT!
            return [];
        }
    }

    /**
     * @return mixed The previous event
     */
    public function prev()
    {
        if ($this->prevEvent) {
            return $this->prevEvent;
        } else {
            return $this->setPrevNext()->prevEvent;
        }
    }

    /**
     * @return mixed The next event
     */
    public function next()
    {
        if ($this->nextEvent) {
            return $this->nextEvent;
        } else {
            return $this->setPrevNext()->nextEvent;
        }
    }

    /**
     * @return float|integer The current event index page ident.
     */
    public function currentPage()
    {
        if ($this->currentPage) {
            return $this->currentPage;
        }
        if (!$this->currentEvent() || !$this->currentEvent()['id']) {
            $this->currentPage = 1;

            return 1;
        }
        $all = $this->all();
        $i = 0;
        foreach ($all as $event) {
            $i++;
            if ($event->id() == $this->currentEvent()['id']) {
                break;
            }
        }

        $this->currentPage = $this->numPerPage() ? ceil($i / $this->numPerPage()) : 1;

        return $this->currentPage;
    }

    /**
     * @param mixed $date The date from which to load.
     * @return array
     */
    public function getEventsByDate($date)
    {
        if (!($date instanceof DateTimeInterface)) {
            $date = new DateTime($date);
        }

        $map = $this->mapEvents();
        $year = $date->format('Y');
        $month = $date->format('m');
        $day = $date->format('d');

        if (isset($map[$year][$month][$day])) {
            return $map[$year][$month][$day];
        }

        return [];
    }

    /**
     * @return mixed
     */
    public function currentEvent()
    {
        if (!$this->currentEvent) {
            $this->currentEvent = $this->latest();
        }

        return $this->currentEvent;
    }

    /**
     * @return integer
     */
    public function numPerPage()
    {
        return $this->numPerPage;
    }

    /**
     * @return boolean
     */
    public function entryCycle()
    {
        return $this->entryCycle;
    }

    /**
     * Amount of event (total)
     * @return integer How many event?
     */
    public function numEvent()
    {
        return !!(count($this->entries()));
    }

    /**
     * The total amount of pages.
     * @return float
     */
    public function numPages()
    {
        if ($this->numPage) {
            $this->numPage;
        };

        $entries = $this->entries();
        $count = count($entries);

        if ($this->numPerPage()) {
            $this->numPage = ceil($count / $this->numPerPage());
        } else {
            $this->numPage = 1;
        }

        return $this->numPage;
    }

    /**
     * Is there a pager.
     * @return boolean
     */
    public function hasPager()
    {
        return ($this->numPages() > 1);
    }

    /**
     * @return integer
     */
    public function page()
    {
        return $this->page;
    }

    /**
     * @return integer
     */
    public function category()
    {
        return $this->category;
    }

    /**
     * @return mixed
     */
    public function objType()
    {
        return $this->objType;
    }

    /**
     * @return mixed
     */
    public function featIdent()
    {
        return $this->featIdent;
    }

    /**
     * @return EventLoader
     */
    public function loader()
    {
        return $this->loader;
    }

    /**
     * Datetime object OR null.
     * @return mixed Datetime or null.
     */
    public function date()
    {
        return $this->date;
    }

    /**
     * Full year
     * @return integer Full year.
     */
    public function year()
    {
        return $this->year;
    }

    /**
     * Month
     * @return mixed month.
     */
    public function month()
    {
        return $this->month;
    }

    /**
     * Day
     * @return mixed day.
     */
    public function day()
    {
        return $this->day;
    }

    /**
     * @param mixed $currentEvent The current event context.
     * @return self
     */
    public function setCurrentEvent($currentEvent)
    {
        $this->currentEvent = $currentEvent;

        return $this;
    }

    /**
     * @param integer $numPerPage The number of event per page.
     * @return self
     */
    public function setNumPerPage($numPerPage)
    {
        $this->numPerPage = $numPerPage;

        return $this;
    }

    /**
     * @param boolean $entryCycle Next and Prev cycles indefinitely.
     * @return self
     */
    public function setEntryCycle($entryCycle)
    {
        $this->entryCycle = $entryCycle;

        return $this;
    }

    /**
     * @param integer $page The page number to load.
     * @return self
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @param integer $category The current entry category.
     * @return self
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @param mixed $objType The object type.
     * @return self
     */
    public function setObjType($objType)
    {
        $this->objType = $objType;

        return $this;
    }

    /**
     * @param mixed $featIdent The featured list ident.
     * @return self
     */
    public function setFeatIdent($featIdent)
    {
        $this->featIdent = $featIdent;

        return $this;
    }

    /**
     * @param EventLoader|null $loader The event loader provider.
     * @return self
     */
    public function setLoader($loader)
    {
        $this->loader = $loader;

        return $this;
    }

    /**
     * Set date filter.
     * @param DateTime $date Date filter.
     * @return self
     */
    public function setDate(DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Full year.
     * @param mixed $year Full year.
     * @throws Exception If argument is not scalar.
     * @return EventManager
     */
    public function setYear($year)
    {
        if (!is_scalar($year)) {
            throw new Exception('Year must be a string or an integer in EventManager setYear method.');
        }
        $this->year = $year;

        return $this->year;
    }

    /**
     * Month.
     * @param mixed $month Specific month.
     * @throws Exception If argument is not scalar.
     * @return EventManager
     */
    public function setMonth($month)
    {
        if (!is_scalar($month)) {
            throw new Exception('Month must be a string or an integer in EventManager setMonth method.');
        }
        $this->month = $month;

        return $this;
    }

    /**
     * Day.
     * @param mixed $day Specific day.
     * @throws Exception If argument is not scalar.
     * @return EventManager
     */
    public function setDay($day)
    {
        if (!is_scalar($day)) {
            throw new Exception('Day must be a string or an integer in EventManager setDay method.');
        }
        $this->day = $day;

        return $this;
    }

    /**
     * Set the Prev and Next event
     * @return $this
     */
    public function setPrevNext()
    {
        if ($this->prevEvent && $this->nextEvent) {
            return $this;
        }
        $entries = $this->entries();

        $isPrev = false;
        $isNext = false;
        $firstEvent = false;
        $lastEvent = false;

        foreach ($entries as $event) {
            // Obtain th first event.
            if (!$firstEvent) {
                $firstEvent = $event;
            }
            $lastEvent = $event;
            // Find the current event
            if ($event->id() == $this->currentEvent()['id']) {
                $isNext = true;
                $isPrev = true;

                continue;
            }
            if (!$isPrev) {
                $this->prevEvent = $event;
            }
            // Store the next event
            if ($isNext) {
                $this->nextEvent = $event;

                $isNext = false;
            }
        }

        if ($this->entryCycle()) {
            if (!$this->nextEvent) {
                $this->nextEvent = $firstEvent;
            }

            if (!$this->prevEvent) {
                $this->prevEvent = $lastEvent;
            }
        }

        return $this;
    }

    /**
     * Mapping between events and dates
     * @return array The array containing events stored as [$year][$month][$day][event]
     */
    public function mapEvents()
    {
        if ($this->mapEvents) {
            return $this->mapEvents;
        }

        $events = $this->all();

        $out = [];
        foreach ($events as $ev) {
            $firstDate = $ev->startDate();
            $lastDate = $ev->endDate();

            $current = new DateTime();
            $current->setTimestamp($firstDate->getTimestamp());

            while ($current <= $lastDate) {
                $year = $current->format('Y');
                $month = $current->format('m');
                $day = $current->format('d');

                if (!isset($out[$year])) {
                    $out[$year] = [];
                }

                if (!isset($out[$year][$month])) {
                    $out[$year][$month] = [];
                }

                if (!isset($out[$year][$month][$day])) {
                    $out[$year][$month][$day] = [];
                }

                $out[$year][$month][$day][] = $ev;

                $current->modify('+1 day');
            }
        }

        $this->mapEvents = $out;

        return $this->mapEvents;
    }
}
