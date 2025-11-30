@extends('layouts.app')

@section('title', 'Create Variant Type')

@section('content')
    <h1>Create Variant Type</h1>

    <form action="{{ route('variant-types.store') }}" method="POST">
        @csrf

        <div class="field">
            <label for="code">Code</label>
            <input type="text" name="code" id="code" value="{{ old('code') }}" placeholder="contoh: SINGLE, DOUBLE">
            @error('code')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="field">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="contoh: Single Mix, Double Mix">
            @error('name')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('variant-types.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
@endsection
