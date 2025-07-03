<!-- resources/views/emails/testMail.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enrollment Confirmation</title>
</head>
<body>
    <h1>Enrollment Confirmation</h1>

    <p>Student: <strong>{{ $student->name }}</strong></p>
    <p>Year Level: <strong>{{ $yearLevel->name }}</strong></p>
    <p>Section: <strong>{{ $section->name }}</strong></p>

    <h3>Enrolled Subjects:</h3>
    <ul>
        @foreach ($subjects as $subject)
            <li>{{ $subject->name }}</li>
        @endforeach
    </ul>

    <p>Thank you for enrolling!</p>
</body>
</html>
