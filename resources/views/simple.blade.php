<!DOCTYPE html>
<html>
<body>
    <h1>SUCCESS! Instructor Dashboard</h1>
    <p>Course: {{ $course->title }}</p>
    <p>Students: {{ $students->count() }}</p>
</body>
</html>