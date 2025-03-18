<?php

require "../config.php";
require_once '../functions.php';

$user_id = $_SESSION['user_id'] ?? 0;

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $product = getProductById($conn, $product_id);

    if (!$product) {
        echo "Error: No Product Found";
        exit;
    }
} else {
    echo "Error: No Product Found";
    exit;
}

$available_brands = getBrands($conn);
$available_sizes = getSizes($conn);
$available_colors = getColors($conn);
$rating_data = getProductRating($conn, $product_id);
$feedbacks = getProductFeedback($conn, $product_id);

$avg_rating = $rating_data['avg_rating'];
$review_count = $rating_data['review_count'];
$feedback_count = count($feedbacks);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include '../includes/head.php'; ?>
</head>

<body>
    <!-- Topbar Start -->
    <?php include '../includes/topbar.php'; ?>
    <!-- Topbar End -->

    <!-- Navbar Start -->
    <?php include '../includes/navbar.php'; ?>
    <!-- Navbar End -->

    <!-- Breadcrumb -->
    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-12">
                <nav class="breadcrumb bg-light mb-30">
                    <a class="breadcrumb-item text-dark" href="index.php">Home</a>
                    <a class="breadcrumb-item text-dark" href="shop.php">Shop</a>
                    <span class="breadcrumb-item active"><?php echo htmlspecialchars($product['name']); ?></span>
                </nav>
            </div>
        </div>
    </div>

    <!-- Shop Detail Start -->
    <div class="container-fluid pb-5">
        <div class="row px-xl-5">
            <div class="col-lg-5 mb-30">
                <div id="product-carousel" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner bg-light">
                        <div class="carousel-item active">
                            <img class="w-100 h-100" src="../<?php echo $product['image_url']; ?>"
                                alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </div>
                    </div>
                    <a class="carousel-control-prev" href="#product-carousel" data-slide="prev">
                        <i class="fa fa-2x fa-angle-left text-dark"></i>
                    </a>
                    <a class="carousel-control-next" href="#product-carousel" data-slide="next">
                        <i class="fa fa-2x fa-angle-right text-dark"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-7 h-auto mb-30">
                <div class="h-100 bg-light p-30">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <div class="d-flex mb-3">
                        <!-- Hiển thị rating sao -->
                        <div class="text-primary mr-2">
                            <?php
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= floor($avg_rating)) {
                                    echo '<small class="fas fa-star"></small>'; // Sao đầy
                                } elseif ($i - 0.5 <= $avg_rating && $avg_rating < $i) {
                                    echo '<small class="fas fa-star-half-alt"></small>'; // Nửa sao
                                } else {
                                    echo '<small class="far fa-star"></small>'; // Sao rỗng
                                }
                            }
                            ?>
                        </div>
                        <small class="pt-1">(<?php echo $feedback_count; ?> Reviews)</small>
                    </div>
                    <h3 class="font-weight-semi-bold mb-4">$<?php echo number_format($product['price'], 2); ?></h3>
                    <label><strong class="text-dark mr-3">In stock:</strong></label>
                    <p class="mb-4" style="display: inline;"><?php echo htmlspecialchars($product['inventory']); ?></p>
                    <p class="mb-4"><?php echo htmlspecialchars($product['description']); ?></p>
                    <!-- Form chọn size và colors gửi đến cart.php-->
                    <form method="get" action="cart.php">
                        <div class="d-flex mb-3">
                            <strong class="text-dark mr-3">Sizes:</strong>
                            <div>
                                <?php if (!empty($available_sizes)) : ?>
                                    <?php foreach ($available_sizes as $size): ?>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" class="custom-control-input"
                                                id="size-<?php echo $size['id']; ?>"
                                                name="size_id"
                                                value="<?php echo $size['id']; ?>"
                                                <?php echo ($size['id'] == $product['size_id']) ? 'checked' : ''; ?> required>
                                            <label class="custom-control-label" for="size-<?php echo $size['id']; ?>">
                                                <?php echo htmlspecialchars($size['name']); ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted">No sizes available</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="d-flex mb-4">
                            <strong class="text-dark mr-3">Colors:</strong>
                            <div>
                                <?php if (!empty($available_colors)) : ?>
                                    <?php foreach ($available_colors as $color): ?>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" class="custom-control-input"
                                                id="color-<?php echo $color['id']; ?>"
                                                name="color_id"
                                                value="<?php echo $color['id']; ?>"
                                                <?php echo ($color['id'] == $product['color_id']) ? 'checked' : ''; ?> required>
                                            <label class="custom-control-label" for="color-<?php echo $color['id']; ?>">
                                                <?php echo htmlspecialchars($color['name']); ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted">No colors available</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <script>
                            document.querySelectorAll('.custom-control').forEach(el => el.style.display = el.querySelector('input:checked') ? '' : 'none');
                        </script>

                        <div class="d-flex align-items-center mb-4 pt-2">
                            <div class="input-group quantity mr-3" style="width: 130px;">
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-primary btn-minus">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                                <input type="text" class="form-control bg-secondary border-0 text-center"
                                    name="quantity" value="1" id="quantity" readonly
                                    min="1" max="<?php echo $product['inventory']; ?>">
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-primary btn-plus">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" class="btn btn-primary px-3">
                                <i class="fa fa-shopping-cart mr-1"></i> Add To Cart
                            </button>
                        </div>
                    </form>

                    <!--  js cho nút tăng/giảm số lượng -->
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            document.querySelectorAll(".quantity").forEach(function(quantityWrapper) {
                                let quantityInput = quantityWrapper.querySelector("#quantity");
                                let minusButton = quantityWrapper.querySelector(".btn-minus");
                                let plusButton = quantityWrapper.querySelector(".btn-plus");
                                let maxStock = parseInt(quantityInput.getAttribute("max"));

                                plusButton.addEventListener("click", function() {
                                    let currentQuantity = parseInt(quantityInput.value);
                                    if (currentQuantity < maxStock) {
                                        quantityInput.value = currentQuantity + 1;
                                    }
                                });

                                minusButton.addEventListener("click", function() {
                                    let currentQuantity = parseInt(quantityInput.value);
                                    if (currentQuantity > 1) {
                                        quantityInput.value = currentQuantity - 1;
                                    }
                                });
                            });
                        });
                    </script>
                </div>
            </div>
        </div>

        <div class="row px-xl-5">
            <div class="col">
                <div class="bg-light p-30">
                    <div class="nav nav-tabs mb-4">
                        <a class="nav-item nav-link text-dark active" data-toggle="tab" href="#tab-pane-1">Description</a>
                        <a class="nav-item nav-link text-dark" data-toggle="tab" href="#tab-pane-3">Reviews (<?php echo $feedback_count; ?>)</a>
                    </div>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab-pane-1">
                            <h4 class="mb-3">Product Description</h4>
                            <p><?php echo htmlspecialchars($product['description']); ?></p>
                        </div>
                        <!-- Phần trang review -->
                        <div class="tab-pane fade" id="tab-pane-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4 class="mb-4"><?php echo $feedback_count; ?> review<?php echo $feedback_count !== 1 ? 's' : ''; ?> for "<?php echo htmlspecialchars($product['name']); ?>"</h4>
                                    <?php if (!empty($feedbacks)): ?>
                                        <?php foreach ($feedbacks as $feedback): ?>
                                            <div class="media mb-4">
                                                <!-- AVt user chưa có -->
                                                <img src="" alt="Image" class="img-fluid mr-3 mt-1" style="width: 45px;">
                                                <div class="media-body">
                                                    <h6><?php echo htmlspecialchars($feedback['user_name']); ?><small> - <i><?php echo date('d M Y', strtotime($feedback['created_at'])); ?></i></small></h6>
                                                    <div class="text-primary mb-2">
                                                        <?php
                                                        for ($i = 1; $i <= 5; $i++) {
                                                            if ($i <= floor($feedback['rating'])) {
                                                                echo '<i class="fas fa-star"></i>'; // Sao đầy
                                                            } elseif ($i - 0.5 <= $feedback['rating'] && $feedback['rating'] < $i) {
                                                                echo '<i class="fas fa-star-half-alt"></i>'; // Nửa sao
                                                            } else {
                                                                echo '<i class="far fa-star"></i>'; // Sao rỗng
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                    <p><?php echo htmlspecialchars($feedback['message']); ?></p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p>No reviews yet.</p>
                                    <?php endif; ?>
                                </div>

                                <!-- Phần Leave a review  -->
                                <div class="col-md-6">
                                    <h4 class="mb-4">Leave a review</h4>
                                    <div class="d-flex my-3">
                                        <p class="mb-0 mr-2">Your Rating * :</p>
                                        <div class="text-primary" id="rating-stars">
                                            <i class="far fa-star" data-value="1"></i>
                                            <i class="far fa-star" data-value="2"></i>
                                            <i class="far fa-star" data-value="3"></i>
                                            <i class="far fa-star" data-value="4"></i>
                                            <i class="far fa-star" data-value="5"></i>
                                            <input type="hidden" name="rating" id="rating-value" value="0">
                                        </div>
                                    </div>
                                    <!-- review gửi dến file nao -->
                                    <form id="review-form" method="POST" action=".php">
                                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                        <div class="form-group">
                                            <label for="message">Your Review *</label>
                                            <textarea id="message" name="message" cols="30" rows="5" class="form-control" required></textarea>
                                        </div>
                                        <div class="form-group mb-0">
                                            <input type="submit" value="Leave Your Review" class="btn btn-primary px-3">
                                        </div>
                                    </form>
                                    <div id="review-message" class="mt-2"></div>
                                </div>


                                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
                                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                                <script>
                                    $(document).ready(function() {
                                        // Xử lý chọn rating sao
                                        $('#rating-stars i').on('click', function() {
                                            var rating = $(this).data('value');
                                            $('#rating-value').val(rating);

                                            // Cập nhật giao diện sao
                                            $('#rating-stars i').each(function() {
                                                if ($(this).data('value') <= rating) {
                                                    $(this).removeClass('far fa-star').addClass('fas fa-star');
                                                } else {
                                                    $(this).removeClass('fas fa-star').addClass('far fa-star');
                                                }
                                            });
                                        });

                                        // Xử lý submit form bằng Ajax
                                        $('#review-form').on('submit', function(e) {
                                            e.preventDefault();
                                            var rating = $('#rating-value').val();
                                            if (rating == 0) {
                                                $('#review-message').html('<p class="text-danger">Please select a rating!</p>');
                                                return;
                                            }

                                            $.ajax({
                                                url: 'submit_review.php',
                                                type: 'POST',
                                                data: $(this).serialize(),
                                                success: function(response) {
                                                    var res = JSON.parse(response);
                                                    if (res.success) {
                                                        $('#review-message').html('<p class="text-success">' + res.message + '</p>');
                                                        $('#review-form')[0].reset();
                                                        $('#rating-stars i').removeClass('fas fa-star').addClass('far fa-star');
                                                        $('#rating-value').val(0);
                                                    } else {
                                                        $('#review-message').html('<p class="text-danger">' + res.message + '</p>');
                                                    }
                                                },
                                                error: function() {
                                                    $('#review-message').html('<p class="text-danger">Something went wrong. Please try again.</p>');
                                                }
                                            });
                                        });
                                    });
                                </script>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Shop Detail End -->

    <!-- Footer Start -->
    <?php include '../includes/footer.php'; ?>
    <!-- Footer End -->


    <!-- Back to Top -->
    <a href="#" class="btn btn-primary back-to-top"><i class="fa fa-angle-double-up"></i></a>


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