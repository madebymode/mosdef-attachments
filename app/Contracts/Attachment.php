<?php
namespace Mosdef\Attachments\Contracts;

use Symfony\Component\HttpFoundation\File\File;

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
     * @param  string $filename
     * @return string
     */
    public function getRandomFilename($filename);

    /**
     * return a cleaned up attachment name
     *
     * @param  string $file
     * @return string
     */
    public function getCleanFilename($filename);

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