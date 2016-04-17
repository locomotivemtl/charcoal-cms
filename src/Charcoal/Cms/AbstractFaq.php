<?php

namespace Charcoal\Cms;

use \Charcoal\Translation\TranslationString;

use \Charcoal\Object\Content;
use \Charcoal\Object\CategorizableInterface;
use \Charcoal\Object\CategorizableTrait;
use \Charcoal\Object\PublishableInterface;
use \Charcoal\Object\PublishableTrait;

use \Charcoal\Cms\FaqInterface;
use \Charcoal\Cms\MetatagInterface;
use \Charcoal\Cms\SearchableInterface;
use \Charcoal\Cms\Searchabletrait;

/**
 * FAQ Entry.
 */
abstract class AbstractFaq extends Content implements
    CategorizableInterface,
    FaqInterface,
    PublishableInterface,
    SearchableInterface
{
    use CategorizableTrait;
    use PublishableTrait;
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
     * The content attachments
     * @var array $attachments
     */
    private $attachments;

    /**
     * @param mixed $question The question (localized).
     * @return FaqInterface Chainable
     */
    public function setQuestion($question)
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
     * @param mixed $answer The answer (localized).
     * @return FaqInterface Chainable
     */
    public function setAnswer($answer)
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
}
