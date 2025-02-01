<?php

declare(strict_types=1);

namespace Modules\Invoices\Domain\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;

/**
 * @property string $id
 * @property string $invoice_id
 * @property string $product_name
 * @property int $quantity
 * @property int $unit_price
 */
class InvoiceProductLine extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'invoice_id',
        'product_name',
        'unit_price',
        'quantity',
    ];
    protected $casts = [
        'id' => 'string',
        'invoice_id' => 'string',
        'product_name' => 'string',
        'unit_price' => 'integer',
        'quantity' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = Uuid::uuid4()->toString();
            }
        });
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }

    public function getTotalUnitPrice(): int
    {
        return $this->unit_price * $this->quantity;
    }
}
