<?php

declare(strict_types=1);

namespace Modules\Invoices\Domain\Entities;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Ramsey\Uuid\Uuid;

/**
 * @property string $id
 * @property string $customer_name
 * @property string $customer_email
 * @property StatusEnum $status
 * @property Collection<InvoiceProductLine> $invoiceProductLines
 * @property int $total_price
 */
class Invoice extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'customer_name',
        'customer_email',
        'status'
    ];

    protected function casts(): array
    {
        return [
            'id' => 'string',
            'customer_name' => 'string',
            'customer_email' => 'string',
            'status' => StatusEnum::class
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = Uuid::uuid4()->toString();
            }
        });
    }

    public function invoiceProductLines(): HasMany
    {
        return $this->hasMany(InvoiceProductLine::class, 'invoice_id', 'id');
    }

    public function getTotalPriceAttribute(): int
    {
        // optimalization to avoid multiple queries to the database (InvoicesFacade::getAll)
        if (array_key_exists('total_price', $this->attributes)) {
            return (int) $this->attributes['total_price'];
        }

        return $this->getTotalPrice();
    }

    public function getTotalPrice(): int
    {
        /** @var Collection<InvoiceProductLine> $invoiceProductLines */
        $invoiceProductLines = $this->invoiceProductLines()->get();

        return $invoiceProductLines->sum(
            fn($line) => $line instanceof InvoiceProductLine ? $line->getTotalUnitPrice() : 0
        );
    }

    // Invoice Critera: To be sent, an invoice must contain product lines with both quantity
    //and unit price as positive integers greater than zero.
    public function hasValidProductLines(): bool
    {
        /** @var Collection<InvoiceProductLine> $invoiceProductLines */
        $invoiceProductLines = $this->invoiceProductLines()->get();

        if ($invoiceProductLines->isEmpty()) {
            return false;
        }

        foreach ($invoiceProductLines as $invoiceProductLine) {
            if ($invoiceProductLine->getTotalUnitPrice() <= 0) {
                return false;
            }
        }

        return true;
    }
}
