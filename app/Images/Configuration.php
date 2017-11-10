<?php
namespace Mosdef\Attachments\Images;

class Configuration
{
    protected $sizes;

    /**
     * Set an array of widths for for generating multiple sizes of images
     * @param array $sizes array[int]
     */
    public function setSizes(array $sizes = [])
    {
        $this->sizes = $sizes;
    }

    /**
     * Get the current set of image sizes
     * @return array
     */
    public function getSizes()
    {
        return $this->sizes ?? [];
    }
}