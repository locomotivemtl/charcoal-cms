<?php

namespace Charcoal\Cms\Service\Manager;

use Exception;

// From 'charcoal-core'
use Charcoal\Model\Collection;

// From 'charcoal-object'
use Charcoal\Object\CategoryInterface;
use Charcoal\Object\CategoryTrait;

// From 'charcoal-cms'
use Charcoal\Cms\Config\CmsConfig;
use Charcoal\Cms\NewsInterface;
use Charcoal\Cms\Service\Loader\NewsLoader;

/**
 * News manager
 */
class NewsManager extends AbstractManager
{
    use CategoryTrait;

    /** @var NewsInterface $currentNews The current news. */
    protected $currentNews;

    /** @var integer $currentPage The current Page. */
    protected $currentPage;

    /** @var integer $numPerPage News by page. */
    protected $numPerPage = 0;

    /** @var integer $numPages How many pages. */
    protected $numPages;

    /** @var boolean $pageCycle Does the pager can cycle indefinitely. */
    protected $entryCycle = false;

    /** @var NewsInterface $nextNews */
    protected $nextNews;

    /** @var NewsInterface $prevNews */
    protected $prevNews;

    /** @var integer $page */
    protected $page = 0;

    /** @var integer $category */
    protected $category = 0;

    /** @var NewsInterface[] $all All the news. */
    protected $all = [];

    /** @var NewsInterface[] $entries The news collection. */
    protected $entries = [];

    /** @var NewsInterface[] $archive The archive news collection. */
    protected $archive = [];

    /** @var NewsInterface $entry A news. */
    protected $entry;

    /** @var object $objType The news object model. */
    protected $objType;

    /** @var string $featIdent The config ident for featured news. */
    protected $featIdent;

    /** @var NewsInterface[] $featList The config ident for featured news. */
    protected $featList = [];

    /** @var NewsLoader $loader The news loader provider. */
    protected $loader;

    /**
     * @var array
     */
    protected $categoryItem = [];

    /**
     * NewsManager constructor.
     * @param array $data The Data.
     * @throws Exception When $data index is not set.
     */
    public function __construct(array $data)
    {
        parent::__construct($data);

        if (!isset($data['news/loader'])) {
            throw new Exception('News Loader must be defined in the NewsManager constructor.');
        }

        $this->setLoader($data['news/loader']);

        /** @var CmsConfig $newsConfig */
        $newsConfig = $this->adminConfig()->newsConfig();

        $this->setNumPerPage($newsConfig->get('numPerPage'));
        $this->setEntryCycle($newsConfig->get('entryCycle'));
        $this->setObjType($newsConfig->get('objType'));
        $this->setCategoryItemType($newsConfig->get('category'));
        $this->setFeatIdent($newsConfig->get('configFeatIdent'));
    }

    /**
     * To be displayed news list.
     * @return mixed The news collection.
     */
    public function entries()
    {
        $page = $this->page();
        $cat = $this->category();
        if (isset($this->entries[$cat])) {
            if (isset($this->entries[$cat][$page])) {
                return $this->entries[$cat][$page];
            }
        }

        $loader = $this->entriesLoader();

        $this->entries[$cat][$page] = $loader->load();

        return $this->entries[$cat][$page];
    }

    /**
     * @return \Charcoal\Loader\CollectionLoader
     */
    public function entriesLoader()
    {
        $loader = $this->loader()->upcoming();
        $loader->addOrder('news_date', 'desc');

        if ($this->category()) {
            $loader->addFilter('category', $this->category(), [ 'operator' => 'in' ]);
        }
        if ($this->numPerPage()) {
            $loader->setPage($this->page());

            $numPerPage = !!($this->page()) ? $this->numPerPage() : 0;
            $loader->setNumPerPage($numPerPage);
        }

        return $loader;
    }

    /**
     * @param integer|null $id The news id.
     * @return mixed
     */
    public function entry($id = null)
    {
        if (!$id) {
            return $this->currentNews();
        }

        if (!isset($this->entry[$id])) {
            $entry = $this->modelFactory()->create($this->objType())->loadFrom('id', $id);
            $this->entry[$id] = $entry->id() ? $entry : $this->currentNews();
        }

        return $this->entry[$id];
    }

    /**
     * All available news.
     * @return NewsInterface[]|Collection The news collection.
     */
    public function all()
    {
        if ($this->all) {
            return $this->all;
        }

        $this->all = $this->loader()->all()->addOrder('news_date', 'desc')->load();

        return $this->all;
    }

    /**
     * @return CategoryInterface[]|Collection The category collection.
     */
    public function loadCategoryItems()
    {
        $proto = $this->modelFactory()->create($this->categoryItemType());
        $loader = $this->collectionLoader()->setModel($proto);
        $loader->addFilter('active', true);

        return $loader->load();
    }

    /**
     * @param integer $id The category id.
     * @return CategoryInterface
     */
    public function categoryItem($id)
    {
        if (isset($this->categoryItem[$id])) {
            return $this->categoryItem[$id];
        }
        $category = $this->modelFactory()->create($this->categoryItemType());

        $this->categoryItem[$id] = $category->load($id);

        return $this->categoryItem[$id];
    }

    /**
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
     * @return NewsInterface[]|Collection
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
        $loader->addOrder('news_date', 'desc');

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
     * Get the latest news.
     * @return NewsInterface|array The latest news.
     */
    public function latest()
    {
        $entries = $this->entries();

        if (isset($entries[0])) {
            return $entries[0];
        } else {
            // NO NEWS!
            return [];
        }
    }

    /**
     * @return mixed The previous news
     */
    public function prev()
    {
        if ($this->prevNews) {
            return $this->prevNews;
        }

        return $this->setPrevNext()->prevNews;
    }

    /**
     * @return mixed The next news
     */
    public function next()
    {
        if ($this->nextNews) {
            return $this->nextNews;
        }

        return $this->setPrevNext()->nextNews;
    }

    /**
     * @return float|integer The current news index page ident.
     */
    public function currentPage()
    {
        if ($this->currentPage) {
            return $this->currentPage;
        }
        if (!$this->currentNews() || !$this->currentNews()['id']) {
            $this->currentPage = 1;

            return 1;
        }
        $all = $this->all();
        $i = 0;
        foreach ($all as $news) {
            $i++;
            if ($news->id() == $this->currentNews()['id']) {
                break;
            }
        }

        $this->currentPage = $this->numPerPage() ? ceil($i / $this->numPerPage()) : 1;

        return $this->currentPage;
    }

    /**
     * @return mixed
     */
    public function currentNews()
    {
        if (!$this->currentNews) {
            $this->currentNews = $this->latest();
        }

        return $this->currentNews;
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
     * Amount of news (total)
     * @return integer How many news?
     */
    public function numNews()
    {
        return !!(count($this->entries()));
    }

    /**
     * The total amount of pages.
     * @return float
     */
    public function numPages()
    {
        return ceil($this->entriesLoader()->loadCount() / $this->numPerPage());
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
     * @return NewsLoader
     */
    public function loader()
    {
        return $this->loader;
    }

    /**
     * @param mixed $currentNews The current news context.
     * @return self .
     */
    public function setCurrentNews($currentNews)
    {
        $this->currentNews = $currentNews;

        return $this;
    }

    /**
     * @param integer $numPerPage The number of news per page.
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
     * @param integer $category The current news category.
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
     * @param NewsLoader $loader The news loader provider.
     * @return self
     */
    public function setLoader(NewsLoader $loader)
    {
        $this->loader = $loader;

        return $this;
    }

    /**
     * Set the Prev and Next news
     * @return $this
     */
    public function setPrevNext()
    {
        if ($this->nextNews && $this->prevNews) {
            return $this;
        }
        $entries = $this->entries();

        $isPrev = false;
        $isNext = false;
        $firstNews = false;
        $lastNews = false;

        /** @var NewsInterface $news */
        foreach ($entries as $news) {
            // Obtain th first news.
            if (!$firstNews) {
                $firstNews = $news;
            }
            $lastNews = $news;
            // Find the current news
            if ($news->id() == $this->currentNews()['id']) {
                $isNext = true;
                $isPrev = true;

                continue;
            }
            if (!$isPrev) {
                $this->prevNews = $news;
            }
            // Store the next news
            if ($isNext) {
                $this->nextNews = $news;

                $isNext = false;
            }
        }

        if ($this->entryCycle()) {
            if (!$this->nextNews) {
                $this->nextNews = $firstNews;
            }

            if (!$this->prevNews) {
                $this->prevNews = $lastNews;
            }
        }

        return $this;
    }
}
