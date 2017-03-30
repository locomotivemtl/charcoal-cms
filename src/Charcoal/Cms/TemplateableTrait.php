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
     * @return self
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
     * @return self
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
     * @return self
     */
    public function setTemplateOptions($options)
    {
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
}
