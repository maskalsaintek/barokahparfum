@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag;
@endphp

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Terjadi error:</strong>
        <ul>
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="field">
    <label>Code</label>
    <input type="text" name="code" value="{{ old('code', $fragrance->code ?? '') }}">
    @error('code') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="field">
    <label>Name</label>
    <input type="text" name="name" value="{{ old('name', $fragrance->name ?? '') }}">
    @error('name') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="field">
    <label>Gender</label>
    @php $g = old('gender', $fragrance->gender ?? 'UNISEX'); @endphp
    <select name="gender">
        <option value="MALE" {{ $g==='MALE' ? 'selected' : '' }}>MALE</option>
        <option value="FEMALE" {{ $g==='FEMALE' ? 'selected' : '' }}>FEMALE</option>
        <option value="UNISEX" {{ $g==='UNISEX' ? 'selected' : '' }}>UNISEX</option>
    </select>
    @error('gender') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="field">
    <label>Origin</label>
    <input type="text" name="origin" value="{{ old('origin', $fragrance->origin ?? '') }}">
    @error('origin') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="field">
    <label>Description</label>
    <textarea name="description" rows="3">{{ old('description', $fragrance->description ?? '') }}</textarea>
    @error('description') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="field">
    <label>
        <input type="checkbox" name="is_active"
               {{ old('is_active', ($fragrance->is_active ?? true)) ? 'checked' : '' }}>
        Active
    </label>
</div>