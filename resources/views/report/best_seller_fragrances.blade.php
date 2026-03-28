@extends('layouts.app')

@section('title', 'Best Seller Fragrances')

@section('content')
    <h1>Best Seller Fragrances</h1>

    {{-- FORM FILTER --}}
    <form method="GET" action="{{ route('reports.best-seller-fragrances') }}" style="margin-bottom:20px;">
        <div class="field">
            <label>Date From</label>
            <input type="date" name="date_from" value="{{ $dateFrom }}">
        </div>

        <div class="field">
            <label>Date To</label>
            <input type="date" name="date_to" value="{{ $dateTo }}">
        </div>

        <div class="field">
            <label>Top N</label>
            <input type="number" name="limit" value="{{ $limit }}" min="1" max="100">
        </div>

        <button type="submit" class="btn btn-primary">Generate</button>
    </form>

    <p>
        Periode:
        <strong>{{ $dateFrom }} 00:00:00</strong>
        s/d
        <strong>{{ $dateTo }} 23:59:59</strong>
    </p>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Rank</th>
                <th>Code</th>
                <th>Name</th>
                <th>Total ML Sold</th>
                <th>Total Orders</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $row->fragrance_code }}</td>
                    <td>{{ $row->fragrance_name }}</td>
                    <td>{{ number_format($row->total_ml_sold, 2, ',', '.') }}</td>
                    <td>{{ number_format($row->total_orders, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No data found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection