<!doctype html>
<html lang="{{ App::getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="Fresns" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fresns Placeholder</title>
    <link rel="stylesheet" href="/static/css/bootstrap.min.css">
</head>

<body style="background-color:#ccc">
    <table class="table">
        <thead>
            <tr>
                <th scope="col">Key</th>
                <th scope="col">Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($params as $key=>$value)
            <tr>
                <th scope="row">{!! $key !!}</th>
                <td>{!! $value !!}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
