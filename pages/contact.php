<?php
require "../config.php";
require_once '../functions.php';
$user_id = $_SESSION['user_id'] ?? 0;

// Declare variables for error messages and input values
$nameError = $emailError = $subjectError = $messageError = '';
$successMessage = $errorMessage = '';
$name = $email = $subject = $message = '';

// Flag to track if the form is valid
$isValid = true;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    // Validate name
    if (empty($name)) {
        $nameError = "Please enter your name.";
        $isValid = false;
    }

    // Validate email
    if (empty($email)) {
        $emailError = "Please enter your email.";
        $isValid = false;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailError = "Please enter a valid email address.";
        $isValid = false;
    }

    // Validate subject
    if (empty($subject)) {
        $subjectError = "Please enter a subject.";
        $isValid = false;
    }

    // Validate message
    if (empty($message)) {
        $messageError = "Please enter your message.";
        $isValid = false;
    }

    // If the form is valid, insert data into the database
    if ($isValid) {
        // Sanitize the data to prevent SQL injection
        $name = $conn->real_escape_string($name);
        $email = $conn->real_escape_string($email);
        $subject = $conn->real_escape_string($subject);
        $message = $conn->real_escape_string($message);

        // Insert into database
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

    <?php include '../includes/head.php' ?>
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
        <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4"><span class="bg-secondary pr-3">Contact
                Us</span></h2>
        <div class="row px-xl-5">
            <div class="col-lg-7 mb-5">
                <div class="contact-form bg-light p-30">
                    <div id="success"></div>


                    <form name="sentMessage" id="contactForm" method="POST" action="" novalidate="novalidate">
                        <?php if ($successMessage): ?>
                        <span class="alert alert-success mb-0"><?php echo $successMessage; ?></span>
                        <br><br>
                        <?php endif; ?>
                        <div class="control-group">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Your Name"
                                required="required" value="<?php echo htmlspecialchars($name); ?>" />
                            <p class="help-block text-danger">
                                <?php echo isset($nameError) ? $nameError : ''; ?>
                            </p>
                        </div>
                        <div class="control-group">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Your Email"
                                required="required" value="<?php echo htmlspecialchars($email); ?>" />
                            <p class="help-block text-danger">
                                <?php echo isset($emailError) ? $emailError : ''; ?>
                            </p>
                        </div>
                        <div class="control-group">
                            <input type="text" class="form-control" id="subject" name="subject" placeholder="Subject"
                                required="required" value="<?php echo htmlspecialchars($subject); ?>" />
                            <p class="help-block text-danger">
                                <?php echo isset($subjectError) ? $subjectError : ''; ?>
                            </p>
                        </div>
                        <div class="control-group">
                            <textarea class="form-control" rows="8" id="message" name="message" style='resize:none;'
                                placeholder="Message"
                                required="required"><?php echo htmlspecialchars($message); ?></textarea>
                            <p class="help-block text-danger">
                                <?php echo isset($messageError) ? $messageError : ''; ?>
                            </p>
                        </div>
                        <div class="d-flex justify-content-between">
                            <button class="btn btn-primary py-2 px-4" type="submit" id="sendMessageButton">Send
                                Message</button>
                            <!-- <?php if ($successMessage): ?>
                    <span class="alert alert-success mb-0"><?php echo $successMessage; ?></span>
                <?php endif; ?> -->
                        </div>
                    </form>

                    <!-- Display error message below the form if necessary -->
                    <?php if ($errorMessage): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> <?php echo $errorMessage; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php endif; ?>


                </div>
            </div>
            <div class="col-lg-5 mb-5">
                <div class="bg-light p-30 mb-30">
                    <iframe style="width: 100%; height: 250px;"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3001156.4288297426!2d-78.01371936852176!3d42.72876761954724!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4ccc4bf0f123a5a9%3A0xddcfc6c1de189567!2sNew%20York%2C%20USA!5e0!3m2!1sen!2sbd!4v1603794290143!5m2!1sen!2sbd"
                        frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
                </div>
                <div class="bg-light p-30 mb-3">
                    <p class="mb-2"><i class="fa fa-map-marker-alt text-primary mr-3"></i>123 Street, New York, USA</p>
                    <p class="mb-2"><i class="fa fa-envelope text-primary mr-3"></i>info@example.com</p>
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

    <!-- Contact Javascript File -->
    <script src="mail/jqBootstrapValidation.min.js"></script>
    <script src="mail/contact.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>