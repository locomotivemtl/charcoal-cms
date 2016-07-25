<?php

namespace Charcoal\Cms;

use \Charcoal\Translation\TranslationString;

/**
 * Default implementation, as Trait,
 * of the {@see \Charcoal\Cms\TemplateableInterface}.
 */
trait TemplateableTrait
{
    /**
     * The object's template identifier.
     *
     * @var mixed
     */
    private $templateIdent;

    /**
     * The customized template options.
     *
     * @var array
     */
    private $templateOptions = [];

    /**
     * Set the renderable object's template identifier.
     *
     * @param mixed $template The template ID.
     * @return TemplateableInterface Chainable
     */
    public function setTemplateIdent($template)
    {
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
        if (!$this->templateIdent) {
            $metadata = $this->metadata();

            if (isset($metadata['template'])) {
                if (is_string($metadata['template'])) {
                    return $metadata['template'];
                } elseif (isset($metadata['template']['ident'])) {
                    return $metadata['template']['ident'];
                }
            } elseif (isset($metadata['template_ident'])) {
                trigger_error(
                    sprintf(
                        'The "template_ident" key, used by %s, is deprecated. Use "template.ident" instead.',
                        get_called_class()
                    ),
                    E_USER_DEPRECATED
                );
                return $metadata['template_ident'];
            }
        }

        return $this->templateIdent;
    }

    /**
     * Customize the template's options.
     *
     * @param array|string $options Template options.
     * @return TemplateableInterface Chainable
     */
    public function setTemplateOptions($options)
    {
        $this->templateOptions = $options;

        return $this;
    }

    /**
     * Retrieve the template's customized options.
     *
     * @return array
     */
    public function templateOptions()
    {
        if (!$this->templateOptions) {
            $metadata = $this->metadata();
            $options  = (isset($metadata['template']['options']) ? $metadata['template']['options'] : []);

            if (isset($metadata['template_options'])) {
                trigger_error(
                    sprintf(
                        'The "template_options" key, used by %s, is deprecated. Use "template.options" instead.',
                        get_called_class()
                    ),
                    E_USER_DEPRECATED
                );
                $options = array_merge($options, $metadata['template_options']);
            }

            return $options;
        }

        return $this->templateOptions;
    }
}
