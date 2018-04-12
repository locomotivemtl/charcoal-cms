<?php

namespace Charcoal\Admin\Widget\FormGroup;

// From 'charcoal-cms'
use Charcoal\Admin\Widget\GroupAttachmentWidget;

// From 'charcoal-ui'
use Charcoal\App\Template\WidgetInterface;
use Charcoal\Ui\FormGroup\FormGroupInterface;
use Charcoal\Ui\FormGroup\FormGroupTrait;
use Charcoal\Ui\Layout\LayoutAwareInterface;
use Charcoal\Ui\Layout\LayoutAwareTrait;
use Charcoal\Ui\UiItemInterface;
use Charcoal\Ui\UiItemTrait;

/**
 * Class TemplateAttachmentFormGroup
 */
class GroupAttachmentFormGroup extends GroupAttachmentWidget implements
    FormGroupInterface,
    UiItemInterface
{
    use FormGroupTrait;
    use UiItemTrait;

    /**
     * @inheritDoc
     */
    public function active()
    {
        if (!$this->hasGroups()) {
            return false;
        }

        return parent::active();
    }
}
