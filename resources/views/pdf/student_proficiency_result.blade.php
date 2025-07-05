<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Student Proficiency Result</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        h1,
        h2 {
            color: #222;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        th,
        td {
            border: 1px solid #aaa;
            padding: 6px 10px;
            text-align: left;
        }

        th {
            background-color: #f1f1f1;
        }

        .subtitle {
            margin-bottom: 10px;
            font-size: 14px;
        }

        .section-heading {
            margin-top: 2rem;
            font-weight: bold;
            font-size: 13px;
        }
    </style>
</head>

<body>

    <h1>Student Proficiency Result Report ({{ $type }})</h1>

    <p class="subtitle"><strong>Year Level:</strong> {{ $yearLevelName }}</p>
    @if ($sectionName)
        <p class="subtitle"><strong>Section:</strong> {{ $sectionName }}</p>
    @endif

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
        <p class="section-heading">Section Averages</p>
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

    <p class="section-heading">Year Level Average: <strong>{{ $yearLevelAverage }}%</strong></p>

</body>

</html>