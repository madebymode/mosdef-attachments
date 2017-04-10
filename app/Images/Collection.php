<?php
namespace Mosdef\Attachments\Images;

use ArrayAccess;

class Collection implements ArrayAccess
{
    /**
     * the src image that other sizes are based on
     * @var string
     */
    protected $src;

    /**
     * collection of images for each size
     * @var array
     */
    protected $sizes;

    /**
     * set the src image. the image that the various sizes are based on
     * @param string $src
     */
    public function setSrc($src)
    {
        $this->src = $src;
    }

    /**
     * get the src image
     * @return string
     */
    public function getSrc()
    {
        return $this->src;
    }

    /**
     * set the images for various sizes. an array of image paths indexed by their width
     * @param array $sizes
     */
    public function setSizes(array $sizes = [])
    {
        $this->sizes = $sizes;
    }

    /**
     * get the images for various sizes.
     * @return array of image paths indexed by their widths
     */
    public function getSizes()
    {
        return $this->sizes;
    }

    /**
     * get a string formatted as a srcset attribute.
     * @param  array  $sizes optionally only include specific sizes
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
     * implement offsetExists for ArrayAccessible
     * @param  mixed $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->getSizes()) || property_exists($this, $offset);
    }

    /**
     * implement offsetGet for ArrayAccessible
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
     * implement offsetSet for ArrayAccessible
     * @param  mixed $offset
     * @param  mixed $value
     * @throws LogicException
     */
    public function offsetSet($offset, $value)
    {
        throw new \LogicException('cannot modify image collection through array access');
    }

    /**
     * implement offsetUnset for ArrayAccessible
     * @param  mixed $offset
     * @throws LogicException
     */
    public function offsetUnset($offset)
    {
        throw new \LogicException('cannot modify image collection through array access');
    }
}