<?php

namespace Charcoal\Cms\Tests;

// From 'charcoal-cms'
use Charcoal\Cms\Faq;
use Charcoal\Cms\FaqCategory;
use Charcoal\Tests\AbstractTestCase;

/**
 *
 */
class FaqTest extends AbstractTestCase
{
    use \Charcoal\Tests\Cms\ContainerIntegrationTrait;

    /**
     * Tested Class.
     *
     * @var Faq
     */
    private $obj;

    /**
     * Set up the test.
     *
     * @return void
     */
    public function setUp()
    {
        $container = $this->getContainer();

        $this->obj = new Faq([
            'container' => $container,
            'logger'    => $container['logger']
        ]);
    }

    /**
     * @return void
     */
    public function testSetData()
    {
        $ret = $this->obj->setData([
            'question' => 'Foo?',
            'answer'   => 'Bar'
        ]);
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('Foo?', (string)$this->obj->question());
        $this->assertEquals('Bar', (string)$this->obj->answer());
    }

    /**
     * @return void
     */
    public function testSetQuestion()
    {
        $ret = $this->obj->setQuestion('Foo?');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('Foo?', $this->obj->question());
    }

    /**
     * @return void
     */
    public function testSetAnswer()
    {
        $ret = $this->obj->setAnswer('Bar');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('Bar', $this->obj->answer());
    }

    /**
     * @return void
     */
    public function testCategoryType()
    {
        $this->assertEquals(FaqCategory::class, $this->obj->categoryType());
    }
}
