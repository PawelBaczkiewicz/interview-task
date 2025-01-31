<?php

namespace Modules\Invoices\Domain\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Ramsey\Uuid\Uuid;

class Invoice extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'customer_name',
        'customer_email',
        'status',
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

    public function getTotalPrice(): int
    {
        return $this->invoiceProductLines->sum(function (InvoiceProductLine $invoiceProductLine) {
            return $invoiceProductLine->getTotalUnitPrice();
        });
    }
}
