<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title> Test Questions</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #111;
            line-height: 1.6;
        }

        h1, h2, h3 {
            color: #01006c;
            margin-bottom: 0.5em;
        }

        .meta p {
            margin: 2px 0;
        }

        .question {
            margin-bottom: 20px;
            padding: 10px 12px;
            border: 1px solid #999;
            border-radius: 6px;
            background-color: #f9f9f9;
            page-break-inside: avoid;
        }

        .question h3 {
            margin-top: 0;
            margin-bottom: 8px;
        }

        ul {
            padding-left: 20px;
            margin: 5px 0;
        }

        .answer {
            margin-top: 5px;
            font-weight: bold;
        }

        hr {
            margin: 25px 0;
            border: 0;
            border-top: 1px solid #ccc;
        }
    </style>
</head>
<body>
    <h2>Activitu Test Questions: {{ $activity->title }}</h2>

    <div class="meta">
        <p><strong>Type:</strong> {{ ucfirst($activity->type) }}</p>
        <p><strong>Scheduled At:</strong> {{ \Carbon\Carbon::parse($activity->scheduled_at)->format('F j, Y - g:i A') }}</p>
        @if (!empty($activity->yearLevel?->name))
            <p><strong>Year Level:</strong> {{ $activity->yearLevel->name }}</p>
        @endif
    </div>

    <hr>

    @foreach ($questions as $index => $question)
        @php
            $options = $question->options ? json_decode($question->options, true) : [];
            $decodedAnswer = json_decode($question->answer_key, true);

            if (is_array($decodedAnswer)) {
                $answerKey = implode(', ', $decodedAnswer);
            } elseif ($question->type === 'true_false') {
                $answerKey = match(strtolower($question->answer_key)) {
                    '1', 'true' => 'True',
                    '0', 'false' => 'False',
                    default => $question->answer_key ?? 'N/A',
                };
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

            <p class="answer"><strong>Answer Key:</strong> {{ $answerKey ?: 'N/A' }}</p>
        </div>
    @endforeach
</body>
</html>
