@extends('layouts.app')

@section('title', 'Create Fragrance')

@section('content')
    <h1>Create Fragrance</h1>

    <form action="{{ route('fragrances.store') }}" method="POST">
        @csrf

        @include('fragrances._form', ['fragrance' => null])

        <button class="btn btn-primary" type="submit">Save</button>
        <a class="btn btn-secondary" href="{{ route('fragrances.index') }}">Cancel</a>
    </form>
@endsection