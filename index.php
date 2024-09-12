<?php
include 'php/db_connection.php';

$query = "SELECT adBanner FROM kidneytransplantadvertisement WHERE status = 3";
$result = $db->query($query);

if (!$result) {
    die("Query failed: " . $db->error);
}

$images = [];
while ($row = $result->fetch_assoc()) {
    $images[] = $row['adBanner'];
}

$db->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="MedAlert - Managing emergency blood needs and kidney transplant advertisements in Sri Lanka.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/slider.css">
    <link rel="stylesheet" href="css/awareness.css">
    <link rel="icon" href="Images/logo.png" type="image/png">
    <title>MedAlert</title>
    <style>
        .invite-friends {
            width: 1000px;
            text-align: center;
            margin: 20px auto;
            background-color: var(--light);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .invite-friends h3 {
            color: var(--Dred);
            font-size: 1.8em;
            margin-bottom: 15px;
        }

        .invite-friends p {
            font-size: 1em;
            margin-bottom: 10px;
            color: var(--black);
        }

        .share-link {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 15px;
        }

        .share-link input {
            width: 70%;
            padding: 10px;
            border: 1px solid var(--Dred);
            border-radius: 5px;
            font-size: 1em;
            margin-right: 10px;
        }

        .social-share-buttons .btn {
            margin: 5px;
            width: auto;
            padding: 10px 20px;
            font-size: 1em;
            border: none;
            border-radius: 5px;
            color: var(--white);
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        .facebook-share {
            background-color: #3b5998;
        }

        .facebook-share:hover {
            background-color: #2d4373;
        }

        .twitter-share {
            background-color: #1da1f2;
        }

        .twitter-share:hover {
            background-color: #0d8dd3;
        }

        .instagram-share {
            background-color: #e4405f;
        }

        .instagram-share:hover {
            background-color: #c13584;
        }

        .whatsapp-share {
            background-color: #25d366;
        }

        .whatsapp-share:hover {
            background-color: #128c7e;
        }

    </style>
</head>

<body>
    <header class="navbar-container">
        <div class="logo">
            <img src="Images/logo.png" alt="MedAlert Logo" class="logo-img">
            <span class="logo-name">MedAlert - Your Healthcare Partner</span>
        </div>
        <div class="activeNav">
            <div id="nav-toggle" class="nav-toggle">☰</div>
            <div class="nav-links">
                <a href="./php/login.php" class="nav-button">Login</a>
                <a href="signup_selection.html" class="nav-button">Signup</a>
                <a href="php/testimonial.php" class="nav-button">User Feedbacks</a>
            </div>
        </div>
    </header>

    <section id="about">
        <h2>Welcome to MedAlert</h2>
        <p>MedAlert is your reliable partner in managing emergency blood needs and kidney transplant advertisements
            across Sri Lanka. Our platform connects hospitals with eligible donors, ensuring that life-saving help is
            just a click away.</p>
    </section>

    <section id="WhatWeDo">
        <h2> - - What We Do - -</h2>
        <div class="WhatWeDo-container">
            <div class="WhatWeDo-card">
                <h3>Emergency Blood Alerts</h3>
                <p>Our real-time blood alert system notifies eligible donors instantly, ensuring that hospitals can
                    respond swiftly to critical needs.</p>
            </div>
            <div class="WhatWeDo-card">
                <h3>Kidney Transplant Advertisements</h3>
                <p>We provide a centralized platform for hospitals to post and manage advertisements for kidney
                    transplants, helping patients find donors faster.</p>
            </div>
            <div class="WhatWeDo-card">
                <h3>Streamlined Communication</h3>
                <p>Our platform facilitates seamless communication between hospitals and donors, reducing delays and
                    ensuring
                    quick coordination during emergencies.</p>
            </div>
        </div>
    </section>
    
    <section id="slider">
        <h2> Kidney Transplant Recipient Advertisements </h2>
        <div class="slider-container">
            <?php if (!empty($images)): ?>
                <div class="slider">
                    <?php foreach ($images as $index => $image): ?>
                        <div class="slide">
                            <img src="TransplantAd<?php echo htmlspecialchars($image); ?>" alt="Kidney Transplant Advertisement <?php echo $index + 1; ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="prev" onclick="moveSlide(-1)"><i class="fas fa-chevron-left"></i></button>
                <button class="next" onclick="moveSlide(1)"><i class="fas fa-chevron-right"></i></button>
                <div class="dots-container">
                    <?php foreach ($images as $index => $image): ?>
                        <span class="dot" onclick="currentSlide(<?php echo $index + 1; ?>)"></span>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-ads">No advertisements available at the moment.</p>
            <?php endif; ?>
        </div>
    </section>



    <section id="awareness">
        <h2>Donate Blood & Organs - Save Lives</h2>
        <p>
            Every donation can save up to three lives. Blood and organ donation are vital actions that help millions of people across the world. 
            Your contribution can make a difference and give someone a second chance at life. Join the movement today and be a life-saving hero.
        </p>

        <!-- Video Awareness Links -->
        <div class="youtube-links">
            <iframe src="https://www.youtube.com/embed/xKNiDlA7cZk" allowfullscreen></iframe>
            <iframe src="//www.youtube.com/embed/erG3HJPlatg" allowfullscreen></iframe>
        </div>
    </section>

    <section id="awareness-modern">
        <!-- Why Donate Section -->
        <div class="why-donate-modern">
            <h2>Why Donate Blood & Organs?</h2>
            <div class="why-donate-cards">
                <div class="why-donate-card">
                    <i class="fas fa-heartbeat"></i>
                    <h4>Save Lives</h4>
                    <p>Each donation can save up to three lives. Your blood or organ donation can make a life-saving difference for patients in need.</p>
                </div>
                <div class="why-donate-card">
                    <i class="fas fa-hand-holding-heart"></i>
                    <h4>Health Benefits</h4>
                    <p>Donating regularly can improve your own health by promoting cardiovascular well-being and reducing cancer risks.</p>
                </div>
                <div class="why-donate-card">
                    <i class="fas fa-globe"></i>
                    <h4>Support Community</h4>
                    <p>Your donation directly impacts your local community by supporting hospitals and patients with critical blood shortages.</p>
                </div>
                <div class="why-donate-card">
                    <i class="fas fa-infinity"></i>
                    <h4>Leave a Legacy</h4>
                    <p>Organ donors have the chance to save up to eight lives after they pass away, leaving behind a life-saving legacy.</p>
                </div>
            </div>
        </div>

        <!-- Frequently Asked Questions Section -->
        <div class="faq-modern">
            <h2>Frequently Asked Questions</h2>
            <div class="faq-cards">
                <div class="faq-card">
                    <h4>Who can donate blood?</h4>
                    <p>Most people in good health, aged between 18 and 65, and weighing at least 50 kg are eligible to donate blood.</p>
                </div>
                <div class="faq-card">
                    <h4>Is blood donation safe?</h4>
                    <p>Yes! It is a safe process. Sterile equipment is used for each donor, ensuring there is no risk of infection.</p>
                </div>
                <div class="faq-card">
                    <h4>What organs can I donate?</h4>
                    <p>Key organs include kidneys, liver, heart, lungs, pancreas, and intestines. Tissues such as corneas can also be donated.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="feedback-section">
        <div class="feedback-content">
            <h2>We Value Your Feedback</h2>
            <p>Your experience matters to us. Please share your thoughts and suggestions to help us improve our services and better support our community.</p>
            <a href="php/login.php" class="feedback-button">Give Feedback</a>
            <a href="php/testimonial.php" class="feedback-button">View User Reviews</a>
        </div>
    </section>

    <section id="how-it-works">
        <h2>How It Works</h2>

        <div id="donors" class="how-it-works-item">
            <img src="Images/donationImg.jpg" alt="Donors" class="how-it-works-img">
            <div class="how-it-works-content">
                <h3>Donors can:</h3>
                <ul>
                    <li>Update their personal details and track their donation history through their dashboard.</li>
                    <li>Mark their attendance at donation events by scanning QR codes.</li>
                    <li>Receive email alerts about urgent blood needs matching their blood type.</li>
                    <li>Invite friends to join the donation community through social media links.</li>
                </ul>
            </div>
        </div>

        <div id="hospitals" class="how-it-works-item">
            <img src="Images/hospital.jpg" alt="Hospitals" class="how-it-works-img">
            <div class="how-it-works-content">
                <h3>Hospitals can:</h3>
                <ul>
                    <li>Register and manage their profiles, including updating information.</li>
                    <li>Post and manage advertisements for kidney transplant recipients.</li>
                    <li>Send notifications to registered donors about emergency blood needs.</li>
                </ul>
            </div>
        </div>

        <div id="campaigners" class="how-it-works-item">
            <img src="Images/campaingers.jpg" alt="Campaigners" class="how-it-works-img">
            <div class="how-it-works-content">
                <h3>Campaigners can:</h3>
                <ul>
                    <li>Update their personal profiles to keep their information current.</li>
                    <li>Request and manage posts about blood donation campaigns.</li>
                    <li>Organize and manage blood donation events to boost participation and awareness.</li>
                </ul>
            </div>
        </div>
    </section>

    <section id="contact">
        <h2>Join Hands with Us Today!</h2>
        <p>By signing up, you become part of a life-saving network that ensures no emergency goes unanswered. Whether
            you’re a donor, a hospital admin, or a campaigner, MedAlert offers you the tools you need to make a
            difference.</p>
        <a href="php/login.php" class="btn wBtn"><span></span><span></span><span></span>
            <span></span>Login</a>
        <a href="signup_selection.html" class="btn wBtn"><span></span><span></span><span></span>
            <span></span>Sign Up</a>

        <!-- Add the invite section here -->
        <div class="invite-friends">
            <h3>Invite Your Friends to Donate Blood</h3>
            <p style="color: var(--Dred); text-align: center;">Share this link and encourage your friends to donate:</p>
            <div class="share-link">
                <input type="text" id="share-url" value="http://localhost/MedAlert/index.php" readonly>
                <button class="btn" onclick="copyLink()">Copy Link</button>
            </div>
            <div class="social-share-buttons">
                <a href="https://facebook.com/sharer/sharer.php?u=https://MedAlert.com/donate" target="_blank" class="btn facebook-share">
                    <i class="fab fa-facebook-f"></i> Share on Facebook
                </a>
                <a href="https://twitter.com/intent/tweet?url=https://MedAlert.com/donate&text=Join%20me%20in%20donating%20blood!" target="_blank" class="btn twitter-share">
                    <i class="fab fa-twitter"></i> Share on Twitter
                </a>
                <a href="https://www.instagram.com/" target="_blank" class="btn instagram-share">
                    <i class="fab fa-instagram"></i> Share on Instagram
                </a>
                <a href="https://api.whatsapp.com/send?text=Join%20me%20in%20donating%20blood!%20https://MedAlert.com/donate" target="_blank" class="btn whatsapp-share">
                    <i class="fab fa-whatsapp"></i> Share on WhatsApp
                </a>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; 2024 MedAlert. All Rights Reserved.</p>
        <p><a href="#">Terms of Service</a></p>
    </footer>
</body>
<script src="Script/script.js"></script>
<script src="Script/slider.js"></script>
<script>
    function copyLink() {
        const linkInput = document.getElementById('share-url');
        linkInput.select();
        linkInput.setSelectionRange(0, 99999);
        document.execCommand('copy');
        alert('Link copied to clipboard!');
    }
</script>
</html>