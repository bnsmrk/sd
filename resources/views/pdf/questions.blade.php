<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Activity Questions</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; line-height: 1.4; }
        h1, h2, h3 { margin-bottom: 0.5em; }
        .question { margin-bottom: 25px; page-break-inside: avoid; }
        ul { margin-top: 5px; padding-left: 20px; }
        hr { margin: 20px 0; }
    </style>
</head>
<body>
    <h2>Activity: {{ $activity->title }}</h2>
    <p><strong>Type:</strong> {{ ucfirst($activity->type) }}</p>
    <p><strong>Scheduled At:</strong> {{ $activity->scheduled_at->format('F j, Y - g:i A') }}</p>
    <hr>

    @foreach ($questions as $index => $question)
        @php
            $options = $question->options ? json_decode($question->options, true) : [];
            $decodedAnswer = json_decode($question->answer_key, true);

            if (is_array($decodedAnswer)) {
                $answerKey = implode(', ', $decodedAnswer);
            } elseif ($question->type === 'true_false') {
                if ($question->answer_key === '1' || strtolower($question->answer_key) === 'true') {
                    $answerKey = 'True';
                } elseif ($question->answer_key === '0' || strtolower($question->answer_key) === 'false') {
                    $answerKey = 'False';
                } else {
                    $answerKey = $question->answer_key ?? 'N/A';
                }
            } else {
                $answerKey = $question->answer_key ?? 'N/A';
            }
        @endphp

        <div class="question">
            <h3>Question #{{ $index + 1 }}</h3>
            <p><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $question->type)) }}</p>
            <p><strong>Question:</strong> {{ $question->question }}</p>

            @if (in_array($question->type, ['multiple_choice', 'checkboxes']) && count($options))
                <p><strong>Choices:</strong></p>
                <ul>
                    @foreach ($options as $opt)
                        <li>{{ $opt }}</li>
                    @endforeach
                </ul>
            @endif

            <p><strong>Answer Key:</strong> {{ $answerKey ?: 'N/A' }}</p>
        </div>
        <hr>
    @endforeach
</body>
</html>
