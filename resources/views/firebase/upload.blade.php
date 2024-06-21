<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Firebase upload file</title>

    <style>
        img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .aspect-image {
            width: 100%;
            aspect-ratio: 16 / 9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
        }
    </style>
</head>

<body>

    <h1>Upload file</h1>

    {{-- show errors --}}
    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('upload') }}" enctype="multipart/form-data" method="POST">
        @csrf
        <input type="file" name="file" id="file" accept="image/*">
        <button type="submit">Upload</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>File</th>
                <th>Name</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($files as $file)
                <tr>
                    <td style="width: 150px">
                        <div class="aspect-image">
                            <img src="{{ $file['url'] }}" alt="" width="150" height="84">
                        </div>
                    </td>
                    <td>
                        <div>{{ $file['name'] }}</div>
                        <a href="{{ $file['url'] }}" target="_blank">View</a>
                    </td>
                    <td>
                        <a href="{{ route('delete', ['fileName' => $file['fullPath']]) }}">Delete</a>
                        
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
