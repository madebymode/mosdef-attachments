# MosDef Attachments

## PHP Extensions
- pecl-imagick

## Installation

Require the package in the composer.json file

```json
"require": {
    "madebymode/mosdef-attachments": "dev-master"
},
```

Add the repository to the composer.json file

```json
"repositories": [
    {
        "type": "vcs",
        "url":  "git@github.com:madebymode/mosdef-attachments.git"
    }
]
```

Publish the attachment migrations
```bash
php artisan vendor:publish --tag=attachment-migrations
```

Install the `TemplateServiceProvider` in `config/app.php`. It should be added in the 'Application Service Providers' section.

```php
[
    /*
     * Application Service Providers...
     */
    Mosdef\Helpers\Providers\TemplateServiceProvider::class
]
```


## Usage

```
<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        // create a new instance of an attachment model.
        $attachment = app()->make('App\Attachment');

        // pull the file from the request. in this case, the field name is 'image'
        $attachment->getFileFromRequest('image');

        // move the file to a publicly viewable destination.
        // by default, files are saved in the storage directory.
        $attachment->move(public_path('uploads'));

        // save the attachment to the database
        $attachment->save();

        return response()->json(['attachment' => $attachment]);
    }
}

```
