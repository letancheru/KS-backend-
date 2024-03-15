<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Message</title>
</head>
<body>
    <h3>New Contact Message From Website</h3>
    <p><strong>Name:</strong> {{ $data->full_name }}</p>
    <p><strong>Email:</strong> {{ $data->email }}</p>
    <p><strong>Phone:</strong> {{ $data->phone }}</p>
    <p><strong>Message:</strong> {{ $data->message }}</p>
</body>
</html>
