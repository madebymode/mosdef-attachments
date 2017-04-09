<?php
namespace Mosdef\Attachments;

use Illuminate\Database\Eloquent\Model;
use Mosdef\Attachments\Contracts\Attachment as AttachmentContract;
use Mosdef\Attachments\Traits\HandlesAttachments;

class Attachment extends Model implements AttachmentContract
{
    use HandlesAttachments;
}