<!DOCTYPE html>
<html>
<head>
    <title>New Task Assigned</title>
</head>
<body>
    <h1>You have been assigned a new task</h1>
    <p><strong>Task Title:</strong> {{ $taskTitle }}</p>
    <p><strong>Start Time:</strong> {{ \Carbon\Carbon::parse($startTime)->format('Y-m-d h:i A') }}</p>
    <p><strong>End Time:</strong> {{ \Carbon\Carbon::parse($endTime)->format('Y-m-d h:i A') }}</p>
    <p><strong>Description:</strong> {!! $description !!}</p>
</body>
</html>
