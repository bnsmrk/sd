<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Enrollment Confirmation</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f4f6f8; padding: 20px; color: #333;">
    <div
        style="max-width: 600px; margin: auto; background: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); overflow: hidden;">
        <div style="background-color: #003366; padding: 20px;">
            <h1 style="color: #ffffff; margin: 0; font-size: 24px;">📘 Enrollment Confirmation</h1>
        </div>

        <div style="padding: 20px;">
            <p style="font-size: 16px; margin-bottom: 8px;">Hello <strong>{{ $student->name }}</strong>,</p>

            <p style="margin-bottom: 16px;">
                We are pleased to confirm your enrollment with the following details:
            </p>

            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <tr>
                    <td style="padding: 8px 0;"><strong>👨‍🎓 Student:</strong></td>
                    <td style="padding: 8px 0;">{{ $student->name }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>📚 Year Level:</strong></td>
                    <td style="padding: 8px 0;">{{ $yearLevel->name }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>🏫 Section:</strong></td>
                    <td style="padding: 8px 0;">{{ $section->name }}</td>
                </tr>
            </table>

            <h3 style="font-size: 18px; margin-bottom: 10px;">📝 Enrolled Subjects</h3>
            <ul style="padding-left: 20px; margin-bottom: 20px;">
                @foreach ($subjects as $subject)
                    <li style="margin-bottom: 4px;">📖 {{ $subject->name }}</li>
                @endforeach
            </ul>

            <p style="margin-top: 20px;">Thank you for enrolling! We wish you a successful academic journey. 🎉</p>
        </div>

        <div style="background-color: #f1f1f1; text-align: center; padding: 10px; font-size: 12px; color: #777;">
            &copy; {{ date('Y') }} Your School Name. All rights reserved.
        </div>
    </div>
</body>

</html>