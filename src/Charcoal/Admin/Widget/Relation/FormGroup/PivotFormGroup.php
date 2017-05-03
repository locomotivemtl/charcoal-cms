<?php

namespace Charcoal\Admin\Widget\Relation\FormGroup;

// From 'charcoal-ui'
use Charcoal\Ui\FormGroup\FormGroupInterface;
use Charcoal\Ui\FormGroup\FormGroupTrait;
use Charcoal\Ui\UiItemInterface;
use Charcoal\Ui\UiItemTrait;

// From 'charcoal-cms'
use Charcoal\Admin\Widget\Relation\PivotWidget;

/**
 * Pivot Widget, as form group.
 */
class PivotFormGroup extends PivotWidget implements
    FormGroupInterface,
    UiItemInterface
{
    use FormGroupTrait;
    use UiItemTrait;
}
