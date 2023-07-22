<html>
<body>
    <div>
        <?php include 'counter.php'; ?>
        <p>Today's visitors: <?php echo $decodedCountJson[$today] ?></p>
        <p>Yesterday's visitors: <?php echo $decodedCountJson[$yesterday] ?></p>
        <p>With <?php echo $max_visitors ?> visitors, <?php echo $max_day ?> was the day with most traffic</p>
        <p>Total visits: <?php echo $decodedCountJson["total"] ?></p>
    </div>
</body>
</html>
