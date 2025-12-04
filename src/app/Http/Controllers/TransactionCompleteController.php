<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Support\Facades\Mail;
use App\Mail\TransactionCompletedMail;

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
            'status' => Transaction::STATUS_CONFIRMED
        ]);

        // 出品者にメール送信
        Mail::to($transaction->item->seller->email)
            ->send(new TransactionCompletedMail($transaction));

        return back();
    }
}
