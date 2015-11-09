<?php

namespace Charcoal\Cms;

// From `charcoal-core`
use \Charcoal\Translation\TranslationString;

// From `charcoal-base`
use \Charcoal\Object\Content;
use \Charcoal\Object\CategorizableInterface;
use \Charcoal\Object\CategorizableTrait;
use \Charcoal\Object\PublishableInterface;
use \Charcoal\Object\PublishableTrait;
use \Charcoal\Object\RoutableInterface;
use \Charcoal\Object\RoutableTrait;

// Local namespace dependencies
use \Charcoal\Cms\MetatagInterface;
use \Charcoal\Cms\SearchableInterface;

/**
* FAQ Entry
*/
class Faq extends Content implements
    CategorizableInterface,
    MetatagInterface,
    PublishableInterface,
    RoutableInterface,
    SearchableInterface
{
    use CategorizableTrait;
    use PublishableTrait;
    use MetatagTrait;
    use SearchableTrait;

    /**
    * The question, or "title", of this entry
    * @var TranslationString $question
    */
    private $question;

    /**
    * @var TranslationString $answer
    */
    private $answer;

    /**
    * @param mixed $question
    * @return Faq Chainable
    */
    public function set_question($question)
    {
        $this->question = new TranslationString($question);
        return $this;
    }

    /**
    * @return TranslationString|null
    */
    public function question()
    {
        return $this->question;
    }

    /**
    * @param mixed answer
    * @return Faq Chainable
    */
    public function set_answer($answer)
    {
        $this->answer = new TranslationString($answer);
        return $this;
    }

    /**
    * @return TranslationString|null
    */
    public function answer()
    {
        return $this->answer;
    }

    /**
    * CategorizableTrait > category_type()
    *
    * @return string
    */
    public function category_type()
    {
        return 'charcoal/cms/faq-category';
    }
}
