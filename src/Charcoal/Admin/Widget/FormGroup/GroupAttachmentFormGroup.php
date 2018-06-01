<?php

namespace Charcoal\Admin\Widget\FormGroup;

// From 'charcoal-ui'
use Charcoal\Ui\FormGroup\FormGroupInterface;
use Charcoal\Ui\FormGroup\FormGroupTrait;
use Charcoal\Ui\UiItemInterface;
use Charcoal\Ui\UiItemTrait;

// From 'charcoal-cms'
use Charcoal\Admin\Widget\GroupAttachmentWidget;

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
     * @return boolean
     */
    public function active()
    {
        if (!$this->hasGroups()) {
            return false;
        }

        return parent::active();
    }
}
