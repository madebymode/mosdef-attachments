<?php
namespace Mosdef\Attachments\Contracts;

use Illuminate\Http\UploadedFile;

interface Attachment
{
    /**
     * return the attachment file as an Imageine instance
     * @return Imagine\Image\ImagineInterface
     */
    public function getFileAsImage();

    /**
     * return the web accessible path to an attachment
     * @return string
     */
    public function getWebPath();

    /**
     * return a randomized attachment name
     *
     * @param  UploadedFile $file
     * @return string
     */
    public function getRandomAttachmentFilename(UploadedFile $file);

    /**
     * return a cleaned up attachment name
     *
     * @param  UploadedFile $file
     * @return string
     */
    public function getCleanAttachmentFilename(UploadedFile $file);

    /**
     * get a file object for the attachments file
     * @return SplFileInfo
     */
    public function getFile();

    /**
     * delete the attachment file from disk
     * @return bool
     */
    public function unlink();
}