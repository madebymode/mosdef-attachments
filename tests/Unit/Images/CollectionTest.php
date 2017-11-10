<?php
namespace Mosdef\Attachments\Tests\Images;

use Mosdef\Attachments\Images\Collection;

class CollectionTest extends \Tests\TestCase
{
    protected static $collection;

    protected $images = [
        100 => '/path/to/img@100.jpg',
        200 => '/path/to/img@200.jpg',
        300 => '/path/to/img@300.jpg',
    ];

    public function setUp()
    {
        parent::setUp();

        if (empty(static::$collection)) {
            static::$collection = new Collection();
        }
    }

    public function testImplementsArrayAccess()
    {
        $this->assertTrue(static::$collection instanceof \ArrayAccess);
    }

    public function testSetGetSrc()
    {
        $src = '/path/to/img.jpg';
        static::$collection->setSrc($src);

        $this->assertEquals($src, static::$collection->getSrc());
    }

    public function testSetGetSizes()
    {
        static::$collection->setSizes($this->images);
        $this->assertEquals($this->images, static::$collection->getSizes());
    }

    public function testGetSrcSetAttr()
    {
        $srcset = static::$collection->getSrcSetAttr();

        $attr = [];
        foreach($this->images as $width => $image) {
            $attr[] = $image . ' ' . $width . 'w';
        }

        $this->assertEquals(implode(', ', $attr), $srcset);
    }

    public function testOffsetExists()
    {
        $this->assertTrue(isset(static::$collection[100]));
        $this->assertFalse(isset(static::$collection['does-not-exist']));
    }

    public function testOffsetGet()
    {
        $this->assertEquals('/path/to/img@100.jpg', static::$collection[100]);
    }

    public function testOffsetSetThrowsLogicException()
    {
        $this->expectException(\LogicException::class);
        static::$collection['new-var'] = 'test';
    }

    public function testUnsetThrowsLogicException()
    {
        $this->expectException(\LogicException::class);
        unset(static::$collection['cannot-unset']);
    }
}