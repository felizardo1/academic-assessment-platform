<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #1a1a1a;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #2E75B6;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 18px;
            color: #2E75B6;
            margin: 0 0 5px 0;
        }

        .header p {
            margin: 2px 0;
            font-size: 11px;
            color: #555;
        }

        .badge {
            background-color: #e74c3c;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #2E75B6;
            border-bottom: 1px solid #2E75B6;
            padding-bottom: 4px;
            margin-bottom: 10px;
        }

        .question {
            margin-bottom: 16px;
            padding: 10px;
            background-color: #f9f9f9;
            border-left: 3px solid #2E75B6;
        }

        .question-text {
            font-weight: bold;
            margin-bottom: 8px;
        }

        .question-points {
            font-size: 10px;
            color: #888;
            float: right;
        }

        .options {
            margin-left: 20px;
            margin-bottom: 8px;
        }

        .option {
            margin-bottom: 4px;
        }

        .option.correct {
            color: #27ae60;
            font-weight: bold;
        }

        .answer-box {
            background-color: #eaf4ea;
            border: 1px solid #27ae60;
            padding: 8px;
            margin-top: 8px;
            border-radius: 4px;
        }

        .answer-label {
            font-weight: bold;
            color: #27ae60;
            font-size: 11px;
        }

        .criteria-box {
            background-color: #fff8e1;
            border: 1px solid #f39c12;
            padding: 8px;
            margin-top: 6px;
            border-radius: 4px;
        }

        .criteria-label {
            font-weight: bold;
            color: #f39c12;
            font-size: 11px;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            color: #888;
            border-top: 1px solid #ccc;
            padding-top: 8px;
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>{{ $exam->institution }}</h1>
        <p><strong>{{ $exam->title }}</strong> — <span class="badge">CORRECTION GUIDE</span></p>
        <p>Discipline: {{ $exam->subject }} | Data: {{ \Carbon\Carbon::parse($exam->date)->format('d/m/Y') }} |
            Duration:
            {{ $exam->duration }} Minutes</p>
        <p>Total: {{ $exam->total_points }} points</p>
    </div>

    @foreach ($exam->sections as $index => $section)
        <div class="section">
            @if ($section->name)
                <div class="section-title">
                    Session {{ $index + 1 }} — {{ $section->name }}
                </div>
            @else
                <div class="section-title">
                    Session {{ $index + 1 }}
                </div>
            @endif

            @foreach ($section->questions as $qIndex => $question)
                <div class="question">
                    <div class="question-text">
                        <span class="question-points">{{ $question->points }} pts</span>
                        {{ $qIndex + 1 }}. {{ $question->content }}
                    </div>

                    @if ($question->type === 'multiple_choice')
                        <div class="options">
                            @foreach ($question->options as $oIndex => $option)
                                <div class="option {{ $option->is_correct ? 'correct' : '' }}">
                                    {{ chr(65 + $oIndex) }}) {{ $option->option_text }}
                                    @if ($option->is_correct)
                                        ✓
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if ($question->answerKey)
                        <div class="answer-box">
                            <div class="answer-label">EXPECTED ANSWER:</div>
                            {{ $question->answerKey->expected_answer }}
                        </div>

                        @if ($question->answerKey->correction_criteria)
                            <div class="criteria-box">
                                <div class="criteria-label">EVALUATION CRITERIA:</div>
                                {{ $question->answerKey->correction_criteria }}
                            </div>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>
    @endforeach

    <div class="footer">
        {{ $exam->institution }} — {{ $exam->title }} — CORRECTION GUIDE —
        {{ \Carbon\Carbon::parse($exam->date)->format('d/m/Y') }}
    </div>

</body>

</html>
