<?php

namespace Mosdef\Attachments\Tests\App\Support;

use Mosdef\Attachments\ResponsiveAttachment;
use Mosdef\Attachments\ResponsiveImageConfiguration;
use Mosdef\Attachments\ResponsiveImageCollection;
use Illuminate\Http\UploadedFile;
use Request;
use SplFileInfo;

class ResponsiveAttachmentTest extends \Tests\TestCase
{
    protected static $attachment;

    protected static $responsiveConfiguration;

    public function setUp()
    {
        parent::setUp();

        if (empty(static::$attachment)) {
            $fileFactory = UploadedFile::fake();
            $imgFile = $fileFactory->image('test-img.jpg');

            $request = Request::instance();
            $request->files->add(['image' => $imgFile]);

            static::$attachment = ResponsiveAttachment::createFromRequest('image');
            static::$responsiveConfiguration = new ResponsiveImageConfiguration();
            static::$responsiveConfiguration->setSizes([1104, 1000, 800, 600, 400, 27]);
        }
    }

    public function testSetGetImageTypes()
    {
        static::$attachment->setImageConfiguration(static::$responsiveConfiguration);
        $imageConfig = static::$attachment->getImageConfiguration();
        $this->assertEquals(static::$responsiveConfiguration->getSizes(), $imageConfig->getSizes());
    }

    public function testCreateResponsiveSizes()
    {
        $images = static::$attachment->createResponsiveSizes();

        foreach(static::$responsiveConfiguration->getSizes() as $index => $size) {
            $this->assertTrue(stripos($images[$index], '@' . $size) !== false);
            $this->assertTrue(file_exists(base_path($images[$index])));
        }
    }

    public function testGetImageCollection()
    {
        $collection = static::$attachment->getImageCollection();
        $this->assertInstanceOf(ResponsiveImageCollection::class, $collection);

        $this->assertTrue(!empty($collection->getSrc()));
        $this->assertTrue(!empty($collection->getSizes()));
        $this->assertTrue(is_array($collection->getSizes()));
    }

    public function testUnlinkAll()
    {
        // manually get a list of file paths to be able to validate they're all removed
        $pattern = base_path(trim(static::$attachment->file_path, '/') . '/' . pathinfo(static::$attachment->file_name, PATHINFO_FILENAME) . '*');
        $paths = glob($pattern);

        static::$attachment->unlinkAll();

        $this->assertTrue(count($paths) > 0);
        foreach($paths as $path) {
            $this->assertFalse(file_exists($path));
        }
    }
}