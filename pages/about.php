<?php
// Include configuration and utility functions
require "../config.php";
require_once '../functions.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include '../includes/head.php'; // Includes meta tags, title, and CSS links ?>
</head>

<body>
    <!-- Topbar Start-->
    <?php include '../includes/topbar.php'; ?>
    <!-- Topbar End -->

    <!-- Navbar Start -->
    <?php include '../includes/navbar.php'; ?>
    <!-- Navbar End -->

    <!-- Breadcrumb Start -->
    <?php include '../includes/breadcumb.php'; ?>
    <!-- Breadcrumb End -->

    <!-- About Us Start: Main content section for the About Us page -->
    <div class="container-fluid">
        <!-- Section Title: Styled heading for the About Us page -->
        <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4">
            <span class="bg-secondary pr-3">About Us</span>
        </h2>
        <div class="row px-xl-5">
            <!-- Hero Section: Introduction to Bronx Luggage -->
            <div class="col-12 mb-5">
                <div class="bg-light p-30 text-center">
                    <h1 class="mb-3">The Legacy of Bronx Luggage</h1>
                    <p class="lead" style="color: #3D464D;">
                        Bronx Luggage was born from an enduring passion for exploration. We believe that every suitcase
                        is more than a mere possession—it is a timeless companion, carrying memories across generations.
                        Nestled within the vibrant Festival Marketplace, we embarked on a journey to curate exceptional
                        luggage that blends durability with elegance for the discerning traveler.
                    </p>
                </div>
            </div>

            <!-- Mission and Vision Section -->
            <div class="col-lg-6 mb-5">
                <div class="bg-light p-30">
                    <!-- Mission Subsection -->
                    <h4 class="mb-2" style="font-weight: 600; color: #3D464D;">Our Mission</h4>
                    <p class="mb-4">
                        To craft a legacy of superior luggage—timeless, resilient, and exquisitely designed—for every
                        voyage you undertake. At Bronx Luggage, we are devoted to elevating the art of travel, ensuring
                        that each piece we offer transcends mere utility to become a cherished emblem of your journeys.
                        Our commitment lies in blending unparalleled craftsmanship with a passion for exploration,
                        delivering products that withstand the test of time and inspire wanderlust in every soul.
                    </p>
                    <!-- Vision Subsection -->
                    <h4 class="mb-2" style="font-weight: 600; color: #3D464D;">Our Vision</h4>
                    <p class="mb-4">
                        To stand as the premier destination for those who seek perfection in every step of their
                        travels. We aspire to redefine the travel experience by curating a collection that embodies
                        elegance, durability, and innovation. From the seasoned adventurer to the meticulous
                        professional, our vision is to be the trusted companion that transforms each trip into a
                        masterpiece of style and function, setting a new standard in the world of luxury luggage.
                    </p>
                    <!-- Quote Block: -->
                    <div class="mt-4 text-center">
                        <blockquote class="blockquote" style="border-left: 4px solid #ffc107; padding-left: 15px;">
                            <p class="mb-0" style="font-style: italic; color: #555;">
                                “Travel is not just a journey—it’s an expression of who we are. Our luggage reflects
                                that truth.”
                            </p>
                        </blockquote>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-5 d-flex align-items-center justify-content-center">
                <!-- Image-->
                <div style="background-color: #f8f9fa; padding: 10px;">
                    <img src="../img/store.jpg" class="img-fluid" style="max-height: 500px; object-fit: cover;"
                        alt="Store">
                </div>
            </div>

            <!-- What Sets Us Apart Section:  -->
            <div class="col-12 mb-5">
                <div class="bg-light p-30">
                    <h3 class="mb-3 text-center">What Sets Us Apart</h3>
                    <div class="row text-center">
                        <div class="col-md-4">
                            <i class="fa fa-suitcase fa-2x text-primary mb-3"></i>
                            <p><strong>Unrivaled Variety</strong><br>A curated collection spanning affordable elegance
                                to the finest luxury brands.</p>
                        </div>
                        <div class="col-md-4">
                            <i class="fa fa-clock fa-2x text-primary mb-3"></i>
                            <p><strong>A Harmony of Heritage and Modernity</strong><br>Luggage that endures through
                                time, adorned with contemporary sophistication.</p>
                        </div>
                        <div class="col-md-4">
                            <i class="fa fa-star fa-2x text-primary mb-3"></i>
                            <p><strong>An Artful Experience</strong><br>Each piece we offer whispers a story, awaiting
                                your discovery.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Traveler’s Experience Section: -->
            <div class="col-lg-6 mb-5 d-flex align-items-center justify-content-center">
                <!-- Image Wrapper -->
                <div style="background-color: #f8f9fa; padding: 10px;">
                    <img src="../img/travel.jpg" class="img-fluid" style="max-height: 350px; object-fit: cover;"
                        alt="Traveler">
                </div>
            </div>
            <div class="col-lg-6 mb-5">
                <div class="bg-light p-30">
                    <h3 class="mb-3">The Traveler’s Experience</h3>
                    <p>At Bronx Luggage, we hold that every suitcase embodies a unique narrative. Whether you’re an
                        adventurer traversing distant horizons or a professional embarking on a journey of purpose, we
                        are here to guide you toward the perfect companion for your tale.</p>
                    <blockquote class="blockquote mt-3">
                        <p class="mb-0" style="font-style: italic;">“I discovered my cherished suitcase at Bronx
                            Luggage—a steadfast partner through five wondrous realms.”</p>
                        <footer class="blockquote-footer">A Devoted Traveler</footer>
                    </blockquote>
                </div>
            </div>

            <!-- Journey in Imagery Section:  -->
            <div class="col-12 mb-5">
                <div class="bg-light p-30">
                    <div class="row">
                        <div class="col-md-3">
                            <img src="../img/gallary1.png" class="img-fluid mb-3" style="object-fit: cover;"
                                alt="Gallery 1">
                        </div>
                        <div class="col-md-3">
                            <img src="../img/gallary2.png" class="img-fluid mb-3" style="object-fit: cover;"
                                alt="Gallery 2">
                        </div>
                        <div class="col-md-3">
                            <img src="../img/gallary3.png" class="img-fluid mb-3" style="object-fit: cover;"
                                alt="Gallery 3">
                        </div>
                        <div class="col-md-3">
                            <img src="../img/gallary4.png" class="img-fluid mb-3" style="object-fit: cover;"
                                alt="Gallery 4">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Link to shop.php-->
            <div class="col-12 mb-5 text-center">
                <div class="bg-light p-30">
                    <h3 class="mb-3">An Invitation to Explore</h3>
                    <p>Step into our world and uncover a collection crafted for your next odyssey. Begin your journey
                        with Bronx Luggage today.</p>
                    <a href="../pages/shop.php" class="btn btn-primary py-2 px-4">Explore Our Collection</a>
                </div>
            </div>
        </div>
    </div>
    <!-- About Us End -->

    <!-- Footer Start  -->
    <?php include '../includes/footer.php'; ?>
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