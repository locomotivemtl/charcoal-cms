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

// From 'charcoal-app'
use Charcoal\App\App;

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
     * Charcoal Application.
     *
     * @var App
     */
    private $app;

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

        $this->getContainerProvider()->registerTemplateFactory($container);

        $route = $container['model/factory']->get(ObjectRoute::class);
        if ($route->source()->tableExists() === false) {
            $route->source()->createTable();
        }

        $this->obj = new SectionRoute([
            'config' => [],
            'path'   => 'en/sections/foo'
        ]);

        $container['config']['templates'] = [
            [
                'value'    => 'generic',
                'label'    => [
                    'en' => 'Generic',
                    'fr' => 'Générique'
                ],
                'template' => 'tests/template/generic',
            ],
        ];

        /** @todo Hack: Ensure the instance is shared */
        $this->app = App::instance($container);
    }

    /**
     *
     */
    public function testPathResolvable()
    {
        $container = $this->getContainer();

        $locales = $container['locales/manager'];
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
        $obj = $container['model/factory']->create(Section::class);
        $obj->source()->createTable();

        $route  = $this->obj;
        $return = $route($container, $request, $response);
        $this->assertEquals(404, $return->getStatusCode());

        $obj->setData([
            'id'             => 1,
            'title'          => 'Foo',
            'template_ident' => ''
        ]);
        $obj->save();

        $return = $route($container, $request, $response);
        $this->assertEquals(404, $return->getStatusCode());

        /*$obj->setTemplateIdent('bar');
        $obj->update();

        $return = $route($container, $request, $response);
        $this->assertEquals(200, $return->getStatusCode());*/
    }
}
