<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>CD BLOG</title>

        <!-- Custom fonts for this template-->
        {{-- <link href="/admin/css/all.min.css" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"  rel="stylesheet">
        <link href="/admin/css/sb-admin-2.min.css" rel="stylesheet"> --}}
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
        <div id="app">
            @if (Auth::check())
                <main-app :user="{{Auth::user()}}" :permission="{{Auth::user()->role->permission}}"></main-app>
            @else
                <main-app :user="false"></main-app>
            @endif
        </div>

    <script src="{{mix('/js/app.js')}}"></script>
    </body>
</html>
