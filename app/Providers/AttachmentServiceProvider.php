<?php
namespace Mosdef\Attachments\Providers;

use Illuminate\Support\ServiceProvider;
use Imagine\Imagick\Imagine;
use Mosdef\Attachments\Attachment;

class AttachmentServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->bind('Imagine\Image\AbstractImagine', function($app) {
            return new Imagine();
        });

        $this->app->bind('Mosdef\Attachments\Contracts\Attachment', function($app) {
            return new Attachment();
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