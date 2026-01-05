<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin-top: 30px;
            margin-bottom: 20px;
        }

        header {
            position: fixed;
            top: -40px;
            left: 0;
            right: 0;
            height: 50px;
            text-align: center;
            border-bottom: 1px solid #000;
        }

        footer {
            position: fixed;
            bottom: -40px;
            left: 0;
            right: 0;
            height: 30px;
            text-align: center;
            font-size: 10px;
            border-top: 1px solid #000;
        }

        .page-number:before {
            content: counter(page);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .header-table,
        .header-table th,
        .header-table td {
            border: none !important;
        }
    </style>
</head>
<body>

<header>
    <table class="header-table" width="100%">
        <tr>
            <td style="text-align: left; vertical-align: middle; font-size: 12px;">
                {{ date('Y-m-d') }}
            </td>

            <td style="text-align: right; vertical-align: middle;">
                <img
                    src="file://{{ public_path('image/angled_view.png') }}"
                    style="height:40px;"
                >
            </td>
        </tr>
    </table>
</header>

<footer>
    Page <span class="page-number"></span>
</footer>
<h1>Songs</h1>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Lyrics</th>
            <th>Songwriter</th>
            <th>Album</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($songs as $song)
            <tr>
                <td>{{ $song->id }}</td>
                <td>{{ $song->name }}</td>
                <td>{{ $song->lyrics }}</td>
                <td>{{ $song->songwriter}}</td>
                <td>{{ $song->album_name }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
