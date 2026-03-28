@extends('layouts.app')

@section('title', 'Total Profit')

@section('content')
    <h1>Total Profit</h1>

    <form method="GET" action="{{ route('reports.total-profit') }}" style="margin-bottom: 20px;">
        <div class="field">
            <label for="date_from">Date From</label>
            <input type="date" name="date_from" id="date_from" value="{{ $dateFrom }}">
        </div>

        <div class="field">
            <label for="date_to">Date To</label>
            <input type="date" name="date_to" id="date_to" value="{{ $dateTo }}">
        </div>

        <button type="submit" class="btn btn-primary">Generate</button>
    </form>

    <div class="card" style="max-width: 500px; padding: 16px; border: 1px solid #ddd; border-radius: 8px;">
        <p>
            Periode:
            <strong>{{ $dateFrom }}</strong>
            s/d
            <strong>{{ $dateTo }}</strong>
        </p>

        <h3>
            Total Profit:
            <span>Rp {{ number_format($totalProfit, 2, ',', '.') }}</span>
        </h3>
    </div>
@endsection