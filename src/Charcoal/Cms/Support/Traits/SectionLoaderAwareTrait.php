<?php

namespace Charcoal\Cms\Support\Traits;

// From Slim
use Slim\Exception\ContainerException;

// From 'charcoal-core'
use Charcoal\Model\ModelInterface;

// From 'charcoal-cms'
use Charcoal\Cms\Service\Loader\SectionLoader;
use Charcoal\Cms\SectionInterface;

/**
 *
 */
trait SectionLoaderAwareTrait
{
    /**
     * @var SectionInterface $section
     */
    private $section;

    /**
     * @var SectionInterface[] $sections
     */
    private $sections;

    /**
     * @var SectionInterface[] $masterSections
     */
    private $masterSections;

    /**
     * @var SectionInterface[] $childrenSections
     */
    private $childrenSections;

    /**
     * @var SectionLoader $sectionLoader City\\Loader\\SectionLoader.
     */
    private $sectionLoader;

    // ==========================================================================
    // INIT
    // ==========================================================================

    /**
     * Must call section if the context object is of type Section.
     * @param ModelInterface $context The current context.
     * @return mixed
     */
    abstract public function setContextObject(ModelInterface $context);

    /**
     * @return \ArrayAccess|\Traversable
     */
    public function sections()
    {
        return $this->sectionLoader()->all()->load();
    }

    /**
     * @return \ArrayAccess|\Traversable
     */
    public function masterSections()
    {
        return $this->sectionLoader()->masters();
    }

    /**
     * @return \ArrayAccess|\Traversable
     */
    public function childrenSections()
    {
        return $this->sectionLoader()->children();
    }

    /**
     * Gets latest route for the given slug.
     * @return string The latest url.
     */
    public function routes()
    {
        return function ($arg) {
            return $this->sectionLoader()->resolveRoute($arg);
        };
    }

    /**
     * Gets the current section based on AbstractTemplate::Section and context.
     * @param boolean $raw Option the receive the non-formatted section.
     * @return array|SectionInterface
     */
    public function currentSection($raw = false)
    {
        if ($raw) {
            return $this->section();
        }

        return $this->formatSection($this->section());
    }

    /**
     * @param string $slug The section slug to load from.
     * @return SectionInterface|array
     */
    public function sectionFromSlug($slug)
    {
        return $this->sectionLoader()->fromSlug($slug);
    }

    // ==========================================================================
    // GETTERS
    // ==========================================================================

    /**
     * @return SectionInterface
     */
    protected function section()
    {
        return $this->section;
    }

    // ==========================================================================
    // SETTERS
    // ==========================================================================

    /**
     * @param SectionInterface $section The current section.
     * @return self
     */
    protected function setSection(SectionInterface $section)
    {
        $this->section = $section;

        return $this;
    }

    // ==========================================================================
    // DEPENDENCIES
    // ==========================================================================

    /**
     * @return SectionLoader
     * @throws ContainerException When dependency is missing.
     */
    protected function sectionLoader()
    {
        if (!$this->sectionLoader instanceof SectionLoader) {
            throw new ContainerException(sprintf(
                'Missing dependency for %s: %s',
                get_called_class(),
                SectionLoader::class
            ));
        }

        return $this->sectionLoader;
    }

    /**
     * @param SectionLoader $loader The section loader.
     * @return self
     */
    protected function setSectionLoader(SectionLoader $loader)
    {
        $this->sectionLoader = $loader;

        return $this;
    }

    // ==========================================================================
    // FORMATTER
    // ==========================================================================

    /**
     * @param SectionInterface $section The section to format.
     * @return array
     */
    protected function formatSection(SectionInterface $section)
    {
        $contentBlocks = $section->getAttachments('content-blocks');
        $gallery = $section->getAttachments('image-gallery');
        $documents = $section->getAttachments('document');

        return [
            'title'         => (string)$section->title(),
            'summary'       => (string)$section->summary(),
            'image'         => (string)$section->image(),
            'content'       => (string)$section->content(),
            'contentBlocks' => $contentBlocks,
            'gallery'       => $gallery,
            'documents'     => $documents
        ];
    }
}
