<?php

namespace Charcoal\Cms\Service\Loader;

use Exception;

// From 'charcoal-core'
use Charcoal\Loader\CollectionLoader;

// From 'charcoal-object'
use Charcoal\Object\ObjectRoute;

// From 'charcoal-translator'
use Charcoal\Translator\TranslatorAwareTrait;

/**
 * Section Loader
 */
class SectionLoader extends AbstractLoader
{
    /**
     * @var array $sectionRoutes The section's routes.
     */
    protected $sectionRoutes;

    /**
     * @var integer $baseSection The id of the base section.
     */
    protected $baseSection;

    /**
     * Available section types.
     *
     * @var array
     */
    protected $sectionTypes = [];

    /**
     * The cache of snake-cased words.
     *
     * @var array
     */
    protected static $snakeCache = [];

    /**
     * @param integer $id The section's id.
     * @return mixed
     */
    public function fromId($id)
    {
        $proto = $this->modelFactory()->create($this->objType());

        return $proto->loadFrom('id', $id);
    }

    /**
     * @param string $slug The section's slug.
     * @return mixed
     */
    public function fromSlug($slug)
    {
        $id = $this->resolveSectionId($slug);
        return $this->fromId($id);
    }

    /**
     * @return CollectionLoader
     */
    public function all()
    {
        $proto = $this->modelFactory()->create($this->objType());
        $loader = $this->collectionLoader()->reset();
        $loader->setModel($proto);
        $loader->addFilter('active', true);
        $loader->addOrder('position', 'asc');

        return $loader;
    }

    /**
     * @return \ArrayAccess|\Traversable
     */
    public function masters()
    {
        $loader = $this->all();
        $operator = [];
        if (!$this->baseSection()) {
            $operator = [ 'operator' => 'IS NULL' ];
        }
        $loader->addFilter('master', $this->baseSection(), $operator);

        return $loader->load();
    }

    /**
     * @return \ArrayAccess|\Traversable
     */
    public function children()
    {
        $masters = $this->masters();

        $children = [];
        $hasChildren = count($masters) > 0;

        while ($hasChildren) {
            $ids = [];

            foreach ($masters as $master) {
                $ids[] = $master->id();
            }

            $masters = $this->all()
                ->addFilter([
                    'property' => 'master',
                    'value'      => $ids,
                    'operator' => 'IN'
                ])
                ->load();

            $children = array_merge($children, $masters);
            $hasChildren = count($masters) > 0;
        }

        return $children;
    }

    /**
     * Pair routes slug to sections ID
     * @return array
     */
    public function sectionRoutes()
    {
        if ($this->sectionRoutes) {
            return $this->sectionRoutes;
        }

        $proto = $this->modelFactory()->create(ObjectRoute::class);

        $sectionTypes = $this->sectionTypes();
        if (empty($sectionTypes)) {
            $sectionTypes = [
                'base' => $this->objType()
            ];
        }

        $loader = $this->collectionLoader()->reset();
        $loader->setModel($proto);

        $filters = [];
        foreach ($sectionTypes as $key => $val) {
            $filters[] = 'route_obj_type = \''.$val.'\'';
        }
        $q = 'SELECT * FROM `'.$proto->source()->table().'`
            WHERE active = 1 AND ('.implode(' OR ', $filters).')
            AND `route_options_ident` IS NULL
            ORDER BY creation_date ASC';

        $objectRoutes = $loader->loadFromQuery($q);

        // $loader->addFilter('route_obj_type', $this->objType())
        //     // This is important. This is why it all works
        //     // Loading it from the first created to the last created
        //     // makes the following foreach override previous data.
        //     ->addOrder('creation_date', 'asc');

        // $objectRoutes = $loader->load();

        $sections = [];
        $routes = [];
        // The current language
        $lang = $this->translator()->getLocale();
        foreach ($objectRoutes as $o) {
            if ($o['lang'] === $lang) {
                // Will automatically override previous slug set
                $sections[$o['routeObjId']] = $o['slug'];
            }
            // Keep track of EVERY slug.
            $routes[$o['slug']] = $o['routeObjId'];
        }

        $this->sectionRoutes = [
            'sections' => $sections,
            'routes'   => $routes
        ];

        return $this->sectionRoutes;
    }

    /**
     * Resolve latest route from route slug.
     * @param  string $route The route to resolve.
     * @return string
     */
    public function resolveRoute($route)
    {
        $routes = $this->sectionRoutes();
        $sId = $this->resolveSectionId($route);

        if (!isset($routes['sections'][$sId])) {
            return '';
        }

        return $routes['sections'][$sId];
    }

    /**
     * Resolve section ID from route slug.
     * @param  string $route The route to resolve.
     * @return integer
     */
    public function resolveSectionId($route)
    {
        $routes = $this->sectionRoutes();

        if (!isset($routes['routes'][$route])) {
            return '';
        }

        $sId = $routes['routes'][$route];

        return $sId;
    }

    /**
     * @return object
     */
    public function objType()
    {
        return $this->objType;
    }

    /**
     * @return integer
     */
    public function baseSection()
    {
        return $this->baseSection;
    }

    /**
     * @return array
     */
    public function sectionTypes()
    {
        return $this->sectionTypes;
    }

    /**
     * @param object $objType The object type.
     * @return self
     */
    public function setObjType($objType)
    {
        $this->objType = $objType;

        return $this;
    }

    /**
     * @param integer $baseSection The base section id.
     * @return self
     */
    public function setBaseSection($baseSection)
    {
        $this->baseSection = $baseSection;

        return $this;
    }

    /**
     * @param  array|null $sectionTypes Available section types.
     * @return self
     */
    public function setSectionTypes(array $sectionTypes = null)
    {
        $this->sectionTypes = $sectionTypes;

        return $this;
    }

    /**
     * Convert a string to snake case.
     *
     * @param  string $value     The value to convert.
     * @param  string $delimiter The word delimiter.
     * @return string
     */
    public static function snake($value, $delimiter = '-')
    {
        $key = $value;
        if (isset(static::$snakeCache[$key][$delimiter])) {
            return static::$snakeCache[$key][$delimiter];
        }
        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', $value);
            $value = mb_strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1'.$delimiter, $value), 'UTF-8');
        }
        static::$snakeCache[$key][$delimiter] = $value;

        return $value;
    }
}
