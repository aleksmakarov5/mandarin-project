@extends('header_menu')
@section('title')
    История
@endsection
@section('main')
    @foreach ($histories as $history)
        <div class="history">
            <div class="history">
                <h5 class="card-title">
                    {{ $history->name }}
                </h5>
                <h6 class="card-subtitle mb-2 text-muted">
                    {{ $history->created_at }}
                </h6>
                <p class="card-text">
                    {{ $history->StatusName }}
                </p>
                @if (isset($history->comment))
                    <p class="card-text">
                        Комментарий: <br>
                        {{ $history->comment }}
                    </p>
                @endif
            </div>
        </div>
    @endforeach
@endsection
