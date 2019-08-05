<?php

namespace Charcoal\Property;

use PDO;

use RuntimeException;
use InvalidArgumentException;

// From Pimple
use Pimple\Container;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;

// From 'charcoal-property'
use Charcoal\Property\AbstractProperty;
use Charcoal\Property\SelectablePropertyInterface;
use Charcoal\Property\SelectablePropertyTrait;

/**
 * Template Selector Property
 */
class TemplateProperty extends AbstractProperty implements SelectablePropertyInterface
{
    use SelectablePropertyTrait;

    /**
     * The available selectable templates.
     *
     * @var array|null
     */
    private $availableTemplates;

    /**
     * @return string
     */
    public function type()
    {
        return 'template';
    }

    /**
     * Add a choice to the available choices.
     *
     * @param  string       $choiceIdent The choice identifier (will be key / default ident).
     * @param  string|array $choice      A string representing the choice label or a structure.
     * @return self
     */
    public function addChoice($choiceIdent, $choice)
    {
        $choice = $this->parseTemplateChoice($choice, strval($choiceIdent));

        $choiceIdent = $choice['value'];

        $this->choices[$choiceIdent] = $choice;

        return $this;
    }

    /**
     * Parse the given value into a choice structure from the available templates (if any).
     *
     * @param  string|array $choice      A string representing the choice label or a structure.
     * @param  string       $choiceIdent The choice identifier.
     * @throws InvalidArgumentException If the choice identifier is not a string.
     * @return array Returns a choice structure.
     */
    protected function parseTemplateChoice($choice, $choiceIdent)
    {
        if (!is_string($choiceIdent)) {
            throw new InvalidArgumentException(
                'Choice identifier must be a string.'
            );
        }

        if (is_string($choice)) {
            if (isset($this->availableTemplates[$choice])) {
                return $this->availableTemplates[$choice];
            } else {
                throw new InvalidArgumentException(sprintf(
                    'Template choice "%s" is not available.',
                    $choice
                ));
            }
        } elseif (is_bool($choice) && $choice === true) {
            if (isset($this->availableTemplates[$choiceIdent])) {
                return $this->availableTemplates[$choiceIdent];
            } else {
                throw new InvalidArgumentException(sprintf(
                    'Template choice "%s" is not available',
                    $choiceIdent
                ));
            }
        } elseif (is_array($choice)) {
            if (isset($this->availableTemplates[$choiceIdent])) {
                if (isset($choice['label'])) {
                    $choice['label'] = $this->translator()->translation($choice['label']);
                }

                $struct    = $this->availableTemplates[$choiceIdent];
                $immutable = [ 'value' => $struct['value'] ];

                return array_replace($struct, $choice, $immutable);
            } elseif (isset($choice['template']) || isset($choice['controller']) || isset($choice['class'])) {
                return $this->parseChoice($choice, $choiceIdent);
            } else {
                throw new InvalidArgumentException(sprintf(
                    'Custom template choice "%s" is requires a "template", "controller", or "class"',
                    $choiceIdent
                ));
            }
        } else {
            throw new InvalidArgumentException(
                'Template choice must be a template identifier or a choice structure'
            );
        }

        return $choice;
    }

    /**
     * Format the given value for display.
     *
     * @param  mixed $val     The value to to convert for display.
     * @param  array $options Optional display options.
     * @return string
     */
    public function displayVal($val, array $options = [])
    {
        if ($val === null || $val === '') {
            return '';
        }

        /** Parse multilingual values */
        if ($this['l10n']) {
            $propertyValue = $this->l10nVal($val, $options);
            if ($propertyValue === null) {
                return '';
            }
        } elseif ($val instanceof Translation) {
            $propertyValue = (string)$val;
        } else {
            $propertyValue = $val;
        }

        $separator = $this->multipleSeparator();

        /** Parse multiple values / ensure they are of array type. */
        if ($this['multiple']) {
            if (!is_array($propertyValue)) {
                $propertyValue = explode($separator, $propertyValue);
            }
        }

        if ($separator === ',') {
            $separator = ', ';
        }

        if (is_array($propertyValue)) {
            foreach ($propertyValue as &$value) {
                if (is_string($value)) {
                    $value = $this->choiceLabel($value);
                    if (!is_string($value)) {
                        $value = $this->l10nVal($value, $options);
                    }
                }
            }
            $propertyValue = implode($separator, $propertyValue);
        } elseif (is_string($propertyValue)) {
            $propertyValue = $this->choiceLabel($propertyValue);
            if (!is_string($propertyValue)) {
                $propertyValue = $this->l10nVal($propertyValue, $options);
            }
        }

        return $propertyValue;
    }

    /**
     * Retrieve the selected template's FQCN.
     *
     * @return string
     */
    public function __toString()
    {
        $val = $this->val();
        if ($this->hasChoice($val)) {
            $choice = $this->choice($val);
            $keys   = [ 'controller', 'template', 'class' ];
            foreach ($keys as $key) {
                if (isset($choice[$key])) {
                    return $choice[$key];
                }
            }
        }

        return '';
    }

    /**
     * @return string
     */
    public function sqlExtra()
    {
        return '';
    }

    /**
     * Get the SQL type (Storage format)
     *
     * @return string The SQL type
     */
    public function sqlType()
    {
        if ($this['multiple']) {
            return 'TEXT';
        }

        return 'VARCHAR(255)';
    }

    /**
     * @return integer
     */
    public function sqlPdoType()
    {
        return PDO::PARAM_STR;
    }

    /**
     * Inject dependencies from a DI Container.
     *
     * @param  Container $container A dependencies container instance.
     * @return void
     */
    protected function setDependencies(Container $container)
    {
        parent::setDependencies($container);

        if (isset($container['config']['templates'])) {
            $this->availableTemplates = $this->parseChoices($container['config']['templates']);
            $this->choices = $this->availableTemplates;
        }
    }
}
