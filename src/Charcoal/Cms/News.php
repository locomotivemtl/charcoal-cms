<?php

namespace Charcoal\Cms;

use \InvalidArgumentException;

use \Psr\Http\Message\RequestInterface;
use \Psr\Http\Message\ResponseInterface;

use \Charcoal\Translation\TranslationString;
use \Charcoal\Object\Content;
use \Charcoal\Object\CategorizableInterface;
use \Charcoal\Object\CategorizableTrait;
use \Charcoal\Object\PublishableInterface;
use \Charcoal\Object\PublishableTrait;
use \Charcoal\App\Routable\RoutableInterface;
use \Charcoal\App\Routable\RoutableTrait;

use \Charcoal\Cms\MetatagInterface;
use \Charcoal\Cms\SearchableInterface;

/**
*
*/
class News extends Content implements
    CategorizableInterface,
    MetatagInterface,
    PublishableInterface,
    RoutableInterface,
    SearchableInterface
{
    use CategorizableTrait;
    use PublishableTrait;
    use MetatagTrait;
    use RoutableTrait;
    use SearchableTrait;

    /**
    * @var TranslationString $title
    */
    private $title;

    /**
    * @var TranslationString $title
    */
    private $subtitle;

    /**
    * @var TranslationString $content
    */
    private $content;

    /**
    * @var Collection $blocks
    */
    private $blocks;

    /**
    * @var DateTime $start_date
    */
    private $start_date;
    /**
    * @var DateTime $start_date
    */
    private $end_date;

    /**
    * @var TranslationString $thumbnail
    */
    private $thumbnail;
    /**
    * @var TranslationString $image
    */
    private $image;

    /**
    * @var Collection $documents
    */
    private $documents;


    /**
    * CategorizableTrait > category_type()
    *
    * @return string
    */
    public function category_type()
    {
        return 'charcoal/cms/news-category';
    }

    /**
    * @param mixed $title
    * @return TranslationString
    */
    public function set_title($title)
    {
        $this->title = new TranslationString($title);
        return $this;
    }

    /**
    * @return TranslationString
    */
    public function title()
    {
        return $this->title;
    }

    /**
    * @param mixed $subbtitle
    * @return Event Chainable
    */
    public function set_subtitle($subtitle)
    {
        $this->subtitle = new TranslationString($subtitle);
        return $this;
    }

    /**
    * @return TranslationString
    */
    public function subtitle()
    {
        return $this->subtitle;
    }

    /**
    * @param mixed $content
    * @return Event Chainable
    */
    public function set_content($content)
    {
        $this->content = new TranslationString($content);
        return $this;
    }

    /**
    * @return TranslationString
    */
    public function content()
    {
        return $this->content;
    }

    /**
    * MetatagTrait > canonical_url
    *
    * @return string
    * @todo
    */
    public function canonical_url()
    {
        return '';
    }

    /**
    * RoutableInterface > handle_route()
    *
    * @param string $path
    * @param RequestInterface $request
    * @param ResponseInterface $response
    * @throws InvalidArgumentException
    * @return callable|null Route dispatcher
    */
    public function route_handler($path, RequestInterface $request, ResponseInterface $response)
    {
        if (!is_string($path)) {
            throw new InvalidArgumentExeption(
                'Route path must be a string'
            );
        }
        $match_path = $path == 'xxx'; // Insert logic here...
        if($match_path) {
            return function(RequestInterface $request, ResponseInterface $response) use ($path) {
                $response->write($path);
            };
        }
        else {
            return null;
        }
    }

}
