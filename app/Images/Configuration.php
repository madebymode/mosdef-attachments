<?php
namespace Mosdef\Attachments\Images;

class Configuration
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