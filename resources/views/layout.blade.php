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
    
    <form method="POST">
        @csrf
        <input type="submit" value="crawl">
    </form>

    @yield('content', '')

  </body>
</html>