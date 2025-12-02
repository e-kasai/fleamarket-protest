{{--
    @props([
    "items",
    ])
    
    <section class="item-grid">
    @foreach ($items as $item)
    <div class="item-card">
    <a class="item-card__link" href="{{ route("details.show", $item) }}">
    <img src="{{ $item->image_url }}" alt="{{ $item->item_name }}" class="item-card__img" />
    <p class="item-card__name">
    {{ $item->item_name }}
    @if ($item->is_sold)
    <span class="item-card__sold">SOLD</span>
    @endif
    </p>
    </a>
    </div>
    @endforeach
    </section>
--}}

@props([
    "items",
    "linkRoute" => "details.show",
])

<section class="item-grid">
    @foreach ($items as $item)
        @php
            // Item or Transaction の判定
            $isTransaction = $item instanceof \App\Models\Transaction;

            // 画像・商品名の取得
            $image = $isTransaction ? $item->item->image_url : $item->image_url;
            $name = $isTransaction ? $item->item->item_name : $item->item_name;

            // リンクID（Transaction なら transaction_id、Item なら item_id）
            $linkId = $item->id;
        @endphp

        <div class="item-card">
            <a class="item-card__link" href="{{ route($linkRoute, $linkId) }}">
                {{-- 未読メッセージ数の表示 --}}
                @if (($item->unread_count ?? 0) > 0)
                    <span class="item-card__badge">{{ $item->unread_count }}</span>
                @endif

                <img src="{{ $image }}" alt="{{ $name }}" class="item-card__img" />
                <p class="item-card__name">
                    {{ $name }}
                    @if (! $isTransaction && $item->is_sold)
                        <span class="item-card__sold">SOLD</span>
                    @endif
                </p>
            </a>
        </div>
    @endforeach
</section>
