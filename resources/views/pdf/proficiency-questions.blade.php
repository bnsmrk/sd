<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Proficiency Test Questions</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
            line-height: 1.5;
        }
        h1, h2 {
            color: #01006c;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .meta {
            margin-bottom: 20px;
        }
        .meta strong {
            color: #ff69b4;
        }
        .question {
            margin-bottom: 18px;
            padding: 10px;
            border: 1px solid #aaa;
            border-radius: 6px;
        }
        .question-number {
            font-weight: bold;
            color: #01006c;
        }
        .question-type {
            font-style: italic;
            color: #666;
        }
        .question-text {
            margin: 5px 0;
        }
        .choices {
            margin-left: 15px;
        }
        .answer {
            margin-top: 5px;
            color: #444;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Proficiency Test Questions</h1>
    </div>

    <div class="meta">
        <p><strong>Activity:</strong> {{ $activity->title }}</p>
        <p><strong>Type:</strong> {{ ucfirst($activity->type) }}</p>
        <p><strong>Scheduled At:</strong> {{ \Carbon\Carbon::parse($activity->scheduled_at)->format('F d, Y - h:i A') }}</p>
        <p><strong>Year Level:</strong> {{ $activity->yearLevel->name ?? 'N/A' }}</p>
    </div>

    @foreach($questions as $index => $question)
        <div class="question">
            <div class="question-number">Question #{{ $index + 1 }}</div>
            <div class="question-type">Type: {{ ucfirst(str_replace('_', ' ', $question->type)) }}</div>
            <div class="question-text">{{ $question->question }}</div>

            @if(in_array($question->type, ['multiple_choice', 'checkboxes']) && $question->options)
                <div class="choices">
                    <strong>Choices:</strong>
                    <ul>
                        @foreach(json_decode($question->options) as $opt)
                            <li>{{ $opt }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($question->answer_key)
                <div class="answer">
                    <strong>Answer Key:</strong>
                    @if($question->type === 'checkboxes')
                        {{ implode(', ', json_decode($question->answer_key)) }}
                    @elseif($question->type === 'true_false')
                        {{ $question->answer_key === 'true' ? 'True' : 'False' }}
                    @else
                        {{ $question->answer_key }}
                    @endif
                </div>
            @else
                <div class="answer"><strong>Answer Key:</strong> N/A</div>
            @endif
        </div>
    @endforeach
</body>
</html>
