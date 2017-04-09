<?php
namespace Mosdef\Attachments\Providers;

use Illuminate\Support\ServiceProvider;
use Imagine\Imagick\Imagine;

class AttachmentServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->app->bind('Imagine\Image\AbstractImagine', function($app) {
            return new Imagine\Imagick\Imagine();
        });

        $this->app->bind('Mosdef\Attachments\Contracts\Attachment', function($app) {
            return new Mosdef\Attachments\Attachment();
        });
    }

    public function register()
    {

    }

}