<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Transaction;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'from_user_id',
        'to_user_id',
        'score',
        'comment',
    ];

    // 評価した側
    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    // 評価された側
    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    // どの取引の評価か
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
