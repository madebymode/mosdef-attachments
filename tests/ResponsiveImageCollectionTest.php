<?php
namespace Mosdef\Attachments\Tests\App\Support;

use Mosdef\Attachments\ResponsiveImageCollection;

class ResponsiveImageCollectionTest extends \Tests\TestCase
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
            static::$collection = new ResponsiveImageCollection();
        }
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
}