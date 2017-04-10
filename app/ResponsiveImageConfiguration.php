<?php
namespace Mosdef\Attachments;

class ResponsiveImageConfiguration
{
    protected $sizes;

    public function setSizes(array $sizes = [])
    {
        $this->sizes = $sizes;
    }

    public function getSizes()
    {
        return $this->sizes;
    }
}