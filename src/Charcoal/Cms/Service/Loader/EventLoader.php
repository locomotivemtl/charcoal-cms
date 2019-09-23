<?php

namespace Charcoal\Cms\Service\Loader;

use DateTime;
use DateTimeInterface;

// From 'charcoal-core'
use Charcoal\Loader\CollectionLoader;

/**
 * Event Loader
 */
class EventLoader extends AbstractLoader
{
    /**
     * Event prototype.
     * @var \Charcoal\Model\ModelInterface $proto
     */
    private $proto;

    /**
     * @var string $lifespan The lifespan of events.
     */
    private $lifespan;

    /**
     * [proto description]
     * @return [type] [description]
     */
    public function proto()
    {
        if (!$this->proto) {
            $this->proto = $this->modelFactory()->get($this->objType());
        }

        return $this->proto;
    }

    /**
     * @return CollectionLoader
     */
    public function all()
    {
        $proto = $this->proto();
        $loader = $this->collectionLoader()->setModel($proto);
        $loader->addFilter('active', true);

        return $loader;
    }

    /**
     * @return CollectionLoader
     */
    public function published()
    {
        $now = new DateTime();
        $loader = $this->all();
        $loader->addFilter('publishDate', $now->format('Y-m-d H:i:s'), [ 'operator' => '<=' ])
            ->addOrder('start_date', 'asc');

        return $loader;
    }

    /**
     * Fetch upcoming entries based on the lifespan or now.
     * @return CollectionLoader
     */
    public function upcoming()
    {
        $lifespan = $this->lifespan();
        $date = is_string($lifespan) ? new DateTime($lifespan) : new DateTime();

        return $this->since($date);
    }

    /**
     * Fetch upcoming entries based on the lifespan or now.
     * @return CollectionLoader
     */
    public function archive()
    {
        $lifespan = $this->lifespan();
        $date = is_string($lifespan) ? new DateTime($lifespan) : new DateTime();

        return $this->to($date);
    }

    /**
     * @param mixed $date The news date to filter
     *                    [startDate, endDate]
     *                    DateTimeInterface
     *                    string.
     * @return CollectionLoader
     */
    public function since($date)
    {
        $date = $this->parseAsDate($date);
        $loader = $this->published();
        $loader->addFilter('end_date', $date->format('Y-m-d H:i:s'), [ 'operator' => '>=' ]);

        return $loader;
    }

    /**
     * @param mixed $date The news date to filter
     *                    [startDate, endDate]
     *                    DateTimeInterface
     *                    string.
     * @return CollectionLoader
     */
    public function to($date)
    {
        $date = $this->parseAsDate($date);
        $loader = $this->published();
        $loader->addFilter('end_date', $date->format('Y-m-d H:i:s'), [ 'operator' => '<' ]);

        return $loader;
    }

    /**
     * @return mixed
     */
    public function lifespan()
    {
        return $this->lifespan;
    }

    /**
     * @return object
     */
    public function objType()
    {
        return $this->objType;
    }

    /**
     * @param string $lifespan The lifespan of events.
     * @return self Chainable
     */
    public function setLifespan($lifespan)
    {
        $this->lifespan = $lifespan;

        return $this;
    }

    /**
     * @param object $objType The object type.
     * @return self Chainable
     */
    public function setObjType($objType)
    {
        $this->objType = $objType;

        return $this;
    }

    /**
     * @param mixed $date The date to convert.
     * @return DateTime
     */
    private function parseAsDate($date)
    {
        if ($date instanceof DateTimeInterface) {
            return $date;
        }

        return new DateTime($date);
    }
}
