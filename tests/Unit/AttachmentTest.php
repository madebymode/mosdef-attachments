<?php
namespace Mosdef\Attachments\Tests;

use Illuminate\Http\UploadedFile;
use Request;
use Artisan;
use Requests_Exception;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class AttachmentTest extends \Tests\TestCase
{
    protected static $attachment;

    public function setUp()
    {
        parent::setUp();

        Artisan::call('migrate');

        if (empty(static::$attachment)) {
            $fileFactory = UploadedFile::fake();
            $imgFile = $fileFactory->image('test-img.jpg');

            $request = Request::instance();
            $request->files->add(['image' => $imgFile]);

            static::$attachment = $this->getMockForAbstractClass('\Mosdef\Attachments\Attachment');
        }
    }

    public function testGetFileFromRequest()
    {
        $file = static::$attachment->getFileFromRequest('image');
        $this->assertInstanceOf(File::class, $file);
    }

    public function testGetWebPathWithNonPublicFile()
    {
        $this->expectException(\LogicException::class);
        $this->assertEquals('/uploads/' . static::$attachment->file_name, static::$attachment->getWebPath());
    }

    public function testMove()
    {
        static::$attachment->move(public_path('uploads'));
    }

    public function testGetWebPathWithPublicFile()
    {
        $this->assertEquals('/uploads/' . static::$attachment->file_name, static::$attachment->getWebPath());
    }

    public function testGetFile()
    {
        $file = static::$attachment->getFile();

        $this->assertInstanceOf(File::class, $file);
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

    public function testGetFileFromUrl()
    {
        $file = static::$attachment->getFileFromUrl('http://replygif.net/i/132.gif');
        $this->assertInstanceOf(File::class, $file);
        $this->assertEquals('gif', $file->getExtension());
        static::$attachment->unlink();

        $file = static::$attachment->getFileFromUrl('http://rs263.pbsrc.com/albums/ii134/imdbjb/gifs/65xv2d.gif~c200');
        $this->assertInstanceOf(File::class, $file);
        $this->assertEquals('gif', $file->getExtension());
        static::$attachment->unlink();

        $this->expectException(Requests_Exception::class);
        static::$attachment->getFileFromUrl('http://invaliddomain.com/path/to/does/not/exist');
    }

    public function testGetFileFromExistingFile()
    {
        copy(__DIR__ . '/../fixtures/demo.pdf', storage_path('app/demo.pdf'));

        $file = static::$attachment->getFileFromExistingFile(storage_path('app/demo.pdf'));
        $this->assertInstanceOf(File::class, $file);
        $this->assertEquals('pdf', $file->getExtension());
        static::$attachment->unlink();

        $this->expectException(FileNotFoundException::class);
        static::$attachment->getFileFromExistingFile('path/to/file/that/doesnt/exist.pdf');
    }
}