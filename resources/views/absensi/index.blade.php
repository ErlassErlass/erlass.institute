<!DOCTYPE html>
<html>
<head>
    <title>Attendance List</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Attendance Records</h1>
    
    <table>
        <thead>
            <tr>
                <th>Report ID</th>
                <th>Instructor</th>
                <th>Attendance Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($absensi as $record)
                <tr>
                    <td>{{ $record->id }}</td>
                    <td>{{ $record->laporanMengajar->instruktur->nama_lengkap }}</td>
                    <td>{{ $record->hadir ? 'Present' : 'Absent' }}</td>
                    <td>
                        <a href="{{ route('absensi.edit', $record) }}">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>