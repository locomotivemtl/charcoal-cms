<?php

namespace Charcoal\Cms\Support\Traits;

// From Slim
use Slim\Exception\ContainerException;

// From 'charcoal-object'
use Charcoal\Object\CategoryInterface;

// From 'charcoal-cms'
use Charcoal\Cms\NewsInterface;
use Charcoal\Cms\Service\Manager\NewsManager;

/**
 *
 */
trait NewsManagerAwareTrait
{
    /**
     * Currently displayed news.
     * @var array $news City/Object/News
     */
    private $currentNews;

    /**
     * @var NewsManager $newsManager
     */
    private $newsManager;

    // ==========================================================================
    // FUNCTIONS
    // ==========================================================================

    /**
     * Formatted news list
     * Returns the entries for the current page.
     * @return \Generator|void
     */
    public function newsList()
    {
        $entries = $this->newsManager()->entries();
        foreach ($entries as $news) {
            yield $this->newsFormatShort($news);
        }
    }

    /**
     * Formatted news archive list
     * Returns the entries for the current page.
     * @return \Generator|void
     */
    public function newsArchiveList()
    {
        $entries = $this->newsManager()->archive();
        foreach ($entries as $entry) {
            yield $this->newsFormatShort($entry);
        }
    }

    /**
     * Current news.
     * @return array The properties of the current news
     */
    public function currentNews()
    {
        if ($this->currentNews) {
            return $this->currentNews;
        }

        // The the current news
        $news = $this->newsManager()->entry();

        // Format the news if there's any
        if ($news) {
            $this->currentNews = $this->newsFormatFull($news);
        }

        return $this->currentNews;
    }

    /**
     * @return \Generator
     */
    public function featNews()
    {
        $entries = $this->newsManager()->featList();

        if (count($entries) > 0) {
            foreach ($entries as $entry) {
                if ($entry->id()) {
                    yield $this->newsFormatFull($entry);
                }
            }
        }
    }

    /**
     * Next news in list.
     * @return array The next news properties.
     */
    public function nextNews()
    {
        $next = $this->newsManager()->next();

        if (!$next) {
            return null;
        }

        return $this->newsFormatNav($next);
    }

    /**
     * Next news in list.
     * @return array The next news properties.
     */
    public function prevNews()
    {
        $prev = $this->newsManager()->prev();

        if (!$prev) {
            return null;
        }

        return $this->newsFormatNav($prev);
    }

    /**
     * Amount of news (total)
     * @return integer How many news?
     */
    public function numNews()
    {
        return $this->newsManager()->numNews();
    }

    /**
     * @return float
     */
    public function numNewsPages()
    {
        return $this->newsManager()->numPages();
    }

    /**
     * @return boolean
     */
    public function newsHasPager()
    {
        return $this->newsManager()->hasPager();
    }

    /**
     * @return \Generator
     */
    public function newsCategoryList()
    {
        $cats = $this->newsManager()->loadCategoryItems();
        foreach ($cats as $cat) {
            yield $this->newsFormatCategory($cat);
        }
    }

    /**
     * @param NewsInterface $news Charcoal\Cms\NewsInterface.
     * @return CategoryInterface
     */
    public function newsCategory(NewsInterface $news)
    {
        $id = $news->category();

        return $this->newsManager()->categoryItem($id);
    }

    // ==========================================================================
    // FORMATTER
    // ==========================================================================

    /**
     * @param NewsInterface $news Charcoal\Cms\NewsInterface.
     * @return string
     */
    protected function getNewsDateFormat(NewsInterface $news)
    {
        return $this->dateHelper()->formatDate(
            $news->newsDate()
        );
    }

    /**
     * Formatting expected in templates
     * @param NewsInterface $news A single news.
     * @return array The needed news properties.
     */
    protected function newsFormatShort(NewsInterface $news)
    {
        return [
            'title'        => (string)$news->title(),
            'url'          => (string)$news->url(),
            'date'         => $this->getNewsDateFormat($news),
            'dateTime'     => $news->newsDate()->format('Y-m-d h:i'),
            'active'       => ($this->currentNews() && ($this->currentNews()['id'] == $news->id())),
            'category'     => $news->category(),
            'categoryName' => $this->newsCategory($news)->name()
        ];
    }

    /**
     * Formatting expected in templates
     * @param NewsInterface $news A single news.
     * @return array The needed news properties.
     */
    protected function newsFormatNav(NewsInterface $news)
    {
        return [
            'date'         => $this->getNewsDateFormat($news),
            'dateTime'     => $news->newsDate()->format('Y-m-d h:i'),
            'title'        => (string)$news->title(),
            'url'          => $news->url(),
            'category'     => $news->category(),
            'categoryName' => $this->newsCategory($news)->name()
        ];
    }

    /**
     * @param NewsInterface $news The current news.
     * @return array The needed properties.
     */
    protected function newsFormatFull(NewsInterface $news)
    {
        $contentBlocks = $news->getAttachments('content-blocks');
        $gallery = $news->getAttachments('image-gallery');
        $documents = $news->getAttachments('document');

        return [
            'id'               => $news->id(),
            'title'            => (string)$news->title(),
            'summary'          => (string)$news->summary(),
            'content'          => (string)$news->content(),
            'image'            => $news->image(),
            'date'             => $this->getNewsDateFormat($news),
            'dateTime'         => $news->newsDate()->format('Y-m-d h:i'),
            'contentBlocks'    => $contentBlocks,
            'hasContentBlocks' => !!(count($contentBlocks)),
            'documents'        => $documents,
            'hasDocuments'     => !!(count($documents)),
            'gallery'          => $gallery,
            'hasGallery'       => !!(count($gallery)),
            'url'              => $news->url(),
            'metaTitle'        => (string)$news->metaTitle(),
            'category'         => $news->category(),
            'categoryName'     => $this->newsCategory($news)->name()
        ];
    }

    /**
     * @param CategoryInterface $category The category item.
     * @return array The formatted category item.
     */
    protected function newsFormatCategory(CategoryInterface $category)
    {
        return [
            'id'   => $category->id(),
            'name' => (string)$category->name(),
        ];
    }

    // ==========================================================================
    // DEPENDENCIES
    // ==========================================================================

    /**
     * @return NewsManager
     * @throws ContainerException When dependency is missing.
     */
    protected function newsManager()
    {
        if (!$this->newsManager instanceof NewsManager) {
            throw new ContainerException(sprintf(
                'Missing dependency for %s: %s',
                get_called_class(),
                NewsManager::class
            ));
        }

        return $this->newsManager;
    }

    /**
     * @param NewsManager $newsManager The news Manager class.
     * @return self
     */
    protected function setNewsManager(NewsManager $newsManager)
    {
        $this->newsManager = $newsManager;

        return $this;
    }

    /**
     * dateHelperAwareTrait dependency
     * @return mixed
     */
    abstract protected function dateHelper();
}
