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

Execute the following `php artisan vendor:publish --tag=attachment-migrations`