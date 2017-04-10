<?php
namespace Mosdef\Attachments;

use Illuminate\Database\Eloquent\Model;
use Mosdef\Attachments\Contracts\Attachment as AttachmentContract;
use Mosdef\Attachments\Traits\HandlesAttachments;
use Mosdef\Attachments\Traits\HandlesResponsiveAttachments;
use Mosdef\Attachments\Traits\HandlesAttachmentsFromUrl;
use Mosdef\Attachments\Traits\HandlesAttachmentsFromExistingFile;

class Attachment extends Model implements AttachmentContract
{
    use HandlesAttachments;
    use HandlesAttachmentsFromUrl;
    use HandlesAttachmentsFromExistingFile;
}