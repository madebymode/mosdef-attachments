<?php
namespace Mosdef\Attachments\Traits;

use Requests;
use SplFileInfo;

trait HandlesAttachmentsFromUrl
{
    public static function createFromUrl($url, array $options = [])
    {
        $optionDefaults = [
            'uploadPath' => base_path('public/uploads')
        ];

        $options = array_replace_recursive($optionDefaults, $options);

        if (!is_dir($options['uploadPath'])) {
            @mkdir($uploadPath, 0755, true);
        }

        $imageFileName = sha1($url);
        $imageExtMatch = null;
        preg_match('/\.(jpe?g|gif|png)/i', $url, $imageExtMatch);
        $imageResponse = \Requests::get($url);

        $attachment = new static();

        if ($imageExtMatch) {
            $imageFileName = $imageFileName . $imageExtMatch[0];
            $fullImagePath = $options['uploadPath'] . '/' . $imageFileName;
            file_put_contents($fullImagePath, $imageResponse->body);

            $existingAttachment = static::where(['file_name' => $imageFileName])->first();
            if ($existingAttachment) {
                return $existingAttachment;
            }

            $attachment->file_path = str_replace(base_path(), '', $options['uploadPath']);
            $attachment->file_name = $imageFileName;
        }

        return $attachment;
    }
}