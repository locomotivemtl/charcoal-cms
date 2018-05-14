<?php

namespace Charcoal\Admin\Widget\FormGroup;

// From 'charcoal-ui'
use Charcoal\Ui\FormGroup\FormGroupInterface;
use Charcoal\Ui\FormGroup\FormGroupTrait;
use Charcoal\Ui\UiItemInterface;
use Charcoal\Ui\UiItemTrait;

// From 'charcoal-cms'
use Charcoal\Admin\Widget\RelationWidget;

/**
 * Relation Widget, as form group.
 */
class RelationFormGroup extends RelationWidget implements
    FormGroupInterface,
    UiItemInterface
{
    use FormGroupTrait;
    use UiItemTrait;
}
