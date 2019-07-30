<?php

namespace Charcoal\Cms;

// From 'charcoal-core'
use Charcoal\Validator\ValidatorInterface;

// From 'charcoal-object'
use Charcoal\Object\Content;
use Charcoal\Object\CategoryInterface;
use Charcoal\Object\CategoryTrait;

/**
 * News Category
 */
class NewsCategory extends Content implements CategoryInterface
{
    use CategoryTrait;

    /**
     * Translatable
     * @var string[] $name
     */
    protected $name;

    /**
     * Section constructor.
     * @param array $data Init data.
     */
    public function __construct(array $data = null)
    {
        parent::__construct($data);

        if (is_callable([ $this, 'defaultData' ])) {
            $this->setData($this->defaultData());
        }
    }

    /**
     * CategoryTrait > itemType()
     *
     * @return string
     */
    public function itemType()
    {
        return News::class;
    }

    /**
     * @return \Charcoal\Model\Collection|array
     */
    public function loadCategoryItems()
    {
        return [];
    }

    /**
     * @return mixed
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @param mixed $name The category name.
     * @return self
     */
    public function setName($name)
    {
        $this->name = $this->translator()->translation($name);

        return $this;
    }

    /**
     * @param ValidatorInterface $v Optional. A custom validator object to use for validation. If null, use object's.
     * @return boolean
     */
    public function validate(ValidatorInterface &$v = null)
    {
        parent::validate($v);

        foreach ($this->translator()->locales() as $locale => $value) {
            if (!(string)$this['name'][$locale]) {
                $this->validator()->error(
                    (string)$this->translator()->translation([
                        'fr' => 'Le NOM doit être rempli dans toutes les langues.',
                        'en' => 'The NAME must be filled in all languages.',
                    ])
                );

                return false;
            }
        }

        return true;
    }
}
