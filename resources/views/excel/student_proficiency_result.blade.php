<table>
    <thead>
        <tr>
            <th>Student</th>
            <th>Section</th>
            <th>Average (%)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($individuals as $i)
            <tr>
                <td>{{ $i['student']['name'] }}</td>
                <td>{{ $i['student']['section']['name'] ?? 'N/A' }}</td>
                <td>{{ $i['average'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>