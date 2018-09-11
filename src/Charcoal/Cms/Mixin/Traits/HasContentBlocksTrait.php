<?php

namespace Charcoal\Cms\Mixin\Traits;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;

/**
 * An implementation, as Trait, of the `HasContentBlocksInterface`.
 *
 * @uses Charcoal\Attachment\Traits\AttachmentAwareTrait
 */
trait HasContentBlocksTrait
{
    /**
     * Retrieve this object's content blocks.
     *
     * @return Collection|Attachment[]
     */
    public function contentBlocks()
    {
        return $this->getAttachments('contents');
    }

    /**
     * Determine if this object has any content blocks.
     *
     * @return boolean
     */
    public function hasContentBlocks()
    {
        return !!($this->numContentBlocks());
    }

    /**
     * Count the number of content blocks associated to this object.
     *
     * @return integer
     */
    public function numContentBlocks()
    {
        return count($this->contentBlocks());
    }

    // ==========================================================================
    // META
    // ==========================================================================

    /**
     * @return Translation
     */
    public function defaultMetaDescription()
    {
        $content = $this->metaDescFromAttachments();

        if ($content === null) {
            $content = parent::defaultMetaDescription();
        }

        return $content;
    }

    /**
     * Gets the content excerpt from attachments if a text attachment is found, otherwise
     * it return null.
     *
     * @return Translation|null|string|\string[] The content from attachment
     */
    private function metaDescFromAttachments()
    {
        $attachments = $this->getAttachments();

        if (!$attachments) {
            return null;
        }

        foreach ($attachments as $attachment) {
            if ($attachment->isText()) {
                $content = $attachment->description();

                $content = $this->ellipsis($content);

                return $content;
            }
        }

        return null;
    }

    /**
     * @param string|Translation $content The content to shorten.
     * @param integer            $length  The length to shorten to. Default : 200.
     * @return string|Translation
     */
    private function ellipsis($content, $length = 200)
    {
        if ($content instanceof Translation) {
            $content = $content->data();
            foreach ($content as $lang => $text) {
                $content[$lang] = strlen($text) > $length ? substr(strip_tags($text), 0, $length).'...' : $text;
            }
            $content = $this->translator()->translation($content);
        } else {
            $content = strlen($content) > $length ? substr(strip_tags($content), 0, $length) : $content;
        }

        return $content;
    }

    /**
     * Retrieve the objects associated to the current object.
     *
     * As defined in Charcoal\Attachment\Traits\AttachmentAwareTrait.
     *
     * @param  string|null   $group  Filter the attachments by a group identifier.
     * @param  string|null   $type   Filter the attachments by type.
     * @param  callable|null $before Process each attachment before applying data.
     * @param  callable|null $after  Process each attachment after applying data.
     * @throws InvalidArgumentException If the $group or $type is invalid.
     * @return Collection|Attachment[]
     */
    abstract public function getAttachments(
        $group = null,
        $type = null,
        callable $before = null,
        callable $after = null
    );
}
