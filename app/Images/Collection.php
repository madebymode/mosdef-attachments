<?php
namespace Mosdef\Attachments\Images;

use ArrayAccess;

class Collection implements ArrayAccess
{
    /**
     * The src image that other sizes are based on
     * @var string
     */
    protected $src;

    /**
     * Collection of images for each size
     * @var array
     */
    protected $sizes;

    /**
     * Set the src image. The image that the various sizes are based on
     * @param string $src
     */
    public function setSrc($src)
    {
        $this->src = $src;
    }

    /**
     * Get the src image
     * @return string
     */
    public function getSrc()
    {
        return $this->src;
    }

    /**
     * Set the images for various sizes. An array of image paths indexed by their width.
     * @param array $sizes
     */
    public function setSizes(array $sizes = [])
    {
        $this->sizes = $sizes;
    }

    /**
     * Get the images for various sizes.
     * @return array Image paths indexed by their widths
     */
    public function getSizes()
    {
        return $this->sizes;
    }

    /**
     * Get a string formatted as a srcset attribute.
     * @param  array  $sizes Optionally only include specific sizes
     * @return string
     */
    public function getSrcSetAttr(array $sizes = [])
    {
        $images = $this->getSizes();

        if (!empty($sizes)) {
            $images = array_intersect_key($images, array_fill_keys($sizes, null));
        }

        if (empty($images)) {
            $images = [];
        }

        $attr = [];
        foreach($images as $key => $image) {
            $attr[] = $image . ' ' . $key . 'w';
        }

        return implode(", ", $attr);
    }

    /**
     * Implement offsetExists for ArrayAccessible
     * @param  mixed $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->getSizes()) || property_exists($this, $offset);
    }

    /**
     * Implement offsetGet for ArrayAccessible
     * @param  mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        if (array_key_exists($offset, $this->sizes)) {
            return $this->sizes[$offset];
        }

        return $this->{$offset};
    }

    /**
     * Implement offsetSet for ArrayAccessible
     * @param  mixed $offset
     * @param  mixed $value
     * @throws LogicException
     */
    public function offsetSet($offset, $value)
    {
        throw new \LogicException('cannot modify image collection through array access');
    }

    /**
     * Implement offsetUnset for ArrayAccessible
     * @param  mixed $offset
     * @throws LogicException
     */
    public function offsetUnset($offset)
    {
        throw new \LogicException('cannot modify image collection through array access');
    }
}