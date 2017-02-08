<?php

namespace Charcoal\Tests\Cms\Route;

// From PSR-7
use Psr\Http\Message\RequestInterface;

// From Slim
use Slim\Http\Response;

// From 'charcoal-object'
use Charcoal\Object\ObjectRoute;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;

// From 'charcoal-cms'
use Charcoal\Cms\Section;
use Charcoal\Cms\Route\SectionRoute;

/**
 *
 */
class SectionRouteTest extends \PHPUnit_Framework_TestCase
{
    use \Charcoal\Tests\Cms\ContainerIntegrationTrait;

    /**
     * Tested Class.
     *
     * @var SectionRoute
     */
    private $obj;

    /**
     * Set up the test.
     */
    public function setUp()
    {
        $container = $this->getContainer();

        $route = $container['model/factory']->get(ObjectRoute::class);
        if ($route->source()->tableExists() === false) {
            $route->source()->createTable();
        }

        $this->obj = new SectionRoute([
            'config' => [],
            'path'   => 'en/sections/foo'
        ]);
    }

    /**
     *
     */
    public function testPathResolvable()
    {
        $container = $this->getContainer();

        $locales   = $container['locales/manager'];
        $locales->setCurrentLocale($locales->currentLocale());

        // Create the section table
        $section = $container['model/factory']->create(Section::class);
        $section->source()->createTable();

        $ret = $this->obj->pathResolvable($container);
        $this->assertFalse($ret);

        // Now try with a resolvable section.
        $section->setData([
            'id'             => 1,
            'title'          => 'Foo',
            'slug'           => new Translation('foo', $locales),
            'template_ident' => 'bar'
        ]);
        $id = $section->save();

        $ret = $this->obj->pathResolvable($container);
        //$this->assertTrue($ret);
    }

    /**
     *
     */
    public function testInvoke()
    {
        $container = $this->getContainer();
        $request   = $this->getMock(RequestInterface::class);
        $response  = new Response();

        // Create the section table
        $section = $container['model/factory']->create(Section::class);
        $section->source()->createTable();

        $obj = $this->obj;
        $ret = $obj($container, $request, $response);
        $this->assertEquals(404, $ret->getStatusCode());
    }
}
