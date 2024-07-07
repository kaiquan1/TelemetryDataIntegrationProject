<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Water Meter Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="user-info">
                <span class="user-name">COSMOS</span>
            </div>
        </header>
        <main>
            <div class="dashboard">
                <?php include('status_chart.php'); ?>
                <?php include('water_consumption.php'); ?>
                <?php include('alarm_message.php'); ?>
            </div>
            <div class="device-table">
                <?php include('device_table.php'); ?>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>
