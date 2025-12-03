<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;

class TransactionCompleteController extends Controller
{

    public function complete(Transaction $transaction)
    {
        $userId = auth()->id();

        // 購入者のみ完了可能
        if ($transaction->buyer_id !== $userId) {
            abort(403, '購入者のみ完了できます');
        }

        // 取引中のみ完了できる
        if ($transaction->status !== Transaction::STATUS_WIP) {
            abort(403, 'この取引はすでに完了しています');
        }

        // ステータス更新
        $transaction->update([
            'status' => Transaction::STATUS_COMPLETED
        ]);

        // モーダルを開くフラグ
        return back()->with('openRatingModal', true);
    }
}
