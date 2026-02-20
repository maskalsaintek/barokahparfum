@extends('layouts.app')

@section('title', 'Edit Fragrance')

@section('content')
    <h1>Edit Fragrance</h1>

    <form action="{{ route('fragrances.update', $fragrance) }}" method="POST">
        @csrf
        @method('PUT')

        @include('fragrances._form', ['fragrance' => $fragrance])

        <button class="btn btn-primary" type="submit">Update</button>
        <a class="btn btn-secondary" href="{{ route('fragrances.index') }}">Back</a>
    </form>
@endsection