<?php

namespace Charcoal\Admin\Widget\Cms;

use Exception;
use RuntimeException;

// From 'charcoal-core'
use Charcoal\Model\ModelInterface;

// From 'charcoal-factory'
use Charcoal\Factory\FactoryInterface;

// From 'charcoal-property'
use Charcoal\Property\PropertyInterface;

// From 'charcoal-cms'
use Charcoal\Cms\AbstractSection;

/**
 *
 */
trait SectionTableTrait
{
    /**
     * Store the factory instance for the current class.
     *
     * @var FactoryInterface
     */
    private $sectionFactory;

    /**
     * Set an object model factory.
     *
     * @param FactoryInterface $factory The model factory, to create objects.
     * @return self
     */
    protected function setSectionFactory(FactoryInterface $factory)
    {
        $this->sectionFactory = $factory;

        return $this;
    }

    /**
     * Retrieve the object model factory.
     *
     * @throws RuntimeException If the model factory was not previously set.
     * @return FactoryInterface
     */
    public function sectionFactory()
    {
        if (!isset($this->sectionFactory)) {
            throw new RuntimeException(
                sprintf('Section Model Factory is not defined for "%s"', get_class($this))
            );
        }

        return $this->sectionFactory;
    }

    /**
     * Filter the property before its assigned to the object row.
     *
     * This method is useful for classes using this trait.
     *
     * @param  ModelInterface    $object        The current row's object.
     * @param  PropertyInterface $property      The current property.
     * @param  string            $propertyValue The property $key's display value.
     * @return array
     */
    protected function parsePropertyCell(
        ModelInterface $object,
        PropertyInterface $property,
        $propertyValue
    ) {
        $propertyIdent = $property->ident();

        switch ($propertyIdent) {
            case 'template_ident':
                if ($object->templateIdent() === 'volleyball/template/page') {
                    $propertyValue = sprintf(
                        '<span aria-hidden="true">─</span><span class="sr-only">%s</span>',
                        $this->translator()->translation([
                            'en' => 'Default Template',
                            'fr' => 'Modèle par défaut'
                        ])
                    );
                }
                break;

            case 'title':
            case 'menu_label':
                $sectionType = $object->sectionType();

                switch ($sectionType) {
                    case AbstractSection::TYPE_EXTERNAL:
                        $externalUrl = (string)$object->externalUrl();
                        $linkExcerpt = '';
                        $tagTemplate = '<span class="fa fa-link" data-toggle="tooltip" '.
                            'data-placement="auto" title="%1$s"></span>';

                        if ($externalUrl) {
                            $tagTemplate = '<a class="btn btn-secondary btn-sm" href="%2$s" target="_blank">'.
                                '<span class="fa fa-link" aria-hidden="true"></span> '.
                                '<span class="sr-only">URL:</span> %3$s'.
                                '</a>';

                            $linkExcerpt = $this->abridgeUri($externalUrl);
                        }

                        $p = $object->p('section_type');
                        $propertyValue .= sprintf(
                            ' &nbsp; '.$tagTemplate,
                            $p->displayVal($p->val()),
                            $externalUrl,
                            $linkExcerpt
                        );
                        break;
                }
                break;
        }

        if ($propertyIdent === 'title') {
            if (is_callable([ $object, 'navMenu' ]) && $object->navMenu()) {
                $propertyValue .= sprintf(
                    ' &nbsp; '.
                    '<span class="fa fa-list" data-toggle="tooltip" '.
                    'data-placement="auto" title="%s"></span>',
                    $this->translator()->translation([
                        'en' => 'Present in a menu',
                        'fr' => 'Présent dans un menu'
                    ])
                );
            }

            if (is_callable([ $object, 'locked' ]) && $object->locked()) {
                $propertyValue .= sprintf(
                    ' &nbsp; '.
                    '<span class="fa fa-lock" data-toggle="tooltip" '.
                    'data-placement="auto" title="%s"></span>',
                    $object->p('locked')['label']
                );
            }
        }

        return parent::parsePropertyCell($object, $property, $propertyValue);
    }

    /**
     * Retrieve an abridged variant to the given URI.
     *
     * @param string $uri A URI to possibly truncate.
     * @return string
     */
    private function abridgeUri($uri)
    {
        $i = 30;
        $j = 12;

        if (mb_strlen($uri) <= $i) {
            return $uri;
        }

        $host = rtrim(parse_url($uri, PHP_URL_HOST), '/');
        $path = '/'.ltrim(parse_url($uri, PHP_URL_PATH), '/');

        if ($host === getenv('HTTP_HOST')) {
            $i = 50;
            $j = 22;

            $host = '';
        } else {
            if (mb_strlen($host) > $i && mb_strlen($path) > $i) {
                return $this->abridgeStr($uri, 50, 22);
            }

            $host = $this->abridgeStr($host, 20, 7);
        }

        $path = $this->abridgeStr($path, $i, $j);

        return $host.$path;
    }

    /**
     * Retrieve an abridged variant to the given URI.
     *
     * @param string  $str The string to possibly truncate.
     * @param integer $l   Optional. The soft-limit of the string.
     * @param integer $a   Optional. The hard-limit to keep from the beginning of the string.
     * @param integer $z   Optional. The hard-limit to keep from the end of the string.
     * @return string
     */
    private function abridgeStr($str, $l = 30, $a = 12, $z = 12)
    {
        if (mb_strlen($str) > $l) {
            $str = (($a > 0) ? mb_substr($str, 0, $a) : '').'&hellip;'.(($z > 0) ? mb_substr($str, -$z) : '');
        }

        return $str;
    }
}
