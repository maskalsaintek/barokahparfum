@extends('layouts.app')

@section('title', 'Variant Types')

@section('content')
    <h1>Variant Types</h1>

    <a href="{{ route('variant-types.create') }}" class="btn btn-primary">+ New Variant Type</a>

    @if($variantTypes->isEmpty())
        <p style="margin-top: 16px;">Belum ada data variant type.</p>
    @else
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Code</th>
                <th>Name</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($variantTypes as $vt)
                <tr>
                    <td>{{ $vt->id }}</td>
                    <td>{{ $vt->code }}</td>
                    <td>{{ $vt->name }}</td>
                    <td>
                        <a href="{{ route('variant-types.edit', $vt) }}" class="btn btn-secondary">Edit</a>

                        <form action="{{ route('variant-types.destroy', $vt) }}"
                              method="POST"
                              style="display:inline-block"
                              onsubmit="return confirm('Yakin hapus variant type ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@endsection
