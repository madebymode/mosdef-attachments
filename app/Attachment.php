<?php
namespace Mosdef\Attachments;

use Request;
use Requests;
use Illuminate\Database\Eloquent\Model;
use Mosdef\Attachments\Contracts\Attachment as AttachmentContract;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Storage;

abstract class Attachment extends Model implements AttachmentContract
{
    protected $table = 'attachments';

    public function jsonSerialize()
    {
        if (!in_array('web_path', $this->appends)) {
            $this->appends[] = 'web_path';
        }

        return parent::jsonSerialize();
    }

    /**
     * get a file from an existing file
     * @param  string $path
     * @param  array  $options
     * @return Symfony\Component\HttpFoundation\File\File
     */
    public function getFileFromExistingFile($path, array $options = [])
    {
        $optionDefaults = [
        ];

        $options = array_replace_recursive($optionDefaults, $options);

        if (!file_exists($path)) {
            // throw new exception - file doesn't exist
            throw new FileNotFoundException("$path does not exist");
        }

        $this->file_path = str_replace(base_path(), '', dirname($path));
        $this->file_name = basename($path);

        return $this->getFile();
    }

    /**
     * get file from url. downloads the file from a remote url in to
     * @param  [type] $url     [description]
     * @param  array  $options [description]
     * @return [type]          [description]
     */
    public function getFileFromUrl($url, array $options = [])
    {
        $optionDefaults = [
            // 'uploadPath' => storage_path('app/public/uploads')
        ];

        $options = array_replace_recursive($optionDefaults, $options);

        $fileResponse = Requests::get($url);

        $extensionGuesser = app()->make('ExtensionGuesser');
        $extension = $extensionGuesser->guess($fileResponse->headers['content-type']);

        if (empty($extension)) {
            // throw new Exception, invalid file type
            throw new \RuntimeException("Cannot determine file extension for file at $url");
        }

        $fileName = sha1($url) . '.' . $extension;
        $filePath = 'uploads/' . $fileName;
        Storage::disk('local')->put($filePath, $fileResponse->body, ['disk' => 'local']);

        $existingAttachment = static::where(['file_name' => $fileName])->first();
        if ($existingAttachment) {
            return $existingAttachment;
        }

        $fullFilePath = Storage::disk('local')->getDriver()->getAdapter()->applyPathPrefix($filePath);

        $this->file_path = pathinfo($fullFilePath, PATHINFO_DIRNAME);
        $this->file_name = pathinfo($fullFilePath, PATHINFO_BASENAME);

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
        ];

        $options = array_replace_recursive($optionDefaults, $options);

        $file = Request::file($name);

        $fileName = $this->getRandomFilename($file->getClientOriginalName());

        $filePath = $file->storeAs('uploads', $fileName, ['disk' => 'local']);

        $fullFilePath = Storage::disk('local')->getDriver()->getAdapter()->applyPathPrefix($filePath);

        $this->file_path = pathinfo($fullFilePath, PATHINFO_DIRNAME);
        $this->file_name = pathinfo($fullFilePath, PATHINFO_BASENAME);

        return $this->getFile();
    }

    /**
     * creates a cleaned up file name based on the files name
     *
     * @param  string $filename
     * @return string
     */
    public function getCleanFilename($filename)
    {
        $fileName = preg_replace('/[^a-z0-9_-]+/', '', pathinfo($filename, PATHINFO_FILENAME));
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        return $fileName . (!empty($ext) ? '.' . $ext : '');
    }

    /**
     * creates a random file name based on the files actual name
     *
     * @param  string $file
     * @return string
     */
    public function getRandomFilename($filename)
    {
        return uniqid() . '-' . $this->getCleanFilename($filename);
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
     *
     * @throws LogicException
     * @return string
     */
    public function getWebPath()
    {
        $fullPath = base_path(ltrim($this->file_path, '/') . '/' . $this->file_name);

        if (stripos($fullPath, public_path()) === false) {
            throw new \LogicException("File path not in web browseable directory. This likely means that path for the file was saved incorrectly.");
        }

        return str_replace(public_path(), '', $fullPath);
    }

    /**
     * get a file object for the attachments file
     *
     * @return Symfony\Component\HttpFoundation\File
     */
    public function getFile()
    {
        $filePath = rtrim($this->file_path, '/') . '/' . $this->file_name;

        if (stripos($filePath, base_path()) === false) {
            $filePath = base_path(ltrim($filePath, '/'));
        }

        return new File($filePath, true);
    }

    /**
     * delete the attachment file from disk
     *
     * @return bool
     */
    public function unlink()
    {
        $file = $this->getFile();
        $this->file_name = null;
        $this->file_path = null;
        return unlink($file->getPathname());
    }

    /**
     * move the file to a different location
     *
     * @param  string $directory
     * @param  array $options - an array of options for moving a file
     * @return Symfony\Component\HttpFoundation\File\File
     */
    public function move($directory, array $options = [])
    {
        $optionDefaults = [
            'name' => null,
        ];

        $options = array_replace_recursive($optionDefaults, $options);

        $newFile = $this->getFile()->move($directory, $options['name']);

        $this->file_name = $newFile->getFilename();
        $this->file_path = $newFile->getPath();

        return $newFile;
    }

    /**
     * setter for the file_path attribute. strips out base path if it's present
     *
     * @param string $filePath
     */
    public function setFilePathAttribute($filePath)
    {
        if (stripos($filePath, base_path()) !== false) {
            $filePath = str_replace(base_path(), '', $filePath);
        }

        $this->attributes['file_path'] = $filePath;
    }

    public function getWebPathAttribute()
    {
        return $this->getWebPath();
    }
}