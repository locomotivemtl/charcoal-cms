<?php

namespace Charcoal\Cms;

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
     * The object's template controller identifier.
     *
     * @var mixed
     */
    private $controllerIdent;

    /**
     * The customized template options.
     *
     * @var array
     */
    private $templateOptions = [];

    /**
     * Set the renderable object's template identifier.
     *
     * @param  mixed $template The template ID.
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
        return $this->templateIdent;
    }

    /**
     * Set the renderable object's template controller identifier.
     *
     * @param  mixed $ident The template controller identifier.
     * @return TemplateableInterface Chainable
     */
    public function setControllerIdent($ident)
    {
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
     * @return TemplateableInterface Chainable
     */
    public function setTemplateOptions($options)
    {
        if (is_string($options)) {
            $options = json_decode($options, true);
        }

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
        return $this->templateOptions;
    }
}