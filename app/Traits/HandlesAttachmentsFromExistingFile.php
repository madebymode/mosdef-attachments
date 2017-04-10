<?php
namespace Mosdef\Attachments\Traits;

use Requests;
use SplFileInfo;

trait HandlesAttachmentsFromExstingFile
{
    public static function createFromFile($path, array $opts = [])
    {
        $optDefaults = [
        ];

        $opts = array_replace_recursive($optDefaults, $opts);

        $attachment = new static();

        if (file_exists($path)) {

            $attachment->file_path = str_replace(base_path(), '', dirname($path));
            $attachment->file_name = basename($path);
        }

        return $attachment;
    }
}