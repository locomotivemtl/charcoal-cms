<?php

namespace Charcoal\Cms;

use RuntimeException;

// From 'charcoal-core'
use Charcoal\Model\Model;
use Charcoal\Model\ModelInterface;
use Charcoal\Source\StorableTrait;

// From 'charcoal-property'
use Charcoal\Property\PropertyInterface;
use Charcoal\Property\SelectablePropertyInterface;
use Charcoal\Property\Structure\StructureMetadata;
use Charcoal\Property\Structure\StructureModel;
use Charcoal\Property\ModelStructureProperty;
use Charcoal\Property\TemplateOptionsProperty;
use Charcoal\Property\TemplateProperty;

// From 'charcoal-cms'
use Charcoal\Cms\TemplateableInterface;

/**
 * Default implementation, as Trait, of the {@see TemplateableInterface}.
 *
 * Note: Call {@see self::saveTemplateOptions()} in {@see StorableTrait::preSave()}
 * and {@see StorableTrait::preUpdate()} when using {@see TemplateOptionsProperty}.
 */
trait TemplateableTrait
{
    /**
     * The object's template identifier.
     *
     * @var mixed
     */
    protected $templateIdent;

    /**
     * The object's template controller identifier.
     *
     * @var mixed
     */
    protected $controllerIdent;

    /**
     * The template options values.
     *
     * @var array
     */
    protected $templateOptions = [];

    /**
     * The template options structure.
     *
     * @var StructureMetadata
     */
    protected $templateOptionsMetadata;

    /**
     * Track the state of the template options structure.
     *
     * @var boolean
     */
    protected $areTemplateOptionsFinalized = false;

    /**
     * Set the renderable object's template identifier.
     *
     * @param  mixed $template The template ID.
     * @return self
     */
    public function setTemplateIdent($template)
    {
        $this->areTemplateOptionsFinalized = false;

        $this->templateIdent = $template;

        return $this;
    }

    /**
     * Retrieve the renderable object's template identifier.
     *
     * @return mixed
     */
    public function templateIdent()
    {
        return $this->templateIdent;
    }

    /**
     * Retrieve the template identifier as class path.
     *
     * @return mixed
     */
    public function templateIdentClass()
    {
        $templateIdent = $this->templateIdent();
        $property      = $this->property('template_ident');

        $key = $property->ident();
        if ($property instanceof SelectablePropertyInterface) {
            if ($property->hasChoice($templateIdent)) {
                $choice = $property->choice($templateIdent);
                $keys   = [ 'controller', 'template', 'class' ];
                foreach ($keys as $key) {
                    if (isset($choice[$key])) {
                        return $choice[$key];
                    }
                }
            }
        } else {
            return $templateIdent;
        }
    }

    /**
     * Set the renderable object's template controller identifier.
     *
     * @param  mixed $ident The template controller identifier.
     * @return self
     */
    public function setControllerIdent($ident)
    {
        $this->areTemplateOptionsFinalized = false;

        $this->controllerIdent = $ident;

        return $this;
    }

    /**
     * Retrieve the renderable object's template controller identifier.
     *
     * @return mixed
     */
    public function controllerIdent()
    {
        return $this->controllerIdent;
    }

    /**
     * Customize the template's options.
     *
     * @param  mixed $options Template options.
     * @return self
     */
    public function setTemplateOptions($options)
    {
        $this->areTemplateOptionsFinalized = false;

        if (is_numeric($options)) {
            $options = null;
        } elseif (is_string($options)) {
            $options = json_decode($options, true);
        }

        $this->templateOptions = $options;

        return $this;
    }

    /**
     * Retrieve the template's customized options.
     *
     * @return mixed
     */
    public function templateOptions()
    {
        return $this->templateOptions;
    }

    /**
     * Validate the model has template options properties.
     *
     * @return boolean
     */
    public function hasTemplateOptions()
    {
        if ($this->templateOptionsStructure()) {
            return (bool)count(
                iterator_to_array(
                    $this->templateOptionsStructure()->properties()
                )
            );
        }

        return false;
    }

    /**
     * Retrieve the object's template options metadata.
     *
     * @return StructureMetadata|null
     */
    public function templateOptionsMetadata()
    {
        if ($this->areTemplateOptionsFinalized === false) {
            $this->areTemplateOptionsFinalized = true;
            $this->prepareTemplateOptions();
        }

        return $this->templateOptionsMetadata;
    }

    /**
     * Retrieve the object's template options as a structured model.
     *
     * @return ModelInterface|ModelInterface[]|null
     */
    public function templateOptionsStructure()
    {
        if ($this->areTemplateOptionsFinalized === false) {
            $this->areTemplateOptionsFinalized = true;
            $this->prepareTemplateOptions();
        }

        $key  = 'template_options';
        $prop = $this->property($key);
        $val  = $this->propertyValue($key);
        $obj  = $prop->structureVal($val, $this->templateOptionsMetadata());

        return $obj;
    }

    /**
     * Asserts that the templateable class meets the requirements,
     * throws an Exception if not.
     *
     * Requirements:
     * 1. The class implements the {@see TemplateableInterface} and
     * 2. The model's "template_options" property uses the {@see TemplateOptionsProperty}.
     *
     *
     * @throws RuntimeException If the model does not implement its requirements.
     * @return void
     */
    final protected function assertValidTemplateStructureDependencies()
    {
        if (!$this instanceof TemplateableInterface) {
            throw new RuntimeException(sprintf(
                'Class [%s] must implement [%s]',
                get_class($this),
                TemplateableInterface::class
            ));
        }

        $prop = $this->property('template_options');
        if (!$prop instanceof TemplateOptionsProperty) {
            throw new RuntimeException(sprintf(
                'Property "%s" must use [%s]',
                $prop->ident(),
                TemplateOptionsProperty::class
            ));
        }
    }

    /**
     * Convert the given model's multilingual property values into {@see \Charcoal\Translator\Translation} objects.
     *
     * @param  ModelInterface $obj       The object to parse.
     * @param  boolean        $recursive Whether we should traverse structure properties.
     * @return ModelInterface The localized object.
     */
    protected function translateTemplateOptionsModel(ModelInterface $obj, $recursive = false)
    {
        unset($recursive);
        foreach ($obj->properties() as $propertyIdent => $property) {
            $val = $obj[$propertyIdent];
            if ($property['l10n']) {
                $obj[$propertyIdent] = $this->translator()->translation($obj[$propertyIdent]);
            } elseif ($property instanceof ModelStructureProperty) {
                $struct = $property->structureVal($obj[$propertyIdent]);

                /** Provide support for dynamically wrapping translation sets.  */
                if (in_array(get_class($struct), [ Model::class, StructureModel::class ])) {
                    $struct = $this->translateTemplateOptionsModel($struct);
                }

                $obj[$propertyIdent] = $struct;
            }
        }

        return $obj;
    }

    /**
     * Retrieve the default template propert(y|ies).
     *
     * @return string[]
     */
    protected function defaultTemplateProperties()
    {
        return [
            'template_ident'
        ];
    }

    /**
     * Retrieve the template's structure interface(s).
     *
     * @see    TemplateProperty::__toString()
     * @see    \Charcoal\Admin\Widget\FormGroup\TemplateOptionsFormGroup::finalizeStructure()
     * @todo   Migrate `MissingDependencyException` from 'mcaskill/charcoal-support' to 'mcaskill/charcoal-core'.
     * @param  PropertyInterface|string ...$properties The properties to lookup.
     * @return string[]|null
     */
    protected function extractTemplateInterfacesFrom(...$properties)
    {
        $interfaces = [];
        foreach ($properties as $property) {
            if (!$property instanceof PropertyInterface) {
                $property = $this->property($property);
            }

            $key = $property->ident();
            $val = $this->propertyValue($key);
            if ($property instanceof SelectablePropertyInterface) {
                if ($property->hasChoice($val)) {
                    $choice = $property->choice($val);
                    $keys   = [ 'controller', 'template', 'class' ];
                    foreach ($keys as $key) {
                        if (isset($choice[$key])) {
                            $interface = $choice[$key];

                            if ($key === 'template' || $key === 'controller') {
                                if (substr($interface, -9) !== '-template') {
                                    $interface .= '-template';
                                }
                            }

                            $interfaces[] = $interface;
                            break;
                        }
                    }
                }
            } else {
                $interfaces[] = $val;
            }
        }

        return $interfaces;
    }

    /**
     * Prepare the template options (structure) for use.
     *
     * @uses   self::assertValidTemplateStructureDependencies() Validates that the model meets requirements.
     * @param  (PropertyInterface|string)[]|null $templateIdentProperties The template key properties to parse.
     * @return boolean
     */
    protected function prepareTemplateOptions(array $templateIdentProperties = null)
    {
        $this->assertValidTemplateStructureDependencies();

        if ($templateIdentProperties === null) {
            $templateIdentProperties = $this->defaultTemplateProperties();
        }

        $templateInterfaces = $this->extractTemplateInterfacesFrom(...$templateIdentProperties);

        if (empty($templateInterfaces)) {
            return false;
        }

        $metadataLoader = $this->metadataLoader();

        $templateStructKey = $templateInterfaces;
        array_unshift($templateStructKey, $this->objType(), $this->id());
        $templateStructKey = 'template/structure='.$metadataLoader->serializeMetaKey($templateStructKey);

        $structureMetadata = $metadataLoader->load(
            $templateStructKey,
            StructureMetadata::class,
            $templateInterfaces
        );

        $this->templateOptionsMetadata = $structureMetadata;

        return true;
    }

    /**
     * Save the template options structure.
     *
     * @param  (PropertyInterface|string)[]|null $properties The template properties to parse.
     * @return void
     */
    protected function saveTemplateOptions(array $properties = null)
    {
        if ($properties === null) {
            $properties = $this->defaultTemplateProperties();
        }

        if ($this->areTemplateOptionsFinalized === false) {
            $this->areTemplateOptionsFinalized = true;
            $this->prepareTemplateOptions();
        }

        $key  = 'template_options';
        $prop = $this->property($key);
        $prop->setStructureMetadata($this->templateOptionsMetadata());
    }
}
