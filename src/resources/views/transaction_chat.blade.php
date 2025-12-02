@extends("layouts.app")

@push("styles")
    <link rel="stylesheet" href="{{ asset("css/transaction_chat.css") }}" />
@endpush

@section("content")
    <main class="transactions">
        {{-- 左サイドバー：その他の取引一覧 --}}
        <aside class="transactions-sidebar">
            <h1 class="transactions-sidebar__title">その他の取引</h1>

            <ul class="transactions-sidebar__list">
                @foreach ($wipTransactions as $other)
                    <li class="transactions-sidebar__item {{ $other->id === $transaction->id ? "is-active" : "" }}">
                        <a class="transactions-sidebar__link" href="{{ route("messages.show", $other->id) }}">
                            <div class="transactions-sidebar__text">
                                <h2 class="transactions-sidebar__name">{{ $other->item->item_name }}</h2>
                                @if (($other->unread_count ?? 0) > 0)
                                    <span class="transaction-sidebar__badge">{{ $other->unread_count }}</span>
                                @endif
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </aside>

        {{-- 右側メインエリア --}}
        <section class="transactions-main">
            {{-- 上部ヘッダー --}}
            <header class="transactions-header">
                <h1 class="transactions-header__title">「{{ $partner->name }}」さんとの取引画面</h1>

                {{-- 取引完了ボタン（購入者のみ表示） --}}
                @if (auth()->id() === $transaction->buyer_id)
                    {{--
                        <form method="POST" action="{{ route("transactions.complete", $transaction->id) }}">
                        @csrf
                        @method("patch")
                        <button type="submit" class="transactions-header__complete">取引を完了する</button>
                        </form>
                    --}}
                @endif
            </header>

            {{-- 商品情報エリア --}}
            <section class="transactions-item">
                <div class="transactions-item__image">
                    <img src="{{ $transaction->item->image_url }}" alt="{{ $transaction->item->item_name }}" />
                </div>
                <div class="transactions-item__info">
                    <p class="transactions-item__name">{{ $transaction->item->item_name }}</p>
                    <p class="transactions-item__price">{{ number_format($transaction->item->price) }}円</p>
                </div>
            </section>

            {{-- メッセージ一覧 --}}
            <section class="transactions-messages">
                @foreach ($transaction->messages as $message)
                    <div
                        class="transactions-message {{ $message->user_id === auth()->id() ? "transactions-message--me" : "transactions-message--other" }}"
                    >
                        <div class="transactions-message__avatar">
                            {{-- アイコンは仮。プロフィール画像があれば差し替え --}}
                            <span class="transactions-message__avatar-circle"></span>
                        </div>

                        <div class="transactions-message__body">
                            <div class="transactions-message__header">
                                <span class="transactions-message__user">{{ $message->user->name }}</span>
                                <span class="transactions-message__time">{{ $message->created_at->format("Y/m/d H:i") }}</span>
                            </div>
                            <p class="transactions-message__text">{{ $message->body }}</p>

                            @if ($message->user_id === auth()->id())
                                <div class="transactions-message__actions">
                                    {{-- 編集・削除はあとでFN010/FN011と連動 --}}
                                    <a href="#" class="transactions-message__edit">編集</a>
                                    <a href="#" class="transactions-message__delete">削除</a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </section>

            {{-- 入力フォーム（画面下部） --}}
            <section class="transactions-input">
                {{-- エラー表示 --}}
                @if ($errors->any())
                    <div class="transactions-errors">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form
                    method="POST"
                    action="{{ route("messages.store", $transaction->id) }}"
                    enctype="multipart/form-data"
                    class="transactions-input__form"
                >
                    @csrf

                    <textarea
                        name="body"
                        class="transactions-input__textarea"
                        placeholder="取引メッセージを記入してください"
                        rows="2"
                    >
{{ old("body") }}</textarea
                    >

                    <div class="transactions-input__footer">
                        <label class="transactions-input__image-button">
                            画像を追加
                            <input type="file" name="image_path" class="transactions-input__file" />
                        </label>

                        <button type="submit" class="transactions-input__send">送信</button>
                    </div>
                </form>
            </section>
        </section>
    </main>
@endsection

// 本文入力保持
@push("scripts")
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const textarea = document.querySelector('textarea[name="body"]');
            const key = 'transaction_draft_{{ $transaction->id }}';

            // ページ表示時：localStorage に残っている下書きをセット
            const saved = localStorage.getItem(key);
            if (saved) {
                textarea.value = saved;
            }

            // 入力するたびに保存
            textarea.addEventListener('input', () => {
                localStorage.setItem(key, textarea.value);
            });

            // 送信時：下書きを削除
            const form = textarea.closest('form');
            form.addEventListener('submit', () => {
                localStorage.removeItem(key);
            });
        });
    </script>
@endpush
