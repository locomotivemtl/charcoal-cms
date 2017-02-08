<?php

namespace Charcoal\Cms;

/**
 * Defines a renderable object associated to a template.
 */
interface TemplateableInterface
{
    /**
     * Set the renderable object's template identifier.
     *
     * @param  mixed $template The template ID.
     * @return self
     */
    public function setTemplateIdent($template);

    /**
     * Retrieve the renderable object's template identifier.
     *
     * @return mixed
     */
    public function templateIdent();

    /**
     * Set the renderable object's template controller identifier.
     *
     * @param  mixed $ident The template controller identifier.
     * @return self
     */
    public function setControllerIdent($ident);

    /**
     * Retrieve the renderable object's template controller identifier.
     *
     * @return mixed
     */
    public function controllerIdent();

    /**
     * Customize the template's options.
     *
     * @param  array|string $options Template options.
     * @return self
     */
    public function setTemplateOptions($options);

    /**
     * Retrieve the template's customized options.
     *
     * @return array
     */
    public function templateOptions();
}
