<?php
namespace Mosdef\Attachments\Images;

use Mosdef\Attachments\Traits\HandlesResponsiveAttachments;
use Mosdef\Attachments\Attachment as BaseAttachment;

abstract class Attachment extends BaseAttachment
{
    protected $imageConfiguration;

    /**
     * create the name for a responsive image
     * @param  integer $size
     * @param  string $suffix
     * @return string
     */
    protected function getResponsiveFilename($size, $suffix = '@')
    {
        $suffix = $suffix . $size;
        $extension = pathinfo($this->file_name, PATHINFO_EXTENSION);
        return preg_replace('/(\.' . preg_quote($extension) . ')$/', $suffix . '$1', $this->file_name);
    }

    /**
     * set the image configuration
     * @param Configuration $config
     */
    public function setImageConfiguration(Configuration $config)
    {
        $this->imageConfiguration = $config;
    }

    /**
     * get the image configuration
     * @return Configuration
     */
    public function getImageConfiguration()
    {
        return $this->imageConfiguration;
    }

    /**
     * generate the responses mased on the image configuration
     * @return array - image paths
     */
    public function generateResponsiveSizes()
    {
        $image = $this->getFileAsImage();
        $imageConfiguration = $this->getImageConfiguration();

        if (empty($imageConfiguration)) {
            throw new \InvalidArgumentException('no image configuration provided');
        }

        $sizes = $imageConfiguration->getSizes();
        $imageDimensions = $image->getSize();

        foreach ($sizes as $size) {

            $thumbName = $this->getResponsiveFilename($size);
            $thumbPath = ltrim($this->file_path, '/') . '/' . $thumbName;

            $image->resize($imageDimensions->scale($size / $imageDimensions->getWidth()))->save(base_path($thumbPath));

            // Return thumb path
            $generatedThumbs[] = $thumbPath;
        }

        return $generatedThumbs;
    }

    /**
     * return an image collection instance
     * @return Mosdef\Attachments\ResponsiveImageCollection
     */
    public function getImageCollection()
    {
        $collection = app()->make('Mosdef\Attachments\Images\Collection');

        $imageConfiguration = $this->getImageConfiguration();

        if (empty($imageConfiguration)) {
            throw new \InvalidArgumentException('no image configuration provided');
        }

        $sizes = $imageConfiguration->getSizes();

        $webPath = $this->getWebPath();
        $webDir = dirname($webPath);

        $collection->setSrc($webPath);

        $responsiveImages = [];
        foreach($sizes as $size) {
            $webPath = $webDir . '/' . $this->getResponsiveFilename($size);
            $responsiveImages[$size] = $webPath;
        }

        $collection->setSizes($responsiveImages);

        return $collection;
    }

    /**
     * overrides the unline method to delete all the responsive sizes
     * @return void
     */
    public function unlink()
    {
        $pattern = base_path(trim($this->file_path, '/') . '/' . pathinfo($this->file_name, PATHINFO_FILENAME) . '*');
        $paths = glob($pattern);

        foreach($paths as $path) {
            unlink($path);
        }
    }

    public function jsonSerialize()
    {
        if (!in_array('image_collection', $this->appends)) {
            $this->appends[] = 'image_collection';
        }

        return parent::jsonSerialize();
    }

    public function getImageCollectionAttribute()
    {
        return $this->getImageCollection();
    }
}