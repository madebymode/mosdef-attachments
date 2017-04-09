<?php
namespace Mosdef\Attachments\Traits;

use Request;
use SplFileInfo;

trait HandlesResponsiveAttachments
{
    protected $imageTypes = [];

    public function createResponsiveSizes($imageType)
    {
        $image = $this->getFileAsImage();

        $imageDimensions = $image->getSize();

        foreach ($sizes as $size) {
            $suffix = '@' . $size;

            $extension = pathinfo($this->file_name, PATHINFO_EXTENSION);
            $thumbName = preg_replace('/(\.' . preg_quote($extension) . ')$/', $suffix . '$1', $this->file_name);

            $thumbPath = ltrim($this->file_path, '/') . '/' . $this->file_name;

            $image->resize($imageDimensions->scale($size / $imageDimensions->getWidth()))->save(base_path($thumbPath));

            // Return thumb path
            $generatedThumbs[] = $thumbPath;
        }

        return $generatedThumbs;
    }

    public function getImageCollection(array $sizes = [])
    {
    // public function getResponsiveImages($attachment = null) {
    //     if (!$attachment) return;
    //     $sizes = array('800','600','400');
    //     $thumbs = array();
    //     foreach ($sizes as $size) {
    //         $suffix = '@' . $size;
    //         $thumb_path = preg_replace('/(\.jpg|\.png)/', $suffix . '$1', ltrim($attachment->file_path, '/') . '/' . $attachment->file_name);
    //         if (File::exists(base_path($thumb_path))) { $thumbs[$size] = str_replace(public_path(), '', base_path($thumb_path)); }
    //     }
    //     if(count(array_intersect_key(array_flip($sizes), $thumbs)) === count($sizes)) {
    //         return $thumbs;
    //     }
    //     return;
    // }
    //
    //
    //
    //
    //
    //
            // $filename = $field_props['filename'];

            // $path = '/img/_content/' . $class_name . '/_default/' . $filename;

            // if (!file_exists($options['base_path'] . '/' . $filename)) {
            //     continue;
            // }

            // $path = Assets::getUrl($options['web_path'] . '/' . pathinfo($filename, PATHINFO_FILENAME) . '.' . pathinfo($filename, PATHINFO_EXTENSION), $cache_ts);

            // if (!empty(static::$_images[$field_name]['responsive_widths'])) {
            //     $collection->{$field_name . '_src'} = $path;

            //     // Add src for each srcset size (so sizes can be referenced as needed)
            //     foreach (static::$_images[$field_name]['responsive_widths'] as $size) {
            //         $collection->{$field_name . '_' . $size . '_src'} = preg_replace('/(\.[0-9]+)?(\.jpg|\.png)/', '@' . $size . '$1$2', $path);
            //     }

            //     $collection->{$field_name} = (object) ['srcset' => static::getImgSrcset($path, $field_name) ];
            //     continue;
            // }

            // $collection->{$field_name} = $path;
    }
}