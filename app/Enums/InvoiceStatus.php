<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case DRAFT = 'draft';
    case SENT = 'sent';
    case VIEWED = 'viewed';
    case PARTIAL = 'partial';
    case PAID = 'paid';
    case OVERDUE = 'overdue';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::SENT => 'Sent',
            self::VIEWED => 'Viewed',
            self::PARTIAL => 'Partially Paid',
            self::PAID => 'Paid',
            self::OVERDUE => 'Overdue',
            self::CANCELLED => 'Cancelled',
            self::REFUNDED => 'Refunded',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'gray',
            self::SENT => 'blue',
            self::VIEWED => 'purple',
            self::PARTIAL => 'yellow',
            self::PAID => 'green',
            self::OVERDUE => 'red',
            self::CANCELLED => 'gray',
            self::REFUNDED => 'orange',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::DRAFT => 'pencil',
            self::SENT => 'paper-airplane',
            self::VIEWED => 'eye',
            self::PARTIAL => 'currency-dollar',
            self::PAID => 'check-circle',
            self::OVERDUE => 'exclamation-circle',
            self::CANCELLED => 'x-circle',
            self::REFUNDED => 'receipt-refund',
        };
    }
}