<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <h1><?php echo $post->title;?></h1>
    <p><?php echo $post->message;?></p>
    <div class="counter">Unique visitors: <?php echo $totalVisits;?></div>
    <form action="" method="POST">
        <input type="submit" value="Save visits">
    </form>
    <p class="message"><?php echo $message;?></p>
</body>
</html>
