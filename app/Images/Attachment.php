<?php
namespace Mosdef\Attachments\Images;

use Mosdef\Attachments\Traits\HandlesResponsiveAttachments;
use Mosdef\Attachments\Attachment as BaseAttachment;

class Attachment extends BaseAttachment
{
    use HandlesResponsiveAttachments;
}