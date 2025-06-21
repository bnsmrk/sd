<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Proficiency Report ({{ $type }})</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 4px; text-align: left; }
        h2 { margin-top: 20px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <h1>Proficiency Report ({{ $type }})</h1>

    @forelse ($resultsByActivity as $activity)
        <h2>{{ $activity['activity_title'] }}</h2>
        <p>Subject ID: {{ $subjectId ?? 'none' }}</p>
<p>Module ID: {{ $moduleId ?? 'none' }}</p>
<p>Type: {{ $type }}</p>

        <table>
            <thead>
                <tr>
                    <th>Studentss</th>
                    <th>Score</th>
                    <th>Total</th>
                    <th>Average (%)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($activity['entries'] as $entry)
                    <tr>
                        <td>{{ $entry['student'] }}</td>
                        <td>{{ $entry['score'] }}</td>
                        <td>{{ $entry['total'] }}</td>
                        <td>{{ $entry['average'] }}%</td>
                    </tr>
                @empty
                    <tr><td colspan="4">No results found for this activity.</td></tr>
                @endforelse
            </tbody>
        </table>
    @empty
        <p>No activities or results found.</p>
    @endforelse
</body>
</html>
