<?php
namespace Mosdef\Attachments\Tests;

use Mosdef\Attachments\Attachment;
use Illuminate\Http\UploadedFile;
use Request;
use SplFileInfo;

class AttachmentTest extends \Tests\TestCase
{
    protected static $attachment;

    public function setUp()
    {
        parent::setUp();

        if (empty(static::$attachment)) {
            $fileFactory = UploadedFile::fake();
            $imgFile = $fileFactory->image('test-img.jpg');

            $request = Request::instance();
            $request->files->add(['image' => $imgFile]);

            static::$attachment = Attachment::createFromRequest('image');
        }
    }

    public function testGetWebPath()
    {
        $this->assertEquals('/uploads/' . static::$attachment->file_name, static::$attachment->getWebPath());
    }

    public function testGetFile()
    {
        $file = static::$attachment->getFile();

        $this->assertInstanceOf(SplFileInfo::class, $file);
        $this->assertTrue(!empty($file->getPathname()));
    }

    public function testGetFileAsImage()
    {
        $image = static::$attachment->getFileAsImage();
        // seems like assertInstanceOf should've been working here but wasn't.
        $this->assertTrue(array_key_exists(\Imagine\Image\ImageInterface::class, class_implements($image)));
    }

    public function testUnlink()
    {
        $file = static::$attachment->getFile();
        static::$attachment->unlink();

        $this->assertFalse(file_exists($file->getPathname()));
    }
}