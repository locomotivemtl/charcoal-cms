<?php

namespace Charcoal\Admin\Widget;

use Charcoal\Admin\Ui\ObjectContainerInterface;
use Charcoal\Admin\Ui\ObjectContainerTrait;
use Charcoal\Factory\FactoryInterface;
use RuntimeException;

use Charcoal\Admin\AdminWidget;

// From Pimple
use Pimple\Container;

// From 'charcoal-ui'
use Charcoal\Ui\Form\FormInterface;
use Charcoal\Ui\Form\FormTrait;
use Charcoal\Ui\Layout\LayoutAwareInterface;
use Charcoal\Ui\Layout\LayoutAwareTrait;
use Charcoal\Ui\PrioritizableInterface;

/**
 * Class TemplateAttachmentWidget
 */
class MultiGroupWidget extends AdminWidget implements
    FormInterface,
    LayoutAwareInterface,
    ObjectContainerInterface
{
    use FormTrait;
    use LayoutAwareTrait;
    use ObjectContainerTrait;

    /**
     * The cache of snake-cased words.
     *
     * @var array
     */
    protected static $snakeCache = [];

    /**
     * The cache of camel-cased words.
     *
     * @var array
     */
    protected static $camelCache = [];

    /**
     * @var string
     */
    protected $controllerIdent;

    /**
     * @var FactoryInterface
     */
    protected $widgetFactory;

    /**
     * @var boolean
     */
    protected $isMergingData;

    /**
     * @var array|mixed
     */
    protected $formGroups;

    /**
     * @var FormWidget
     */
    protected $form;

    /**
     * Comparison function used by {@see uasort()}.
     *
     * @param  PrioritizableInterface $a Sortable entity A.
     * @param  PrioritizableInterface $b Sortable entity B.
     * @return integer Sorting value: -1 or 1.
     */
    protected function sortItemsByPriority(
        PrioritizableInterface $a,
        PrioritizableInterface $b
    ) {
        $priorityA = $a->priority();
        $priorityB = $b->priority();

        if ($priorityA === $priorityB) {
            return 0;
        }

        return ($priorityA < $priorityB) ? (-1) : 1;
    }

    /**
     * @param  Container $container The DI container.
     * @return void
     */
    protected function setDependencies(Container $container)
    {
        parent::setDependencies($container);

        $this->setWidgetFactory($container['widget/factory']);

        // Satisfies FormInterface
        $this->setFormGroupFactory($container['form/group/factory']);
    }

    /**
     * @return string
     */
    public function defaultGroupType()
    {
        return 'charcoal/admin/widget/form-group/generic';
    }

    /**
     * Set the object's form groups.
     *
     * @param array $groups A collection of group structures.
     * @return FormInterface Chainable
     */
    public function setGroups(array $groups)
    {
        $this->groups       = [];
        $obj                = $this->obj();
        $objMetadata        = $obj->metadata();
        $adminMetadata      = (isset($objMetadata['admin']) ? $objMetadata['admin'] : null);

        if (isset($adminMetadata['form_groups'])) {
            $extraFormGroups = array_intersect(
                array_keys($groups),
                array_keys($adminMetadata['form_groups'])
            );
            foreach ($extraFormGroups as $groupIdent) {
                $groups[$groupIdent] = array_replace_recursive(
                    $adminMetadata['form_groups'][$groupIdent],
                    $groups[$groupIdent]
                );

                $this->addGroup($groupIdent, $groups[$groupIdent]);
            }
        }

        return $this;
    }

    /**
     * Set an widget factory.
     *
     * @param FactoryInterface $factory The factory to create widgets.
     * @return self
     */
    protected function setWidgetFactory(FactoryInterface $factory)
    {
        $this->widgetFactory = $factory;

        return $this;
    }

    /**
     * Retrieve the widget factory.
     *
     * @throws RuntimeException If the widget factory was not previously set.
     * @return FactoryInterface
     */
    public function widgetFactory()
    {
        if (!isset($this->widgetFactory)) {
            throw new RuntimeException(sprintf(
                'Widget Factory is not defined for "%s"',
                get_class($this)
            ));
        }

        return $this->widgetFactory;
    }

    /**
     * @return FormWidget
     */
    public function form()
    {
        return $this->form;
    }

    /**
     * @param FormInterface $form Form for MultiGroupWidget.
     * @return self
     */
    public function setForm(FormInterface $form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * @return mixed
     */
    public function formGroups()
    {
        return $this->formGroups;
    }

    /**
     * @param mixed $formGroups FormGroups for MultiGroupWidget.
     * @return self
     */
    public function setFormGroups($formGroups)
    {
        $this->formGroups = $formGroups;

        return $this;
    }

    /**
     * So that the formTrait can access the current From widget.
     *
     * @return FormWidget
     */
    protected function formWidget()
    {
        return $this->form();
    }
}
