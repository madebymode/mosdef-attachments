<?php
namespace Mosdef\Attachments;

use Request;
use Requests;
use Illuminate\Database\Eloquent\Model;
use Mosdef\Attachments\Contracts\Attachment as AttachmentContract;
use Illuminate\Http\UploadedFile;

abstract class Attachment extends Model implements AttachmentContract
{
    protected $table = 'attachments';

    public function getFileFromExistingFile($path, array $options = [])
    {
        $optionDefaults = [
        ];

        $options = array_replace_recursive($optionDefaults, $options);

        if (!file_exists($path)) {
            // throw new exception - file doesn't exist
        }

        $this->file_path = str_replace(base_path(), '', dirname($path));
        $this->file_name = basename($path);

        return $this->getFile();
    }

    public function getFileFromUrl($url, array $options = [])
    {
        $optionDefaults = [
            'uploadPath' => base_path('public/uploads')
        ];

        $options = array_replace_recursive($optionDefaults, $options);

        if (!is_dir($options['uploadPath'])) {
            @mkdir($uploadPath, 0755, true);
        }

        $extensionGuesser = app()->make('ExtensionGuesser');

        $fileResponse = Requests::get($url);

        $extension = $extensionGuesser->guess($fileResponse->headers['content-type']);

        if (empty($extension)) {
            // throw new Exception, invalid file type
        }

        $fileName = sha1($url) . '.' . $extension;
        $fullFilePath = rtrim($options['uploadPath'], '/') . '/' . $fileName;

        file_put_contents($fullFilePath, $fileResponse->body);

        $existingAttachment = static::where(['file_name' => $fileName])->first();
        if ($existingAttachment) {
            return $existingAttachment;
        }

        $this->file_path = str_replace(base_path(), '', $options['uploadPath']);
        $this->file_name = $fileName;

        return $this->getFile();
    }

    /**
     * create an attachment from an uploaded file in the current request
     *
     * @param  string $name the field name of the uploaded attachment
     * @param  array $options
     * @return
     */
    public function getFileFromRequest($name, array $options = [])
    {
        $optionDefaults = [
            'uploadPath' => base_path('public/uploads')
        ];

        $options = array_replace_recursive($optionDefaults, $options);

        if (!is_dir($options['uploadPath'])) {
            @mkdir($uploadPath, 0755, true);
        }

        $file = Request::file($name);

        $fileName = $this->getRandomAttachmentFilename($file);
        $file->move($options['uploadPath'], $fileName);

        $this->file_path = str_replace(base_path(), '', $options['uploadPath']);
        $this->file_name = $fileName;

        return $this->getFile();
    }

    /**
     * creates a cleaned up file name based on the files name
     *
     * @param  UploadedFile $file
     * @return string
     */
    public function getCleanAttachmentFilename(UploadedFile $file)
    {
        $fileName = preg_replace('/[^a-z0-9_-]+/', '', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $ext = strtolower($file->getClientOriginalExtension());

        return $fileName . '.' . $ext;
    }

    /**
     * creates a random file name based on the files actual name
     *
     * @param  UploadedFile $file
     * @return string
     */
    public function getRandomAttachmentFilename(UploadedFile $file)
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
     * @return UploadedFile
     */
    public function getFile()
    {
        return new UploadedFile(base_path(ltrim($this->file_path, '/') . '/' . $this->file_name), $this->file_name);
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