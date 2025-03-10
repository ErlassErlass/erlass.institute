<!DOCTYPE html>
<html>
<head>
    <title>Sekolah List</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Sekolah List</h1>
    
    <a href="{{ route('sekolah.create') }}" style="display: inline-block; padding: 8px 12px; background: #4CAF50; color: white; text-decoration: none; border-radius: 4px;">Tambah Sekolah</a>

    <table>
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
                        <a href="{{ route('sekolah.edit', $item) }}" style="color: blue; margin-right: 10px;">Edit</a>
                        <form action="{{ route('sekolah.destroy', $item) }}" method="POST" style="display: inline;">
                            @csrf @method('DELETE')
                            <button type="submit" style="background: none; border: none; color: red; cursor: pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>