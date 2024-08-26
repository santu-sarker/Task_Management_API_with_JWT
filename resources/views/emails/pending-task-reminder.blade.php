<!DOCTYPE html>
<html>

<head>
    <title>Pending Task Reminder</title>
</head>

<body>
    <h1>Hello, {{ $user->name ?? "no name" }}</h1>
    <p>You have the following tasks pending that are due in less than 1 day:</p>

    <ul>
        @foreach ($tasks as $task)
        <li>{{ $task->title }} - Due: {{ $task->due_date->format('d-m-Y H:i') }}</li>
        @endforeach
    </ul>

    <p>Please complete them as soon as possible.</p>

    <p>Thank you!</p>
</body>

</html>
