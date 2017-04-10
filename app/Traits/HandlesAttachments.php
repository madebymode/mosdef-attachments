<?php
namespace Mosdef\Attachments\Traits;

use Request;
use SplFileInfo;

trait HandlesAttachments
{
    /**
     * create an attachment from an uploaded file in the current request
     *
     * @param  string $name the field name of the uploaded attachment
     * @param  array $options
     * @return
     */
    public static function createFromRequest($name, array $options = [])
    {
        $attachment = new static();

        $optionDefaults = [
            'uploadPath' => base_path('public/uploads')
        ];

        $options = array_replace_recursive($optionDefaults, $options);

        if (!is_dir($options['uploadPath'])) {
            @mkdir($uploadPath, 0755, true);
        }

        $file = Request::file($name);

        $fileName = $attachment->getRandomAttachmentFilename($file);
        $file->move($options['uploadPath'], $fileName);

        $attachment->file_path = str_replace(base_path(), '', $options['uploadPath']);
        $attachment->file_name = $fileName;

        return $attachment;
    }

    /**
     * creates a cleaned up file name based on the files name
     *
     * @param  SplFileInfo $file
     * @return string
     */
    public function getCleanAttachmentFilename(SplFileInfo $file)
    {
        $fileName = preg_replace('/[^a-z0-9_-]+/', '', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $ext = strtolower($file->getClientOriginalExtension());

        return $fileName . '.' . $ext;
    }

    /**
     * creates a random file name based on the files actual name
     *
     * @param  SplFileInfo $file
     * @return string
     */
    public function getRandomAttachmentFilename(SplFileInfo $file)
    {
        return uniqid() . '-' . $this->getCleanAttachmentFilename($file);
    }

    /**
     * @return Imagine\Image\ImagineInterface
     */
    public function getFileAsImage()
    {
        $imagine = app()->make('Imagine\Image\ImagineInterface');
        $image = $imagine->open(base_path(ltrim($this->file_path, '/') . '/' . $this->file_name));

        return $image;
    }

    /**
     * get the web accessible path
     * @return string
     */
    public function getWebPath()
    {
        $fullPath = base_path(ltrim($this->file_path, '/') . '/' . $this->file_name);
        return str_replace(public_path(), '', $fullPath);
    }

    /**
     * get a file object for the attachments file
     * @return SplFileInfo
     */
    public function getFile()
    {
        return new SplFileInfo(base_path(ltrim($this->file_path, '/') . '/' . $this->file_name));
    }

    /**
     * delete the attachment file from disk
     * @return bool
     */
    public function unlink()
    {
        $file = $this->getFile();
        return unlink($file->getPathname());
    }
}