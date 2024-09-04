<?php
session_start();
require '../db_connection.php';
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}
$campaignersId = $_SESSION['campaignersId'];
$activeSection = isset($_SESSION['activeSection']) ? $_SESSION['activeSection'] : 'profile';
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/hospital.css">
    <link rel="icon" href="../../Images/logo.png" type="image/png">
    <title>MedAlert</title>
    <style>
        /* General Styling */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

        /* Card Title */
        .card-title {
            font-size: 1.5rem; 
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
        }

        /* Card Text */
        .card-text {
            font-size: 1rem;
            line-height: 1.6;
            color: #555;
        }

        /* Status Buttons */
        .status-buttons .btn {
            font-size: 0.875rem;
            font-weight: 500; 
            padding: 0.5rem 1rem;
            border-radius: 4px;
            border: none;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .status-buttons .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .status-buttons .btn-warning:hover {
            background-color: #e0a800;
        }

        .status-buttons .btn-success {
            background-color: #28a745;
            color: #fff;
        }

        .status-buttons .btn-success:hover {
            background-color: #218838; 
        }

        .status-buttons .btn-danger {
            background-color: #dc3545;
            color: #fff;
        }

        .status-buttons .btn-danger:hover {
            background-color: #c82333;
        }

        .status-buttons .btn-secondary {
            background-color: #6c757d;
            color: #fff;
        }

        .status-buttons .btn-secondary:hover {
            background-color: #5a6268;
        }

        /* Card Header */
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
            padding: 0.75rem;
            font-size: 2.25rem;
            font-weight: 800; 
            color: #333;
        }

        /* Container Heading */
        .container h2 {
            font-size: 2rem;
            font-weight: 700; 
            margin-bottom: 2rem;
        }

        /* Description Text */
        .card-text strong {
            font-weight: 600;
            color: #000;
        }
    </style>
</head>

<body>
    <header class="navbar-container_long">
        <div class="logo">
            <img src="../../Images/logo.png" alt="MedAlert Logo" class="logo-img">
            <span class="logo-name">MedAlert - Your Healthcare Partner</span>
        </div>
        <div class="activeNav mt-3">
            <div id="nav-toggle" class="nav-toggle_long">☰</div>
            <div class="nav-links nav-links_long">
                <a href="setSession.php?section=profile" class="nav-button"> Profile </a>
                <a href="setSession.php?section=requestEvent" class="nav-button"> Request Event</a>
                <a href="setSession.php?section=manageEvent" class="nav-button">Manage Event</a>
                <a href="../logout.php" class="nav-button"> Logout</a>
            </div>
        </div>
    </header>

    <div class="section <?php echo $activeSection == 'profile' ? 'active' : ''; ?>" id="profile">
        <?php
        $stmt = $db->prepare("SELECT campaignersId, name, address, contact, email, description, username, password FROM campaigners WHERE campaignersId = ?");
        $stmt->bind_param("s", $campaignersId);
        $stmt->execute();
        $result = $stmt->get_result();
        $campaigner = $result->fetch_assoc();
        $stmt->close();
        ?>
        <div class="card my-3 mx-2">
            <div class="card-body text-center">
                <h2 class="card-title">Campaigner Details</h2>
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($campaigner['name']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($campaigner['address']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="contact" class="form-label">Contact</label>
                    <input type="text" class="form-control" id="contactNo" name="contact" value="<?php echo htmlspecialchars($campaigner['contact']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($campaigner['email']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" readonly rows="10"><?php echo htmlspecialchars($campaigner['description']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($campaigner['username']); ?>" readonly>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal">
                    Edit Details
                </button>
            </div>
        </div>
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Campaigner Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
                    </div>
                    <div class="modal-body">
                        <form action="update_campaigner.php" method="post">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($campaigner['name']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($campaigner['address']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="contact" class="form-label">Contact</label>
                                <input type="text" class="form-control" id="contactNo" name="contact"
                                    pattern="^(\+94[0-9]{9}|0[0-9]{9})$"
                                    placeholder="+94XXXXXXXXX"
                                    title="Contact number should start with +94 followed by 9 digits (e.g., +94123456789)"
                                    value="<?php echo htmlspecialchars($campaigner['contact']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($campaigner['email']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="10"><?php echo htmlspecialchars($campaigner['description']); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($campaigner['username']); ?>">
                            </div>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="section <?php echo $activeSection == 'requestEvent' ? 'active' : ''; ?>" id="requestEvent">
        <?php
        $stmt = $db->prepare("SELECT hospitalId, hospitalName FROM hospitals");
        $stmt->execute();
        $result = $stmt->get_result();
        $hospitals = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        ?>
        <div class="container text-center mt-5">
            <h2>Request New Event</h2>
            <form action="request_event.php" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label">Event Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="banner" class="form-label">Event Banner</label>
                    <input type="file" class="form-control-file" id="banner" name="banner" accept=".jpg, .jpeg, .png" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="date" class="form-label">Event Date</label>
                    <label class="text-danger small-font">*you can't use today's date, you have to request before a day!</label>
                    <input type="date" class="form-control" id="date" name="date" required>
                </div>
                <div class="d-flex">
                    <div class="mb-3">
                        <label for="startTime" class="form-label">Start Time</label>
                        <input type="time" class="form-control" id="startTime" name="startTime" min="06:00" max="17:00" required oninput="validateTime()">
                    </div>
                    <div class="mb-3 ml-2">
                        <label for="endTime" class="form-label">End Time</label>
                        <input type="time" class="form-control" id="endTime" name="endTime" min="06:00" max="17:00" required oninput="validateTime()">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($campaigner['address']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="hospitalId" class="form-label">Hospital</label>
                    <select class="form-control" id="hospitalId" name="hospitalId" required>
                        <option value="">Select Hospital</option>
                        <?php foreach ($hospitals as $hospital): ?>
                            <option value="<?php echo $hospital['hospitalId']; ?>"><?php echo htmlspecialchars($hospital['hospitalName']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Submit Request</button>
            </form>
        </div>
    </div>

    <div class="section <?php echo $activeSection == 'manageEvent' ? 'active' : ''; ?>" id="manageEvent">
        <div class="container mt-5">
            <h2 class="text-center mb-4">Manage Events</h2>
            <?php
            $stmt = $db->prepare("SELECT eventid, name, date, time, location, banner, description, status FROM events WHERE campaignersId = ?");
            $stmt->bind_param("s", $campaignersId);
            $stmt->execute();
            $result = $stmt->get_result();
            $events = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            ?>

            <div class="row">
                <?php if (count($events) > 0): ?>
                    <?php foreach ($events as $event): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card shadow-sm border-light">
                                <?php if ($event['banner']): ?>
                                    <img src="../../camp_post/<?php echo htmlspecialchars($event['banner']); ?>" class="card-img-top" alt="Event Banner">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($event['name']); ?></h5>
                                    <p class="card-text mb-3">
                                        <strong>Date:</strong> <?php echo htmlspecialchars($event['date']); ?><br>
                                        <strong>Time:</strong> <?php echo htmlspecialchars($event['time']); ?><br>
                                        <strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?><br>
                                        <strong>Description:</strong> <?php echo htmlspecialchars($event['description']); ?>
                                    </p>
                                    <div class="status-buttons text-center">
                                        <?php
                                        switch ($event['status']) {
                                            case 0:
                                                echo '<button class="btn btn-warning">Pending</button>';
                                                break;
                                            case 1:
                                                echo '<button class="btn btn-success">Accepted</button>';
                                                break;
                                            case 2:
                                                echo '<button class="btn btn-danger">Rejected</button>';
                                                break;
                                            default:
                                                echo '<button class="btn btn-secondary">Unknown Status</button>';
                                                break;
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center">No events found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>



    <footer>
        <p>&copy; 2024 MedAlert. All Rights Reserved.</p>
    </footer>
</body>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="../../Script/script.js"></script>
<script>
    // Date Validations
    const today = new Date();
    let tomorrow = new Date(today);
    tomorrow.setDate(today.getDate() + 1);

    const dd = String(tomorrow.getDate()).padStart(2, '0');
    const mm = String(tomorrow.getMonth() + 1).padStart(2, '0');
    const yyyy = tomorrow.getFullYear();

    tomorrow = yyyy + '-' + mm + '-' + dd;

    document.getElementById("date").setAttribute("min", tomorrow);

    // Time Validations
    function validateTime() {
        const startTimeInput = document.getElementById("startTime");
        const endTimeInput = document.getElementById("endTime");

        const startTime = new Date(`2000-01-01T${startTimeInput.value}`);
        const endTime = new Date(`2000-01-01T${endTimeInput.value}`);

        if (endTime - startTime < 3600000) { 
            endTimeInput.setCustomValidity("End time must be at least 1 hour after start time and should be 5pm or below.");
            return false;
        } else {
            endTimeInput.setCustomValidity("");
            return true;
        }
    }

    function showSection(sectionId) {
        // Hide all sections
        const sections = document.querySelectorAll('.section');
        sections.forEach(section => {
            section.classList.remove('active');
        });

        // Show the selected section
        document.getElementById(sectionId).classList.add('active');

        // Update active class on sidebar links
        const links = document.querySelectorAll('.nav-button');
        links.forEach(link => {
            link.classList.remove('active');
        });
        event.target.classList.add('active');
    }

</script>

</html>