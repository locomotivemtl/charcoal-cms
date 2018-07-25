<?php

namespace Charcoal\Admin\Widget\FormGroup;

use Charcoal\Admin\AdminWidget;
use Charcoal\Admin\Widget\FormGroupWidget;
use Charcoal\Admin\Widget\MultiGroupWidget;
use Charcoal\Ui\FormGroup\FormGroupInterface;
use Charcoal\Ui\FormGroup\FormGroupTrait;
use Charcoal\Ui\UiItemInterface;
use Charcoal\Ui\UiItemTrait;

/**
 * Class NestedFormGroup
 */
class MultiGroupFormGroup extends MultiGroupWidget implements
    FormGroupInterface,
    UiItemInterface
{
    use FormGroupTrait;
    use UiItemTrait;

    /**
     * @return string
     */
    public function template()
    {
        return 'charcoal/admin/widget/multi-group';
    }
}
