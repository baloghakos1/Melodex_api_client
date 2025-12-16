<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Artists PDF</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
        img { max-width: 50px; max-height: 50px; }
    </style>
</head>
<body>
    <h2>Artists Table</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Nationality</th>
                <th>Image</th>
                <th>Description</th>
                <th>Is Band</th>
            </tr>
        </thead>
        <tbody>
            @foreach($artists as $artist)
                <tr>
                    <td>{{ $artist->id }}</td>
                    <td>{{ $artist->name }}</td>
                    <td>{{ $artist->nationality }}</td>
                    <td>
                        @if($artist->image)
                            <img src="{{ $artist->image }}" alt="{{ $artist->name }}">
                        @endif
                    </td>
                    <td>{{ \Illuminate\Support\Str::limit($artist->description, 100) }}</td>
                    <td>{{ $artist->is_band ? 'Yes' : 'No' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
