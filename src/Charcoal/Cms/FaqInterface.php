<?php

namespace Charcoal\Cms;

/**
 *
 */
interface FaqInterface
{
    /**
     * @param mixed $question The question (localized).
     * @return Faq Chainable
     */
    public function setQuestion($question);

    /**
     * @return TranslationString|null
     */
    public function question();

    /**
     * @param mixed $answer The answer (localized).
     * @return Faq Chainable
     */
    public function setAnswer($answer);

    /**
     * @return TranslationString|null
     */
    public function answer();
}
