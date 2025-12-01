<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Profile;
use App\Models\Comment;
use App\Models\Transaction;
use App\Models\Item;


class User extends Authenticatable implements MustVerifyEmail
{

    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // User(Parent)は0または1つのProfile(Child)を持つ
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    // User(Parent)は複数のItem(Child)を持つ = hasMany
    public function items()
    {
        return $this->hasMany(Item::class, 'seller_id');
    }

    // User(Parent)は複数のComment(Child)を持つ = hasMany
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // User(Parent)は複数のTransaction(Child)を持つ = hasMany
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'buyer_id');
    }

    //中間テーブルfavoritesのリレーション
    // itemsというメソッド名はすでにあるのでfavoriteItemsを使用
    public function favoriteItems()
    {
        return $this->belongsToMany(Item::class, 'favorites', 'user_id', 'item_id');
    }

    //１人のユーザーは複数の取引コメントを投稿可
    public function transactionMessages()
    {
        return $this->hasMany(TransactionMessage::class);
    }

    // ユーザーが他人に与えた評価
    public function ratingsGiven()
    {
        return $this->hasMany(Rating::class, 'from_user_id');
    }

    // ユーザーが他人から受けた評価
    public function ratingsReceived()
    {
        return $this->hasMany(Rating::class, 'to_user_id');
    }
}
