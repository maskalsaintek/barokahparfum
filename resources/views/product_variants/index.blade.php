@extends('layouts.app')

@section('title', 'Product Variants')

@section('content')
    <h1>Product Variants</h1>

    <a href="{{ route('product-variants.create') }}" class="btn btn-primary">+ New Product Variant</a>

    {{-- Filter sederhana --}}
    <form method="GET" action="{{ route('product-variants.index') }}" style="margin-top:16px; margin-bottom:16px;">
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <div>
                <label for="filter_fragrance">Fragrance</label><br>
                <select name="fragrance_id" id="filter_fragrance">
                    <option value="">-- All --</option>
                    @foreach($fragrances as $fr)
                        <option value="{{ $fr->id }}"
                            {{ request('fragrance_id') == $fr->id ? 'selected' : '' }}>
                            {{ $fr->code }} - {{ $fr->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="filter_variant_type">Variant Type</label><br>
                <select name="variant_type_id" id="filter_variant_type">
                    <option value="">-- All --</option>
                    @foreach($variantTypes as $vt)
                        <option value="{{ $vt->id }}"
                            {{ request('variant_type_id') == $vt->id ? 'selected' : '' }}>
                            {{ $vt->code }} - {{ $vt->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="filter_active">Status</label><br>
                <select name="is_active" id="filter_active">
                    <option value="">All</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div style="align-self:flex-end;">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>

    @if($variants->isEmpty())
        <p>Belum ada product variant.</p>
    @else
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Fragrance</th>
                <th>Variant Type</th>
                <th>Size (ml)</th>
                <th>Base Price</th>
                <th>Cost Price / ML</th>
                <th>Mix Ratio</th>
                <th>Active</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($variants as $v)
                <tr>
                    <td>{{ $v->id }}</td>
                    <td>
                        @if($v->fragrance)
                            {{ $v->fragrance->code }} - {{ $v->fragrance->name }}
                        @else
                            <em>n/a</em>
                        @endif
                    </td>
                    <td>
                        @if($v->variantType)
                            {{ $v->variantType->code }} - {{ $v->variantType->name }}
                        @else
                            <em>n/a</em>
                        @endif
                    </td>
                    <td>{{ number_format($v->bottle_size_ml, 2) }}</td>
                    <td>{{ number_format($v->base_price, 0) }}</td>
                    <td>{{ number_format($v->cost_ml, 0) }}</td>
                    <td>{{ $v->mix_ratio ?? '-' }}</td>
                    <td>{{ $v->is_active ? 'Yes' : 'No' }}</td>
                    <td>
                        <a href="{{ route('product-variants.edit', $v) }}" class="btn btn-secondary">Edit</a>
                        <form action="{{ route('product-variants.destroy', $v) }}"
                              method="POST"
                              style="display:inline-block"
                              onsubmit="return confirm('Yakin hapus product variant ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Del</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@endsection
