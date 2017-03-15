<?php

namespace Charcoal\Cms\Mixin\Traits;

// dependencies from `beneroch/charcoal-attachment`
use Charcoal\Attachment\Traits\AttachmentAwareTrait;

// dependencies from `charcoal-translation`
use Charcoal\Translator\Translation;

/**
 * An implementation, as Trait, of the `HasContentBlocksInterface`.
 *
 * @uses Charcoal\Attachment\Traits\AttachmentAwareTrait
 */
trait HasContentBlocksTrait
{
    use AttachmentAwareTrait;

    /**
     * Retrieve this object's content blocks.
     *
     * @return Collection|Attachment[]
     */
    public function contentBlocks()
    {
        return $this->attachments('contents');
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
        $attachments = $this->attachments();

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
    public function ellipsis($content, $length = 200)
    {
        if ($content instanceof Translation) {
            $content = $content->data();
            foreach ($content as $lang => $text) {
                $content[$lang] = strlen($text) > $length ? substr(strip_tags($text), 0, $length).'...' : $text;
            }
            $content = $this->translator()->translation($content);
        } else {
            $content = strlen($content) > $length ? substr(strip_tags($content), 0, $length): $content;
        }

        return $content;
    }
}
