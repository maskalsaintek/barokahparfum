@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag;
@endphp

@extends('layouts.app')

@section('title', 'Edit Product Variant')

@section('content')
    <h1>Edit Product Variant</h1>

    <form action="{{ route('product-variants.update', $variant) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- FRAGRANCE + search --}}
        <div class="field">
            <label for="fragranceSearch">Fragrance (search)</label>
            <input type="text" id="fragranceSearch" placeholder="Cari fragrance...">
            <br>
            <select name="fragrance_id" id="fragranceSelect" size="6" style="width:100%; margin-top:4px;">
                @foreach($fragrances as $fr)
                    <option value="{{ $fr->id }}"
                        {{ old('fragrance_id', $variant->fragrance_id) == $fr->id ? 'selected' : '' }}>
                        {{ $fr->code }} - {{ $fr->name }}
                    </option>
                @endforeach
            </select>
            @error('fragrance_id')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        {{-- VARIANT TYPE + search --}}
        <div class="field">
            <label for="variantTypeSearch">Variant Type (search)</label>
            <input type="text" id="variantTypeSearch" placeholder="Cari variant type...">
            <br>
            <select name="variant_type_id" id="variantTypeSelect" size="4" style="width:100%; margin-top:4px;">
                @foreach($variantTypes as $vt)
                    <option value="{{ $vt->id }}"
                        {{ old('variant_type_id', $variant->variant_type_id) == $vt->id ? 'selected' : '' }}>
                        {{ $vt->code }} - {{ $vt->name }}
                    </option>
                @endforeach
            </select>
            @error('variant_type_id')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="field">
            <label for="bottle_size_ml">Bottle Size (ml)</label>
            <input type="text" name="bottle_size_ml" id="bottle_size_ml"
                   value="{{ old('bottle_size_ml', $variant->bottle_size_ml) }}">
            @error('bottle_size_ml')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="field">
            <label for="base_price">Base Price</label>
            <input type="text" name="base_price" id="base_price"
                   value="{{ old('base_price', $variant->base_price) }}">
            @error('base_price')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="field">
            <label for="cost_ml">Cost Price / ML</label>
            <input type="text" name="cost_ml" id="cost_ml"
                   value="{{ old('cost_ml', $variant->cost_ml) }}">
            @error('cost_ml')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="field">
            <label for="mix_ratio">Mix Ratio</label>
            <input type="text" name="mix_ratio" id="mix_ratio"
                   value="{{ old('mix_ratio', $variant->mix_ratio) }}">
            @error('mix_ratio')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="field">
            <label>
                <input type="checkbox" name="is_active"
                    {{ old('is_active', $variant->is_active) ? 'checked' : '' }}>
                Active
            </label>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('product-variants.index') }}" class="btn btn-secondary">Back</a>
    </form>

    <script>
        function setupSearch(inputId, selectId) {
            const input = document.getElementById(inputId);
            const select = document.getElementById(selectId);
            if (!input || !select) return;

            input.addEventListener('input', function () {
                const filter = this.value.toLowerCase();
                Array.from(select.options).forEach(function (opt) {
                    const text = opt.text.toLowerCase();
                    opt.hidden = filter && !text.includes(filter);
                });
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            setupSearch('fragranceSearch', 'fragranceSelect');
            setupSearch('variantTypeSearch', 'variantTypeSelect');
        });
    </script>
@endsection
