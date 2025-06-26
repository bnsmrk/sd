<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Proficiency Report</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f0f0f0; }
        h2 { margin-top: 20px; }
    </style>
</head>
<body>
    <h1>Proficiency Report ({{ $type }})</h1>

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
        <p>No results to display.</p>
    @endforelse
</body>
</html>
