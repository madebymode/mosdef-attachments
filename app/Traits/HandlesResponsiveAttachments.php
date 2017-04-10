<?php
namespace Mosdef\Attachments\Traits;

use Mosdef\Attachments\Images\Configuration;
use Request;
use SplFileInfo;

trait HandlesResponsiveAttachments
{
    protected $imageConfiguration;

    protected function getResponsiveFilename($size, $suffix = '@')
    {
        $suffix = $suffix . $size;
        $extension = pathinfo($this->file_name, PATHINFO_EXTENSION);
        return preg_replace('/(\.' . preg_quote($extension) . ')$/', $suffix . '$1', $this->file_name);
    }

    public function setImageConfiguration(Configuration $config)
    {
        $this->imageConfiguration = $config;
    }

    public function getImageConfiguration()
    {
        return $this->imageConfiguration;
    }

    public function createResponsiveSizes()
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

    public function unlinkAll()
    {
        $pattern = base_path(trim($this->file_path, '/') . '/' . pathinfo($this->file_name, PATHINFO_FILENAME) . '*');
        $paths = glob($pattern);

        foreach($paths as $path) {
            unlink($path);
        }
    }
}