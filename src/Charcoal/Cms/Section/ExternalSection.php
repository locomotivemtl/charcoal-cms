<?php

namespace Charcoal\Cms;

use Charcoal\Translation\TranslationString;

use Charcoal\Cms\AbstractSection;

/**
 * External section may appear in menus and breadcrumbs, but only
 *
 * Unlike all other section types, they do not provide any metadata information.
 */
class ExternalSection extends AbstractSection
{
    /**
     * @var TranslationString $externalUrl
     */
    private $externalUrl;

    /**
     * @param mixed $url The external URL (localized).
     * @return ExternalSection Chainable
     */
    public function setExternalUrl($url)
    {
        $this->externalUrl = new TranslationString($url);
        return $this;
    }

    /**
     * @return TranslationString
     */
    public function externalUrl()
    {
        return $this->externalUrl;
    }
}
