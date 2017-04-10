<?php
namespace Mosdef\Attachments\Providers;

use Illuminate\Support\ServiceProvider;
use Imagine\Imagick\Imagine;
use Mosdef\Attachments\Attachment;
use Mosdef\Attachments\ImagesAttachment as ImageAttachment;
use Mosdef\Attachments\Images\Collection;

class AttachmentServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->bind('Imagine\Image\ImagineInterface', function($app) {
            return new Imagine();
        });

        $this->app->bind('Mosdef\Attachments\Contracts\Attachment', function($app) {
            return new Attachment();
        });

        $this->app->bind('Mosdef\Attachments\Images\Attachment', function($app) {
            return new ImageAttachment();
        });

        $this->app->bind('Mosdef\Attachments\Images\Collection', function($app) {
            return new Collection();
        });

        $this->app->bind('Mosdef\Attachments\Images\Configuration', function($app) {
            return new Configuration();
        });

        $this->publishMigrations();
    }

    protected function publishMigrations()
    {
        $ts = date('Y_m_d_His', time());

        $this->publishes([

            dirname(dirname(__DIR__)) . '/database/migrations/create_attachments.php'
                => database_path('migrations/' . $ts . '_create_attachments.php'),

        ], 'attachment-migrations');
    }
}