<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Not Found</title>

        <link rel="stylesheet" href="/css/app.css">
        <script>
            (function(){
                window.Laravel = {
                    csrfToken:'{{csrf_token()}}'
                }
            })();
        </script>
    </head>
    <body>
        <h1 class="text-center text-danger">Not Found</h1>
        <img width="500px" height="500px" src="https://webartdevelopers.com/blog/wp-content/uploads/2018/09/404-SVG-Animated-Page-Concept.png" alt="">
    </body>
</html>
