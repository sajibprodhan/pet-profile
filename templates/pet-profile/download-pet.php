

<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Pet QR-Code</title>
    </head>
    <body>
        <?php 
            echo $output->output( $qrCode, 64, 'white', 'black' ); 
        ?>
    </body>
</html>
