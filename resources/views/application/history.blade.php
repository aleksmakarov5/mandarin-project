@extends('header_menu')
@section('title')
    История
@endsection
@section('main')
    @foreach ($histories as $history)
        <div class="alert" role="alert" style="width: 100%; background: rgb(116, 116, 255);">
            <div class="card-body">
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
