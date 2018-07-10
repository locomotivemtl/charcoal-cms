<?php

namespace Charcoal\Cms;

// From 'charcoal-object'
use Charcoal\Object\Content;
use Charcoal\Object\CategorizableTrait;
use Charcoal\Object\PublishableTrait;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;

/**
 * FAQ Entry.
 */
abstract class AbstractFaq extends Content implements FaqInterface
{
    use CategorizableTrait;
    use PublishableTrait;
    use SearchableTrait;

    /**
     * The question, or "title", of this entry.
     *
     * @var Translation|string|null
     */
    private $question;

    /**
     * @var Translation|string|null
     */
    private $answer;

    /**
     * @param  mixed $question The question (localized).
     * @return self
     */
    public function setQuestion($question)
    {
        $this->question = $this->translator()->translation($question);
        return $this;
    }

    /**
     * @return Translation|string|null
     */
    public function question()
    {
        return $this->question;
    }

    /**
     * @param  mixed $answer The answer (localized).
     * @return self
     */
    public function setAnswer($answer)
    {
        $this->answer = $this->translator()->translation($answer);
        return $this;
    }

    /**
     * @return Translation|string|null
     */
    public function answer()
    {
        return $this->answer;
    }
}
