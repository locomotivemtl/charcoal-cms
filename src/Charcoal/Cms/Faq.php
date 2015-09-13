<?php

namespace Charcoal\Cms;

// From `charcoal-base`
use \Charcoal\Object\Content;

// From `charcoal-core`
use \Charcoal\Translation\TranslationString;

/**
* FAQ Entry
*/
class Faq extends Content
{
	/**
	* The question, or "title", of this entrt
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
}