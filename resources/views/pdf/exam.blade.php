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
            margin-bottom: 14px;
        }

        .question-text {
            font-weight: bold;
            margin-bottom: 6px;
        }

        .question-points {
            font-size: 10px;
            color: #888;
            float: right;
        }

        .options {
            margin-left: 20px;
        }

        .option {
            margin-bottom: 4px;
        }

        .answer-space {
            border-bottom: 1px solid #ccc;
            margin-top: 6px;
            height: 40px;
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
        <p><strong>{{ $exam->title }}</strong></p>
        <p>Discipline: {{ $exam->subject }} | Date: {{ \Carbon\Carbon::parse($exam->date)->format('d/m/Y') }} |
            Duration:
            {{ $exam->duration }} minutos</p>
        <p>Total Points: {{ $exam->total_points }} points</p>
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
                        <p class="font-thin">{{ $qIndex + 1 }}. {{ $question->content }}
                    </div>

                    @if ($question->type === 'multiple_choice')
                        <div class="options">
                            @foreach ($question->options as $oIndex => $option)
                                <div class="option">
                                    {{ chr(65 + $oIndex) }}) {{ $option->option_text }}
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="answer-space"></div>
                    @endif
                </div>
            @endforeach
        </div>
    @endforeach

    <div class="footer">
        {{ $exam->institution }} — {{ $exam->title }} — {{ \Carbon\Carbon::parse($exam->date)->format('d/m/Y') }}
    </div>

</body>

</html>
