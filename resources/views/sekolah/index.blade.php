<!-- resources/views/sekolah/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="card">
        <h1>Sekolah List</h1>

        <!-- Success Message (if any) -->
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Search/Filter Section (Optional) -->
        <form method="GET" action="{{ route('sekolah.index') }}">
            <div class="input-group mb-3">
                <input type="text" name="search" class="form-control" placeholder="Search by school name...">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>

        <!-- Add New School Button -->
        <a href="{{ route('sekolah.create') }}" class="btn btn-success mb-3">Tambah Sekolah</a>

        <!-- Table with Pagination -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Kode Sekolah</th>
                    <th>Nama Sekolah</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sekolah as $item)
                    <tr>
                        <td>{{ $item->kodlan }}</td>
                        <td>{{ $item->namasekolah }}</td>
                        <td>
                            <a href="{{ route('sekolah.edit', $item) }}" class="btn btn-sm btn-primary">Edit</a>
                            <form action="{{ route('sekolah.destroy', $item) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger" onclick="confirm('Are you sure?') ? this.parentElement.submit() : null">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination Links -->
        {{ $sekolah->links() }}

    </div>
@endsection