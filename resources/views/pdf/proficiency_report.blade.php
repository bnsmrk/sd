<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Proficiency Report</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            margin: 40px;
            color: #333;
        }

        .header {
            display: flex;
            align-items: center;
            border-bottom: 2px solid #ccc;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }

        .logo {
            width: 70px;
            height: 70px;
            object-fit: contain;
            margin-right: 20px;
        }

        .header-text {
            line-height: 1.4;
        }

        .header-text h1 {
            margin: 0;
            font-size: 22px;
            color: #2c3e50;
        }

        .header-text p {
            margin: 2px 0 0;
            font-size: 16px;
            color: #4b5563;
        }

        h2 {
            font-size: 18px;
            margin-top: 30px;
            color: #1e3a8a;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px 10px;
            text-align: left;
        }

        th {
            background-color: #f3f4f6;
        }

        .no-data {
            margin-top: 40px;
            text-align: center;
            font-style: italic;
            color: #888;
        }
    </style>
</head>

<body>


    <div class="header">
        <img src="{{ public_path('images/lmslogoo.png') }}" alt="School Logo" class="logo">
        <div class="header-text">
            <h1>Sample School Name</h1>
            <p>Students Activities Report ({{ ucfirst($type) }})</p>
        </div>
    </div>



    @forelse ($resultsByActivity as $group)
        <h2>{{ $group['activity_title'] }}</h2>
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Score</th>
                    <th>Total</th>
                    <th>Average (%)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($group['entries'] as $entry)
                    <tr>
                        <td>{{ $entry['student'] }}</td>
                        <td>{{ $entry['score'] }}</td>
                        <td>{{ $entry['total'] }}</td>
                        <td>{{ $entry['average'] }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @empty
        <p class="no-data">No results to display.</p>
    @endforelse

</body>

</html>
