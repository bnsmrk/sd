<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Student Proficiency Result Report</title>
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            font-size: 13px;
            margin: 30px;
            color: #333;
        }

        h1 {
            font-size: 20px;
            color: #1a237e;
            margin-bottom: 10px;
        }

        .subtitle,
        .section-heading {
            font-size: 14px;
            margin: 6px 0;
        }

        .section-heading {
            margin-top: 30px;
            font-weight: bold;
            border-bottom: 1px solid #ccc;
            padding-bottom: 4px;
            color: #37474f;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #bbb;
            padding: 8px 10px;
            text-align: left;
        }

        th {
            background-color: #f1f1f1;
            color: #111;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .footer-summary {
            margin-top: 20px;
            font-weight: bold;
            font-size: 14px;
            color: #2e7d32;
        }
    </style>
</head>

<body>

    <h1>📊 Student Proficiency Result Report ({{ ucfirst($type) }})</h1>

    <p class="subtitle"><strong>Year Level:</strong> {{ $yearLevelName }}</p>
    @if ($sectionName)
        <p class="subtitle"><strong>Section:</strong> {{ $sectionName }}</p>
    @endif

    <div class="section-heading">Individual Scores</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Student</th>
                <th>Section</th>
                <th>Average Score (%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($individuals as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['student']['name'] }}</td>
                    <td>{{ $item['student']['section']['name'] ?? 'N/A' }}</td>
                    <td>{{ $item['average'] }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if (count($sectionsAvg))
        <div class="section-heading">Section Averages</div>
        <table>
            <thead>
                <tr>
                    <th>Section</th>
                    <th>Average Score (%)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sectionsAvg as $section)
                    <tr>
                        <td>{{ $section['section']['name'] ?? 'N/A' }}</td>
                        <td>{{ $section['average'] }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <p class="footer-summary">📘 Year Level Average: <strong>{{ $yearLevelAverage }}%</strong></p>

</body>

</html>