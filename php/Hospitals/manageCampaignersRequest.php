<?php
session_start();
require '../db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="icon" href="../../Images/logo.png" type="image/png">
    <title>MedAlert</title>
</head>

<body>
    <header class="navbar-container">
        <div class="logo">
            <img src="../../Images/logo.png" alt="MedAlert Logo" class="logo-img">
            <span class="logo-name">MedAlert - Your Healthcare Partner</span>
        </div>
    </header>

    <div class="container">
        <h2 class="my-4 text-center">Manage Campaign Events</h2>
        <div class="row">
            <?php
            $sql = "SELECT DISTINCT e.eventid, e.name AS eventName, e.banner, e.description, e.campaignersId, e.date, e.time, e.location, e.hospitalId, e.status, e.shared,
           q.eventId AS qrEnv,
           h.hospitalName, c.name AS campaignerName, c.email AS campaignerEmail
    FROM events e
    LEFT JOIN qrattendance q ON e.eventid = q.eventId
    JOIN hospitals h ON e.hospitalId = h.hospitalId
    JOIN campaigners c ON e.campaignersId = c.campaignersId
    ORDER BY e.status";


            $result = $db->query($sql);
            if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <img src="../../camp_post/<?php echo htmlspecialchars($row['banner']); ?>" class="card-img-top" alt="Event Banner">
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><strong>Event Name:</strong> <?php echo htmlspecialchars($row['eventName']); ?></li>
                                    <li class="list-group-item"><strong>Date:</strong> <?php echo htmlspecialchars($row['date']); ?></li>
                                    <li class="list-group-item"><strong>Time:</strong> <?php echo htmlspecialchars($row['time']); ?></li>
                                    <li class="list-group-item"><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></li>
                                    <li class="list-group-item"><strong>Hospital:</strong> <?php echo htmlspecialchars($row['hospitalName']); ?></li>
                                    <li class="list-group-item"><strong>Campaigner:</strong> <?php echo htmlspecialchars($row['campaignerName']); ?></li>
                                    <li class="list-group-item"><strong>Description:</strong> <?php echo htmlspecialchars($row['description']); ?></li>
                                </ul>
                                <div class="card-body">
                                    <?php if ($row['status'] == '0'): ?>
                                        <form method="POST" action="manage_event.php" style="display:inline-block;">
                                            <input type="hidden" id="emailBody" name="eventName" value="<?php echo $row['eventName']; ?>">
                                            <input type="hidden" id="emailBody" name="campaignerEmail" value="<?php echo $row['campaignerEmail']; ?>">
                                            <input type="hidden" name="eventid" value="<?php echo $row['eventid']; ?>">
                                            <button type="submit" name="action" value="1" class="badge px-4 py-3 badge-success border-0">Accept</button>
                                        </form>
                                        <form method="POST" action="manage_event.php" style="display:inline-block;">
                                            <input type="hidden" id="emailBody" name="eventName" value="<?php echo $row['eventName']; ?>">
                                            <input type="hidden" id="emailBody" name="campaignerEmail" value="<?php echo $row['campaignerEmail']; ?>">
                                            <input type="hidden" name="eventid" value="<?php echo $row['eventid']; ?>">
                                            <button type="submit" name="action" value="2" class="badge px-4 py-3 badge-danger border-0">Reject</button>
                                        </form>
                                    <?php elseif ($row['status'] == '2'): ?>
                                        <label class="text-danger">*you have rejected this event</label><br>
                                        <span class="badge px-4 py-3 badge-<?php echo $row['status'] == '1' ? 'success' : 'danger'; ?>">
                                            <?php echo $row['status'] == '1' ? 'Accepted' : 'Rejected'; ?>
                                        </span>
                                        <form method="POST" action="manage_event.php" style="display:inline-block;">
                                            <input type="hidden" id="emailBody" name="eventName" value="<?php echo $row['eventName']; ?>">
                                            <input type="hidden" id="emailBody" name="campaignerEmail" value="<?php echo $row['campaignerEmail']; ?>">
                                            <input type="hidden" name="eventid" value="<?php echo $row['eventid']; ?>">
                                            <button type="submit" name="action" value="1" class="badge px-4 py-3 badge-success border-0">Accept</button>
                                        </form>
                                    <?php else: ?>
                                        <label class="text-success">*you have accepted this event</label><br>
                                        <span class="badge px-4 py-3 badge-<?php echo $row['status'] == '1' ? 'success' : 'danger'; ?>">
                                            <?php echo $row['status'] == '1' ? 'Accepted' : 'Rejected'; ?>
                                        </span>
                                        <?php 
                                        if ($row['qrEnv'] != $row['eventid']): ?>
                                            <form method="POST" action="generate_qr.php" style="display:inline-block;">
                                                <input type="hidden" id="emailBody" name="eventName" value="<?php echo $row['eventName']; ?>">
                                                <input type="hidden" id="emailBody" name="campaignerEmail" value="<?php echo $row['campaignerEmail']; ?>">
                                                <input type="hidden" id="ImgEventID" name="eventid" value="<?php echo $row['eventid']; ?>">
                                                <button type="button" class="badge px-4 py-3 border-0 badge-primary" onclick="generateQRCode('<?php echo $row['eventid']; ?>','<?php echo $row['campaignerEmail']; ?>','<?php echo $row['eventName']; ?>')">Generate QR</button>
                                            </form>
                                            <?php endif; ?>
                                            <?php if($row['shared'] != 1): ?>
                                                <form method="POST" action="sendToDonoars.php" style="display:inline-block;">
                                                <input type="hidden" id="eventid" name="eventid" value="<?php echo $row['eventid']; ?>">
                                                <button type="submit" class="badge px-4 py-3 border-0 badge-primary">Send To Donors</button>
                                            </form>
                                        <?php endif; ?>
                                        <form method="POST" action="manage_event.php" style="display:inline-block;">
                                            <input type="hidden" id="emailBody" name="eventName" value="<?php echo $row['eventName']; ?>">
                                            <input type="hidden" id="emailBody" name="campaignerEmail" value="<?php echo $row['campaignerEmail']; ?>">
                                            <input type="hidden" name="eventid" value="<?php echo $row['eventid']; ?>">
                                            <button type="submit" name="action" value="2" class="badge px-4 py-3 badge-danger border-0">Reject</button>
                                        </form>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center" role="alert">
                        No events found.
                    </div>
                </div>
            <?php endif;
            $db->close(); ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 MedAlert. All Rights Reserved.</p>
    </footer>
</body>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.min.js"></script>
<script>
    function generateQRCode(eventId, campaignerEmail, eventName) {
        // console.log(eventId); 
        const qr = qrcode(4, 'H');
        qr.addData('Event ID: ' + eventId);
        qr.make();

        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = 400;
        canvas.height = 400;

        const cellSize = canvas.width / qr.getModuleCount();
        for (let row = 0; row < qr.getModuleCount(); row++) {
            for (let col = 0; col < qr.getModuleCount(); col++) {
                ctx.fillStyle = qr.isDark(row, col) ? '#000000' : '#ffffff';
                ctx.fillRect(col * cellSize, row * cellSize, cellSize, cellSize);
            }
        }

        const dataUrl = canvas.toDataURL('image/png');

        saveQRCode(dataUrl, eventId, campaignerEmail, eventName);
    }

    function saveQRCode(dataUrl, eventId, campaignerEmail, eventName) {
        console.log(campaignerEmail)
        console.log(eventName)
        fetch('generate_qr.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `eventid=${eventId}&image=${encodeURIComponent(dataUrl)}&campaignerEmail=${encodeURIComponent(campaignerEmail)}&eventName=${encodeURIComponent(eventName)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Successfully Generated!");
                    window.location.replace('manageCampaignersRequest.php');
                } else {
                    alert('Error saving QR code.');
                }
            })
    }
</script>

</html>