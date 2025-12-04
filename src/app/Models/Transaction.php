<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Item;

class Transaction extends Model
{
    use HasFactory;

    const STATUS_WIP          = 1;  // 取引中
    const STATUS_CONFIRMED    = 2;  // 購入者が取引完了ボタン押下
    const STATUS_COMPLETED    = 3;  // 双方が評価を完了した時点

    protected $fillable = [
        'item_id',
        'purchase_price',
        'payment_method',
        'shipping_postal_code',
        'shipping_address',
        'shipping_building',
        'seller_id',
        'buyer_id',
        'status',
    ];

    protected $casts = [
        'payment_method' => 'integer',
    ];


    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // この取引に紐づく全メッセージ
    public function messages()
    {
        return $this->hasMany(TransactionMessage::class);
    }

    // １取引に対する評価一覧
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
