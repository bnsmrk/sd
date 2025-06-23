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

        /* Header and Footer Styles */
        .header, .footer { text-align: center; }
        .header img, .footer img { max-width: 150px; height: auto; }
        .footer { position: fixed; bottom: 0; width: 100%; }
    </style>
</head>
<body>

    <!-- Header Logo -->
    <div class="header">
        <img src="asset{{ ('images/header.png') }}" alt="Header Logo">
    </div>

    <h1>Proficiency Report ({{ $type }})</h1>

    @forelse ($resultsByActivity as $activity)
        <h2>{{ $activity['activity_title'] }}</h2>
        <p>Subject ID: {{ $subjectId ?? 'none' }}</p>
        <p>Module ID: {{ $moduleId ?? 'none' }}</p>
        <p>Type: {{ $type }}</p>

        <table>
            <thead>
                <tr>
                    <th>Students</th>
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

    <!-- Footer Logo -->
    <div class="footer">
        <img src="{{ asset('images/header.png') }}" alt="Footer Logo">
    </div>

</body>
</html>
