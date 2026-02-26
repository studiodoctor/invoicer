<?php

namespace App\Enums;

enum QuoteStatus: string
{
    case DRAFT = 'draft';
    case SENT = 'sent';
    case VIEWED = 'viewed';
    case SIGNED = 'signed';
    case DECLINED = 'declined';
    case EXPIRED = 'expired';
    case CONVERTED = 'converted';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::SENT => 'Sent',
            self::VIEWED => 'Viewed',
            self::SIGNED => 'Signed',
            self::DECLINED => 'Declined',
            self::EXPIRED => 'Expired',
            self::CONVERTED => 'Converted to Invoice',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'gray',
            self::SENT => 'blue',
            self::VIEWED => 'purple',
            self::SIGNED => 'green',
            self::DECLINED => 'red',
            self::EXPIRED => 'orange',
            self::CONVERTED => 'teal',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::DRAFT => 'pencil',
            self::SENT => 'paper-airplane',
            self::VIEWED => 'eye',
            self::SIGNED => 'check-circle',
            self::DECLINED => 'x-circle',
            self::EXPIRED => 'clock',
            self::CONVERTED => 'document-duplicate',
        };
    }
}