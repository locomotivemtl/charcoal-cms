<?php

namespace Charcoal\Cms;

// From 'charcoal-object'
use Charcoal\Object\ContentInterface;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;

/**
 *
 */
interface FaqInterface extends ContentInterface
{
    /**
     * @param  mixed $question The question (localized).
     * @return self
     */
    public function setQuestion($question);

    /**
     * @return Translation|string|null
     */
    public function question();

    /**
     * @param  mixed $answer The answer (localized).
     * @return self
     */
    public function setAnswer($answer);

    /**
     * @return Translation|string|null
     */
    public function answer();
}
