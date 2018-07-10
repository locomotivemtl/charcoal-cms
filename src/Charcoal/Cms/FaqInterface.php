<?php

namespace Charcoal\Cms;

// From 'charcoal-object'
use Charcoal\Object\ContentInterface;
use Charcoal\Object\CategorizableInterface;
use Charcoal\Object\PublishableInterface;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;

/**
 *
 */
interface FaqInterface extends
    CategorizableInterface,
    ContentInterface,
    PublishableInterface,
    SearchableInterface
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
