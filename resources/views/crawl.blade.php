<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Agency Analytics Takehome Assignment by Peter Duke</title>
  </head>
  <body>
  
    <h4>Agency Analytics Takehome Assignment by Peter Duke</h4>
    <h5>Crawled URL: {{ $url }}</h5>

    <p>Number of pages crawled: {{ $count }}</p>
    <p>Number of a unique images: {{ $img }}</p>
    <p>Number of unique internal links: {{ $intLinks }}</p>
    <p>Number of unique external links: {{ $extLinks }}</p>
    <p>Avg page load: {{ $pageLoad }}</p>
    <p>Avg word count: {{ $wordCount }}</p>
    <p>Avg Title length: {{ $titleLength }}</p>

    <table class="table">
        <tr><th>page</th><th>status</th></tr>
        @foreach ($crawled as $page => $status )
        <tr><td>{{ $page }}</td><td>{{ $status }}</td></tr>
        @endforeach
    </table>

  </body>
</html>