<?php
namespace Mosdef\Attachments\Contracts;

use SplFileInfo;

interface Attachment
{
    /**
     * return the attachment file as an Imageine instance
     * @return Imagine\Image\AbstractImagine
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
     * @param  SplFileInfo $file
     * @return string
     */
    public function getRandomAttachmentFilename(SplFileInfo $file);

    /**
     * return a cleaned up attachment name
     *
     * @param  SplFileInfo $file
     * @return string
     */
    public function getCleanAttachmentFilename(SplFileInfo $file);
}