@extends('layouts.app')

@section('title', 'Fragrances')

@section('content')
    <h1>Fragrances</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('fragrances.index') }}" class="mb-3">
        <input type="text" name="q" placeholder="Search code/name/origin" value="{{ request('q') }}">

        <select name="gender">
            <option value="">-- gender --</option>
            <option value="MALE" {{ request('gender')==='MALE' ? 'selected' : '' }}>MALE</option>
            <option value="FEMALE" {{ request('gender')==='FEMALE' ? 'selected' : '' }}>FEMALE</option>
            <option value="UNISEX" {{ request('gender')==='UNISEX' ? 'selected' : '' }}>UNISEX</option>
        </select>

        <select name="is_active">
            <option value="">-- active --</option>
            <option value="1" {{ request('is_active')==='1' ? 'selected' : '' }}>Active</option>
            <option value="0" {{ request('is_active')==='0' ? 'selected' : '' }}>Inactive</option>
        </select>

        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
        <a href="{{ route('fragrances.index') }}" class="btn btn-sm btn-secondary">Reset</a>
    </form>

    <div class="mb-3">
        <a href="{{ route('fragrances.create') }}" class="btn btn-primary">+ New Fragrance</a>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Name</th>
                <th>Gender</th>
                <th>Origin</th>
                <th>Active</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($fragrances as $fr)
                <tr>
                    <td>{{ $loop->iteration + ($fragrances->currentPage()-1)*$fragrances->perPage() }}</td>
                    <td>{{ $fr->code }}</td>
                    <td>{{ $fr->name }}</td>
                    <td>{{ $fr->gender }}</td>
                    <td>{{ $fr->origin ?? '-' }}</td>
                    <td>{{ $fr->is_active ? 'YES' : 'NO' }}</td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('fragrances.edit', $fr) }}">Edit</a>

                        <form action="{{ route('fragrances.destroy', $fr) }}" method="POST"
                              style="display:inline-block"
                              onsubmit="return confirm('Delete this fragrance?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">No data.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $fragrances->links() }}
@endsection