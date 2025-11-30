@extends('layouts.app')

@section('title', 'Edit Variant Type')

@section('content')
    <h1>Edit Variant Type</h1>

    <form action="{{ route('variant-types.update', $variantType) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="field">
            <label for="code">Code</label>
            <input type="text" name="code" id="code"
                   value="{{ old('code', $variantType->code) }}">
            @error('code')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="field">
            <label for="name">Name</label>
            <input type="text" name="name" id="name"
                   value="{{ old('name', $variantType->name) }}">
            @error('name')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('variant-types.index') }}" class="btn btn-secondary">Back</a>
    </form>
@endsection
