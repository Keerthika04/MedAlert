<?php
session_start();
require '../db_connection.php';
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

$hospitalId = $_SESSION['hospitalId'];

if ($_SESSION['userLevel'] != 1) {
    $activeSection = isset($_SESSION['activeSection']) ? $_SESSION['activeSection'] : 'dashboard';
} else {
    $activeSection = isset($_SESSION['activeSection']) ? $_SESSION['activeSection'] : 'profile';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <title>MedAlert</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Montserrat">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/hospital.css">
    <link rel="icon" href="../../Images/logo.png" type="image/png">
</head>

<body class="body">
    <div class="sidebar">
        <div class="logo">MedAlert</div>
        <?php if ($_SESSION['userLevel'] != 1) { ?>
            <a href="setSession.php?section=dashboard"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
            <a href="setSession.php?section=profile"><i class="fas fa-hospital"></i>Profile</a>
        <?php } else { ?>
            <a href="setSession.php?section=profile"><i class="fas fa-hospital"></i>Profile</a>
        <?php }; ?>
        <?php if ($_SESSION['userLevel'] != 1) { ?>
            <a href="setSession.php?section=hospital"><i class="fas fa-hospital"></i>Manage Hospital</a>
            <a href="setSession.php?section=donors"><i class="fa-solid fa-hand-holding-droplet"></i>Manage Donors</a>
            <a href="setSession.php?section=campaigners"><i class="fas fa-bullhorn"></i>Manage Campaigners</a>
            <a href="setSession.php?section=campaignersRequest"><i class="fas fa-bullhorn"></i>Manage Campaigners Requests</a>
        <?php }; ?>
        <a href="emergencyAlert.php"><i class="fas fa-bullhorn"></i>Emergency Blood Need</a>
        <a href="setSession.php?section=transplant"><i class="fas fa-procedures"></i>Manage Kidney Transplant Recipients</a>
        <?php if ($_SESSION['userLevel'] != 1) { ?>
            <a href="setSession.php?section=AdManagement"><i class="fas fa-procedures"></i>Ad Management</a>
        <?php }; ?>
        <a href="../logout.php" class="logout"><i class="fas fa-sign-out-alt"></i>Logout</a>
    </div>

    <div class="main-content">
        <?php if ($_SESSION['userLevel'] != 1) { ?>

            <!-- Dashboard Section -->
            <div id="dashboard" class="section  <?php echo $activeSection == 'dashboard' ? 'active' : ''; ?>">
                <br>
                <h2>Dashboard</h2>

                <?php
                // Donors
                $donorQuery = "SELECT COUNT(*) AS donor_count FROM donors";
                $donorResult = mysqli_query($db, $donorQuery);
                $donorRow = mysqli_fetch_assoc($donorResult);
                $donorCount = $donorRow['donor_count'];

                // Hospitals
                $hospitalQuery = "SELECT COUNT(*) AS hospital_count FROM hospitals where userLevel = 1";
                $hospitalResult = mysqli_query($db, $hospitalQuery);
                $hospitalRow = mysqli_fetch_assoc($hospitalResult);
                $hospitalCount = $hospitalRow['hospital_count'];

                // Campaigners
                $campaignerQuery = "SELECT COUNT(*) AS campaigner_count FROM campaigners";
                $campaignerResult = mysqli_query($db, $campaignerQuery);
                $campaignerRow = mysqli_fetch_assoc($campaignerResult);
                $campaignerCount = $campaignerRow['campaigner_count'];
                ?>

                <!-- Card Section -->
                <div class="card-container">
                    <div class="card Rcard card-donors">
                        <div class="card-content">
                            <div>
                                <i class="fas fa-users"></i>
                                <p>Total Donors</p>
                            </div>
                            <h3><?php echo $donorCount; ?></h3>
                            <p>Active Donors</p>
                        </div>
                    </div>
                    <div class="card Rcard card-campaigners">
                        <div class="card-content">
                            <div>
                                <i class="fas fa-bullhorn"></i>
                                <p>Campaigners</p>
                            </div>
                            <h3><?php echo $campaignerCount; ?></h3>
                            <p>Active Campaigners</p>
                        </div>
                    </div>
                    <div class="card Rcard card-blood-groups">
                        <div class="card-content">
                            <div>
                                <i class="fas fa-tint"></i>
                                <p>Blood Groups</p>
                            </div>
                            <h3>7</h3>
                            <p>Available Types</p>
                        </div>
                    </div>
                    <div class="card Rcard card-hospital">
                        <div class="card-content">
                            <div>
                                <i class="fas fa-tint"></i>
                                <p>Registered Hospitals</p>
                            </div>
                            <h3><?php echo $hospitalCount; ?></h3>
                            <p>Active Hospitals</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php }; ?>


        <div class="section  <?php echo $activeSection == 'profile' ? 'active' : ''; ?>" id="profile">
            <?php
            $stmt = $db->prepare("SELECT hospitalName, address, contact, email FROM hospitals WHERE hospitalId = ?");
            $stmt->bind_param("s", $hospitalId);
            $stmt->execute();
            $result = $stmt->get_result();
            $hospital = $result->fetch_assoc();
            $stmt->close();
            ?>
            <div class="card my-3 mx-2">
                <div class="card-body text-center">
                    <h2 class="card-title">Hospital Details</h5>
                        <div class="mb-3">
                            <label for="hospitalName" class="form-label">Hospital Name</label>
                            <input type="text" class="form-control" id="hospitalName" name="hospitalName" value="<?php echo $hospital['hospitalName']; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address" value="<?php echo $hospital['address']; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="contact" class="form-label">Contact</label>
                            <input type="text" class="form-control" id="contactNo" name="contact" value="<?php echo $hospital['contact']; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $hospital['email']; ?>" readonly>
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
                            <h5 class="modal-title" id="editModalLabel">Edit Hospital Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
                        </div>
                        <div class="modal-body">
                            <form action="update_hospital.php" method="post">
                                <div class="mb-3">
                                    <label for="hospitalName" class="form-label">Hospital Name</label>
                                    <input type="text" class="form-control" id="hospitalName" name="hospitalName" value="<?php echo htmlspecialchars($hospital['hospitalName']); ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($hospital['address']); ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="contact" class="form-label">Contact</label>
                                    <input type="text" class="form-control" id="contactNo" name="contact"
                                        pattern="^(\+94[0-9]{9}|0[0-9]{9})$"
                                        placeholder="+94XXXXXXXXX"
                                        title="Contact number should start with +94 followed by 9 digits (e.g., +94123456789)"
                                        value="<?php echo htmlspecialchars($hospital['contact']); ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($hospital['email']); ?>">
                                </div>
                                <button type="submit" class="btn">Save Changes</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <br>

        <!-- Hospital Management Section -->
        <div id="hospital" class="section <?php echo $activeSection == 'hospital' ? 'active' : ''; ?>">
            <div class="header">
                <form method="GET" action="" class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Search..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit" style="display: none;"> Search</button>
                </form>
            </div>
            <br>
            <h2>Manage Hospitals</h2>

            <?php
            $search = isset($_GET['search']) ? $_GET['search'] : '';

            $sql = "SELECT hospitalId, hospitalName, username, address, contact, email FROM hospitals WHERE userLevel != '0'";

            if (!empty($search)) {
                $search = "%$search%";
                $sql .= " AND (hospitalName LIKE ? OR address LIKE ? OR contact LIKE ? OR email LIKE ? OR username LIKE ?)";

                $stmt = $db->prepare($sql);
                $stmt->bind_param("sssss", $search, $search, $search, $search, $search);
                $stmt->execute();
                $result = $stmt->get_result();
            } else {
                $result = $db->query($sql);
            }
            if ($result->num_rows > 0) {
                // Loop through all the records and display them
                while ($row = $result->fetch_assoc()) {
            ?>
                    <!-- Hospital Card -->
                    <div class="hospital-card">

                        <div class="card-header">
                            <h3><?php echo htmlspecialchars($row['hospitalId']); ?></h3>
                            <div class="card-icons">
                                <a href="editHospital.php?hospitalId=<?php echo urlencode($row['hospitalId']); ?>" class="edit-icon"><i class="fas fa-edit"></i></a>
                                <a href="deleteHospital.php?hospitalId=<?php echo urlencode($row['hospitalId']); ?>" onclick="return confirm('Are you sure you want to delete this Hospital?');">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </div>
                        <table class="hospital-table">
                            <tbody>
                                <tr>
                                    <td><strong>Hospital Name:</strong></td>
                                    <td><?php echo htmlspecialchars($row['hospitalName']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Username:</strong></td>
                                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Contact Number:</strong></td>
                                    <td><?php echo htmlspecialchars($row['contact']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Address:</strong></td>
                                    <td><?php echo htmlspecialchars($row['address']); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
            <?php
                }
            } else {
                echo "No hospitals found.";
            }
            ?>
        </div>


        <!-- Donors Management Section -->
        <div id="donors" class="section <?php echo $activeSection == 'donors' ? 'active' : ''; ?>">
            <div class="header">
                <form method="GET" action="" class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Search..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit" style="display: none;"> Search</button>
                </form>
                <button onclick="window.location.href='download_donors.php'" class="badge px-4 py-3 badge-warning border-0" style="font-size: 0.8rem;"><i class="fa-solid fa-download"></i> Download Donor Details as PDF</button>
            </div>
            <br>
            <h2>Manage Donors</h2>
            <?php
            $search = isset($_GET['search']) ? $_GET['search'] : '';
            $sql = "SELECT Donorid, firstName, lastName , username, NICnumber, weight, bloodGroup, dateOfBirth, gender, address, personalContact, emergencyContact, email, eligibilityStatus FROM donors";

            if (!empty($search)) {
                if (strcasecmp($search, 'Eligible') == 0) {
                    $search = '1';
                } elseif (strcasecmp($search, 'Not Eligible') == 0 || strcasecmp($search, 'Not') == 0) {
                    $search = '0';
                } else {
                    $search = "%$search%";
                }

                $sql .= " WHERE (firstName LIKE ? OR username LIKE ? OR lastName LIKE ? OR NICnumber LIKE ? OR bloodGroup LIKE ? OR address LIKE ? OR personalContact LIKE ? OR emergencyContact LIKE ? OR email LIKE ? OR eligibilityStatus LIKE ?)";

                $stmt = $db->prepare($sql);
                $stmt->bind_param("ssssssssss", $search, $search, $search, $search, $search, $search, $search, $search, $search, $search);
                $stmt->execute();
                $result = $stmt->get_result();
            } else {
                $result = $db->query($sql);
            }
            if ($result->num_rows > 0) {
                // Loop through all donor records and display them
                while ($row = $result->fetch_assoc()) {
                    // Determine eligibility status
                    $eligibilityStatus = $row['eligibilityStatus'] == 1 ? 'Eligible' : 'Not Eligible';
            ?>
                    <!-- Donor Card -->
                    <div class="donor-card">
                        <div class="card-header">
                            <h3><?php echo $row['Donorid']; ?></h3>
                            <div class="card-icons">
                                <a href="editDonor.php?donorId=<?php echo urlencode($row['Donorid']); ?>" class="edit-icon"><i class="fas fa-edit"></i></a>
                                <a href="deleteDonor.php?donorId=<?php echo urlencode($row['Donorid']); ?>" onclick="return confirm('Are you sure you want to delete this donor?');">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </div>
                        <table class="donor-table">
                            <tbody>
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td><?php echo $row['firstName'] . " " . $row['lastName']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Username:</strong></td>
                                    <td><?php echo $row['username']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>NIC Number:</strong></td>
                                    <td><?php echo $row['NICnumber']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Weight:</strong></td>
                                    <td><?php echo $row['weight']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Blood Group:</strong></td>
                                    <td><?php echo $row['bloodGroup']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Date of Birth:</strong></td>
                                    <td><?php echo $row['dateOfBirth']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Gender:</strong></td>
                                    <td><?php echo $row['gender']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Address:</strong></td>
                                    <td><?php echo $row['address']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Personal Contact:</strong></td>
                                    <td><?php echo $row['personalContact']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Emergency Contact:</strong></td>
                                    <td><?php echo $row['emergencyContact']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td><?php echo $row['email']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Eligibility Status:</strong></td>
                                    <td class="text-<?php echo $row['eligibilityStatus'] == 1 ? "success" : "danger" ?>"><?php echo $eligibilityStatus; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
            <?php
                }
            } else {
                echo "No donors found.";
            }
            ?>
        </div>

        <!-- Campaigners Management Section -->
        <div id="campaigners" class="section <?php echo $activeSection == 'campaigners' ? 'active' : ''; ?>">
            <div class="header">
                <form method="GET" action="" class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Search..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit" style="display: none;"> Search</button>
                </form>
            </div>
            <br>
            <h2>Manage Campaigners</h2>

            <?php

            // Query to retrieve all records from the campaigners table
            $sql = "SELECT campaignersId, name, address, contact, email, description, username FROM campaigners";

            if (!empty($search)) {
                $search = "%$search%";

                $sql .= " WHERE (name LIKE ? OR address LIKE ? OR contact LIKE ? OR email LIKE ? OR description LIKE ? OR username LIKE ?)";
                $stmt = $db->prepare($sql);

                $stmt->bind_param("ssssss", $search, $search, $search, $search, $search, $search);
                $stmt->execute();

                $result = $stmt->get_result();
            } else {
                $result = $db->query($sql);
            }

            if ($result->num_rows > 0) {
                // Loop through all the records and display them
                while ($row = $result->fetch_assoc()) {
            ?>
                    <!-- Campaigner Card -->
                    <div class="campaigner-card">
                        <div class="card-header">
                            <h3><?php echo $row['campaignersId']; ?></h3>
                            <div class="card-icons">
                                <a href="editCampaigner.php?campaignerId=<?php echo urlencode($row['campaignersId']); ?>" class="edit-icon"><i class="fas fa-edit"></i></a>
                                <a href="deleteCampaigner.php?campaignerId=<?php echo urlencode($row['campaignersId']); ?>" onclick="return confirm('Are you sure you want to delete this campaigner?');">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </div>
                        <table class="campaigner-table">
                            <tbody>
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td><?php echo $row['name']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Username:</strong></td>
                                    <td><?php echo $row['username']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Contact Number:</strong></td>
                                    <td><?php echo $row['contact']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td><?php echo $row['email']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Address:</strong></td>
                                    <td><?php echo $row['address']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Description:</strong></td>
                                    <td><?php echo $row['description']; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
            <?php
                }
            } else {
                echo "No campaigners found.";
            }

            ?>
        </div>

        <!-- Campaigner's Request Management Section -->
        <div class="section <?php echo $activeSection == 'campaignersRequest' ? 'active' : ''; ?>" id="campaignersRequest">
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
                                            <span class="badge px-4 py-3 badge-<?php echo $row['status'] == '1' || '3' || '4' ? 'success' : 'danger'; ?>">
                                                <?php echo $row['status'] == '1' || '3' || '4' ? 'Accepted' : 'Rejected'; ?>
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
                                            <?php if ($row['shared'] != 1): ?>
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
                ?>
            </div>
        </div>

        <!-- Transplant Request Management Section -->
        <div id="transplant" class="section <?php echo $activeSection == 'transplant' ? 'active' : ''; ?>">
            <br>
            <h2>Manage Kidney Transplant Recipients</h2>
            <!-- Add Transplant Advertisement Button -->
            <div class="button-container">
                <a href="addTransplant.php" class="btn btn-primary">Add New Advertisement</a>
            </div>
            <?php
            if ($_SESSION['userLevel'] == 0) {
                $sql = "SELECT k.kidneyTransplantAdvertisementId, k.adBanner, k.patientName, k.age, k.contact, k.email, k.description, h.hospitalName FROM kidneytransplantadvertisement k 
                    JOIN hospitals h ON k.hospitalId = h.hospitalId;";
                $result = $db->query($sql);
            } else {
                $sql = "SELECT k.kidneyTransplantAdvertisementId, k.adBanner, k.patientName, k.age, k.contact, k.email, k.description, h.hospitalName 
                    FROM kidneytransplantadvertisement k 
                    JOIN hospitals h ON k.hospitalId = h.hospitalId 
                    WHERE k.hospitalId = ?";

                $stmt = $db->prepare($sql);
                $stmt->bind_param("s", $hospitalId);

                $stmt->execute();
                $result = $stmt->get_result();
            }

            if ($result->num_rows > 0) {
                // Loop through all advertisement records
                while ($row = $result->fetch_assoc()) {
            ?>
                    <!-- Transplant Card -->
                    <div class="transplant-card">
                        <div class="card-header">
                            <h3><?php echo htmlspecialchars($row['kidneyTransplantAdvertisementId']); ?></h3>
                            <div class="card-icons">
                                <a href="editTransplant.php?kidneyTransplantAdvertisementId=<?php echo urlencode($row['kidneyTransplantAdvertisementId']); ?>" class="edit-icon"><i class="fas fa-edit"></i></a>
                                <a href="deleteKidneyTransplantRecipients.php?kidneyTransplantAdvertisementId=<?php echo urlencode($row['kidneyTransplantAdvertisementId']); ?>" onclick="return confirm('Are you sure you want to delete this advertisement?');">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </div>
                        <table class="transplant-table">
                            <tbody>
                                <tr>
                                    <td><strong>Recipient Name:</strong></td>
                                    <td><?php echo htmlspecialchars($row['patientName']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Age:</strong></td>
                                    <td><?php echo htmlspecialchars($row['age']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Contact Number:</strong></td>
                                    <td><?php echo htmlspecialchars($row['contact']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Description:</strong></td>
                                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Hospital:</strong></td>
                                    <td><?php echo htmlspecialchars($row['hospitalName']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Advertisement Banner:</strong></td>
                                    <td>
                                        <?php if (!empty($row['adBanner'])) { ?>
                                            <img src="../../TransplantAd<?php echo htmlspecialchars($row['adBanner']); ?>" alt="Advertisement Banner" class="ad-banner">
                                        <?php } ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
            <?php
                }
            } else {
                echo "No advertisements found.";
            }
            ?>
        </div>

        <!-- Ad Management Section -->
        <div id="AdManagement" class="section <?php echo $activeSection == 'AdManagement' ? 'active' : ''; ?>">
            <br>
            <?php
            $eventAds = "SELECT eventid, banner, status FROM events WHERE status IN (1, 3, 4) ORDER BY status ASC";
            $eventResults = mysqli_query($db, $eventAds);

            $kidneyAds = "SELECT kidneyTransplantAdvertisementId, adBanner, status FROM kidneytransplantadvertisement ORDER BY status ASC";
            $kidneyResults = mysqli_query($db, $kidneyAds);
            ?>

            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <h2 class="my-4 text-center">Events Ads</h2>
                        <?php while ($event = mysqli_fetch_assoc($eventResults)) { ?>
                            <div class="card mb-4">
                                <img src="../../camp_post/<?php echo htmlspecialchars($event['banner']); ?>" class="card-img-top img-fluid" alt="Event Banner">
                                <div class="card-body text-center">
                                    <form action="updateStatus.php" method="post">
                                        <input type="hidden" name="id" value="<?php echo $event['eventid']; ?>">
                                        <input type="hidden" name="adType" value="event">
                                        <?php if ($event['status'] == 1) { ?>
                                            <input type="hidden" name="status" value="3">
                                            <button type="submit" class="badge px-4 py-3 badge-warning border-0">Upload</button>
                                        <?php } elseif ($event['status'] == 3) { ?>
                                            <p class="card-text text-success">*Currently showing on the website</p>
                                            <input type="hidden" name="status" value="4">
                                            <button type="submit" class="badge px-4 py-3 badge-danger border-0">Remove</button>
                                        <?php } elseif ($event['status'] == 4) { ?>
                                            <p class="card-text text-danger">*You have removed this from ad</p>
                                            <input type="hidden" name="status" value="3">
                                            <button type="submit" class="badge px-4 py-3 badge-warning border-0">Upload</button>
                                        <?php } ?>
                                    </form>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="col-md-6">
                        <h2 class="my-4 text-center">Kidney Ads</h2>
                        <?php while ($ad = mysqli_fetch_assoc($kidneyResults)) { ?>
                            <div class="card mb-4">
                                <img src="<?php echo htmlspecialchars($ad['adBanner']); ?>" class="card-img-top img-fluid" alt="Kidney Transplant Ad Banner" style="object-fit: cover;">
                                <div class="card-body text-center">
                                    <form action="updateStatus.php" method="post">
                                        <input type="hidden" name="id" value="<?php echo $ad['kidneyTransplantAdvertisementId']; ?>">
                                        <input type="hidden" name="adType" value="kidney">
                                        <?php if ($ad['status'] == 0) { ?>
                                            <input type="hidden" name="status" value="3">
                                            <button type="submit" class="badge px-4 py-3 badge-warning border-0">Upload</button>
                                        <?php } elseif ($ad['status'] == 3) { ?>
                                            <p class="card-text text-success">*Currently showing on the website</p>
                                            <input type="hidden" name="status" value="4">
                                            <button type="submit" class="badge px-4 py-3 badge-danger border-0">Remove</button>
                                        <?php } elseif ($ad['status'] == 4) { ?>
                                            <p class="card-text text-danger">*You have removed this from ad</p>
                                            <input type="hidden" name="status" value="3">
                                            <button type="submit" class="badge px-4 py-3 badge-warning border-0">Upload</button>
                                        <?php } ?>
                                    </form>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

        </div>


    </div>
    </div>

    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.2/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.min.js"></script>
    <script>
        function generateQRCode(eventId, campaignerEmail, eventName) {
            const padding = 20; // Define the padding size
            const qr = qrcode(1, 'L');
            qr.addData(eventId);
            qr.make();

            const cellSize = 10; // Size of each QR code cell
            const qrSize = qr.getModuleCount() * cellSize; // Size of the QR code
            const canvasSize = qrSize + 2 * padding; // Total canvas size including padding

            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            canvas.width = canvasSize;
            canvas.height = canvasSize;

            // Draw white background
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            // Draw the QR code
            for (let row = 0; row < qr.getModuleCount(); row++) {
                for (let col = 0; col < qr.getModuleCount(); col++) {
                    ctx.fillStyle = qr.isDark(row, col) ? '#000000' : '#ffffff';
                    ctx.fillRect(
                        col * cellSize + padding,
                        row * cellSize + padding,
                        cellSize,
                        cellSize
                    );
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
                        window.location.replace('HospitalDashBoard.php');
                    } else {
                        alert('Error saving QR code.');
                    }
                })
        }
    </script>

</body>

</html>