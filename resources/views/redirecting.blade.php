<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Redirecting ...</title>

    </head>
    
    <script>
        var hash = window.location.hash.substring(1);
        var hostname = window.location.hostname; 
        var link = "http://"+hostname+"/"+hash;
        window.location.replace(link);
    </script>
</html>