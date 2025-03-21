<?php
require "../config.php";
require_once '../functions.php';
$user_id = $_SESSION['user_id'] ?? 0;

// Declare variables for error messages and input values
$nameError = $emailError = $subjectError = $messageError = '';
$successMessage = $errorMessage = '';
$name = $email = $subject = $message = '';
$isValid = true;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if (empty($name)) {
        $nameError = "Please enter your name.";
        $isValid = false;
    }
    if (empty($email)) {
        $emailError = "Please enter your email.";
        $isValid = false;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailError = "Please enter a valid email address.";
        $isValid = false;
    }
    if (empty($subject)) {
        $subjectError = "Please enter a subject.";
        $isValid = false;
    }
    if (empty($message)) {
        $messageError = "Please enter your message.";
        $isValid = false;
    }

    if ($isValid) {
        $name = $conn->real_escape_string($name);
        $email = $conn->real_escape_string($email);
        $subject = $conn->real_escape_string($subject);
        $message = $conn->real_escape_string($message);

        $sql = "INSERT INTO contact (name, email, subject, message) VALUES ('$name', '$email', '$subject', '$message')";
        if ($conn->query($sql) === TRUE) {
            $successMessage = "Your message has been sent successfully!";
            $name = $email = $subject = $message = '';
        } else {
            $errorMessage = "There was an issue with your submission. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include '../includes/head.php'; ?>
    <style>
        .map-container {
            width: 94%;
            margin: 0 auto 30px auto;
        }
        .map-container iframe {
            width: 100%;
            height: 600px;
            border: 0;
        }
        @media (max-width: 991px) {
            .map-container {
                width: 100%;
            }
            .map-container iframe {
                height: 400px;
            }
        }
    </style>
</head>

<body>
    <!-- Topbar Start -->
    <?php include '../includes/topbar.php' ?>
    <!-- Topbar End -->

    <!-- Navbar Start -->
    <?php include '../includes/navbar.php' ?>
    <!-- Navbar End -->

    <!-- Breadcrumb Start -->
    <?php include '../includes/breadcumb.php' ?>
    <!-- Breadcrumb End -->

    <!-- Contact Start -->
    <div class="container-fluid">
        <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4">
            <span class="bg-secondary pr-3">Contact Us</span>
        </h2>

        <div class="map-container">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7449.480496831259!2d105.84744037387541!3d21.003046888663874!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ac748cee8b8d%3A0x2916773f4229fd1d!2zMTkgUC4gTMOqIFRoYW5oIE5naOG7iywgQuG6oWNoIE1haSwgSGFpIELDoCBUcsawbmcsIEjDoCBO4buZaSAxMTYxOCwgVmnhu4d0IE5hbQ!5e0!3m2!1svi!2sus!4v1742563231496!5m2!1svi!2sus" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>   
        </div>

        <div class="row px-xl-5">
            <!-- Form liên hệ-->
            <div class="col-lg-6 mb-5">
                <div class="contact-form bg-light p-30">
                    <form name="sentMessage" id="contactForm" method="POST" action="" novalidate="novalidate">
                        <?php if ($successMessage): ?>
                            <span class="alert alert-success mb-0"><?php echo $successMessage; ?></span><br><br>
                        <?php endif; ?>
                        <div class="control-group">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Your Name" required="required" value="<?php echo htmlspecialchars($name); ?>" />
                            <p class="help-block text-danger"><?php echo isset($nameError) ? $nameError : ''; ?></p>
                        </div>
                        <div class="control-group">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Your Email" required="required" value="<?php echo htmlspecialchars($email); ?>" />
                            <p class="help-block text-danger"><?php echo isset($emailError) ? $emailError : ''; ?></p>
                        </div>
                        <div class="control-group">
                            <input type="text" class="form-control" id="subject" name="subject" placeholder="Subject" required="required" value="<?php echo htmlspecialchars($subject); ?>" />
                            <p class="help-block text-danger"><?php echo isset($subjectError) ? $subjectError : ''; ?></p>
                        </div>
                        <div class="control-group">
                            <textarea class="form-control" rows="8" id="message" name="message" style="resize:none;" placeholder="Message" required="required"><?php echo htmlspecialchars($message); ?></textarea>
                            <p class="help-block text-danger"><?php echo isset($messageError) ? $messageError : ''; ?></p>
                        </div>
                        <div class="d-flex justify-content-between">
                            <button class="btn btn-primary py-2 px-4" type="submit" id="sendMessageButton">Send Message</button>
                        </div>
                    </form>
                    <?php if ($errorMessage): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> <?php echo $errorMessage; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Thông tin liên hệ -->
            <div class="col-lg-6 mb-5">
                <div class="bg-light p-30">
                    <p class="mb-2"><i class="fa fa-map-marker-alt text-primary mr-3"></i>19 Le Thanh Nghi, Ha Noi, Viet Nam</p>
                    <p class="mb-2"><i class="fa fa-envelope text-primary mr-3"></i>bronxluggage@example.com</p>
                    <p class="mb-2"><i class="fa fa-phone-alt text-primary mr-3"></i>+012 345 67890</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Contact End -->

    <!-- Footer Start -->
    <?php include '../includes/footer.php' ?>
    <!-- Footer End -->

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="mail/jqBootstrapValidation.min.js"></script>
    <script src="mail/contact.js"></script>
    <script src="js/main.js"></script>
</body>

</html>