<?php

namespace Charcoal\Tests\Cms\Mock;

// From 'charcoal-app'
use Charcoal\App\Template\AbstractTemplate;

// From 'charcoal-cms'
use Charcoal\Cms\SectionInterface;

/**
 * Home Template Controller
 */
class HomeTemplate extends AbstractTemplate
{
    /**
     * @var SectionInterface $section
     */
    private $section;

    /**
     * @return SectionInterface
     */
    public function section()
    {
        return $this->section;
    }

    /**
     * @param  SectionInterface $section The current section.
     * @return self
     */
    public function setSection(SectionInterface $section)
    {
        $this->section = $section;

        return $this;
    }
}
