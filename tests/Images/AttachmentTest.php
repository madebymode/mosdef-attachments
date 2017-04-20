<?php
namespace Mosdef\Attachments\Tests\Images;

use Mosdef\Attachments\Images\Attachment;
use Mosdef\Attachments\Images\Configuration;
use Mosdef\Attachments\Images\Collection;
use Illuminate\Http\UploadedFile;
use Request;
use Artisan;

class AttachmentTest extends \Tests\TestCase
{
    protected static $attachment;

    protected static $responsiveConfiguration;

    public function setUp()
    {
        parent::setUp();

        Artisan::call('migrate');

        if (empty(static::$attachment)) {
            $fileFactory = UploadedFile::fake();
            $imgFile = $fileFactory->image('test-img.jpg');

            $request = Request::instance();
            $request->files->add(['image' => $imgFile]);

            static::$attachment = $this->getMockForAbstractClass('\Mosdef\Attachments\Images\Attachment');
            static::$attachment->getFileFromRequest('image');
            static::$responsiveConfiguration = new Configuration();
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
        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertTrue(!empty($collection->getSrc()));
        $this->assertTrue(!empty($collection->getSizes()));
        $this->assertTrue(is_array($collection->getSizes()));
    }

    public function testUnlink()
    {
        // manually get a list of file paths to be able to validate they're all removed
        $pattern = base_path(trim(static::$attachment->file_path, '/') . '/' . pathinfo(static::$attachment->file_name, PATHINFO_FILENAME) . '*');
        $paths = glob($pattern);

        static::$attachment->unlink();

        $this->assertTrue(count($paths) > 0);
        foreach($paths as $path) {
            $this->assertFalse(file_exists($path));
        }
    }
}