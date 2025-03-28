<?php
require "../config.php";
require_once '../functions.php';

$user_id = $_SESSION['user_id'] ?? 0;

// Lấy thông tin sản phẩm
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


// Lấy dữ liệu khác
$available_brands = getBrands($conn);
$available_sizes = getSizes($conn);
$available_colors = getColors($conn);
$rating_data = getProductRating($conn, $product_id);
$feedbacks = getProductFeedback($conn, $product_id);
$product_images = getProductImages($conn, $product_id);

$avg_rating = $rating_data['avg_rating'];
$review_count = $rating_data['review_count'];
$feedback_count = count($feedbacks);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include '../includes/head.php'; ?>
    <!-- CSS để thêm gạch chéo đỏ trong hình tròn radio button -->
    <style>
        .custom-control-input:disabled~.custom-control-label {
            color: #6c757d;
            /* Màu xám nhạt để biểu thị không khả dụng */
            cursor: not-allowed;
        }

        .unavailable .custom-control-input:disabled::before {
            position: relative;
            content: '';
            display: inline-block;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, transparent 45%, red 45%, red 55%, transparent 55%);
            /* Tạo đường chéo đỏ trong hình tròn */
            border-radius: 50%;
            pointer-events: none;
        }

        .unavailable .custom-control-input:disabled:checked::before {
            background: none;
            /* Xóa gạch chéo nếu radio được chọn (trường hợp đặc biệt) */
        }

        /* CSS Star Rating */
        .rating-stars {
            cursor: pointer;
        }

        .stars-container i {
            margin-right: 5px;
        }

        .rating-stars:hover i,
        .rating-stars i.active {
            color: #ffc107 !important;
        }

        /* CSS Cart Message */
        .cart-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #4CAF50;
            /* Màu xanh lá cho thông báo thành công */
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            font-size: 16px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            transition: opacity 0.5s ease-in-out;
        }

        .cart-alert.error {
            background-color: #FF3D00;
            /* Màu đỏ cho thông báo lỗi */
        }

        /* CSS cho container chứa review */
        .review-container {
            max-height: 400px;
            /* Chiều cao tối đa để hiển thị khoảng 4-5 feedback */
            overflow-y: auto;
            /* Thêm thanh cuộn dọc khi nội dung vượt quá chiều cao */
            padding-right: 10px;
            /* Khoảng cách bên phải để thanh cuộn không che nội dung */
        }

        /* Tùy chỉnh thanh cuộn (tùy chọn) để đẹp hơn */
        .review-container::-webkit-scrollbar {
            width: 8px;
            /* Chiều rộng thanh cuộn */
        }

        .review-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            /* Màu nền của thanh cuộn */
            border-radius: 4px;
        }

        .review-container::-webkit-scrollbar-thumb {
            background: #888;
            /* Màu của thanh kéo */
            border-radius: 4px;
        }

        .review-container::-webkit-scrollbar-thumb:hover {
            background: #555;
            /* Màu khi hover */
        }
    </style>
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
                    <a class="breadcrumb-item text-dark" href="../pages/index.php">Home</a>
                    <a class="breadcrumb-item text-dark" href="../pages/shop.php">Shop</a>
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
                        <div class="carousel-inner bg-light">
                            <?php foreach ($product_images as $index => $image) : ?>
                                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                    <img class="d-block w-100" style="height: 700px; object-fit: cover;"
                                        src="../<?php echo $image['image_url']; ?>"
                                        alt="<?php echo htmlspecialchars($product['name']); ?>">
                                </div>
                            <?php endforeach; ?>
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
                    <!-- Start add to cart-->
                    <?php if (isset($_SESSION['cart_message'])): ?>
                        <div id="cart-message" class="cart-alert">
                            <?php
                            echo $_SESSION['cart_message'];
                            unset($_SESSION['cart_message']); // Xóa session sau khi hiển thị
                            ?>
                        </div>
                        <script>
                            setTimeout(function() {
                                var messageBox = document.getElementById("cart-message");
                                if (messageBox) {
                                    messageBox.style.opacity = "0"; // Làm mờ dần
                                    setTimeout(() => messageBox.style.display = "none", 500); // Ẩn hẳn sau 0.5s
                                }
                            }, 3000);
                        </script>
                    <?php endif; ?>
                    <!-- End add to cart message -->

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
                    <p class="mb-4"><?php echo htmlspecialchars($product['description']); ?></p>
                    <div class="d-flex mb-3">
                        <strong class="text-dark mr-3">Sizes:</strong>
                        <div>
                            <?php if (!empty($available_sizes)) : ?>
                                <?php foreach ($available_sizes as $size): ?>
                                    <div
                                        class="custom-control custom-radio custom-control-inline <?php echo ($size['id'] != $product['size_id']) ? 'unavailable' : ''; ?>">
                                        <input type="radio" class="custom-control-input"
                                            id="size-<?php echo $size['id']; ?>" name="size_id"
                                            value="<?php echo $size['id']; ?>"
                                            <?php echo ($size['id'] == $product['size_id']) ? 'checked' : 'disabled'; ?>
                                            required>
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
                                    <div
                                        class="custom-control custom-radio custom-control-inline <?php echo ($color['id'] != $product['color_id']) ? 'unavailable' : ''; ?>">
                                        <input type="radio" class="custom-control-input"
                                            id="color-<?php echo $color['id']; ?>" name="color_id"
                                            value="<?php echo $color['id']; ?>"
                                            <?php echo ($color['id'] == $product['color_id']) ? 'checked' : 'disabled'; ?>
                                            required>
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
                    <!-- Form chọn size và colors gửi đến cart.php-->
                    <form method="POST" action="../actions/add_to_cart.php">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <div class="d-flex align-items-center mb-4 pt-2">
                            <div class="input-group quantity mr-3" style="width: 130px;">
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-primary btn-minus">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                                <input type="text" class="form-control bg-secondary border-0 text-center"
                                    name="quantity" value="1" id="quantity" readonly min="1">
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-primary btn-plus">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary px-3">
                                <i class="fa fa-cart-plus mr-1"></i> Add To Cart
                            </button>
                            <a href="../pages/cart.php" class="btn btn-success px-3 ml-3">
                                <i class="fa fa-shopping-cart mr-1"></i> View Cart
                            </a>
                        </div>
                    </form>

                    <a href="../pages/shop.php">Back to Shop</a>


                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const quantityInput = document.getElementById("quantity");

                            document.querySelector(".btn-minus").addEventListener("click", function() {
                                let currentValue = parseInt(quantityInput.value);
                                if (currentValue > 1) {
                                    quantityInput.value = currentValue - 1;
                                }
                            });

                            document.querySelector(".btn-plus").addEventListener("click", function() {
                                let currentValue = parseInt(quantityInput.value);
                                quantityInput.value = currentValue + 1;
                            });
                        });
                    </script>
                </div>
            </div>
        </div>

        <!-- Review and description -->
        <div class="row px-xl-5">
            <div class="col">
                <div class="bg-light p-30">
                    <div class="nav nav-tabs mb-4">
                        <a class="nav-item nav-link text-dark active" data-toggle="tab"
                            href="#tab-pane-1">Description</a>
                        <a class="nav-item nav-link text-dark" data-toggle="tab" href="#tab-pane-3">Reviews
                            (<?php echo $feedback_count; ?>)</a>
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
                                        <div class="review-container">
                                            <?php foreach ($feedbacks as $feedback): ?>
                                                <div class="media mb-4">
                                                    <!-- AVT user chưa có -->
                                                    <img src="../img/john-doe.png" alt="Image" class="img-fluid mr-3 mt-1" style="width: 45px;">
                                                    <div class="media-body">
                                                        <h6><?php echo htmlspecialchars($feedback['user_name']); ?><small> - <i><?php echo date('d M Y', strtotime($feedback['created_at'])); ?></i></small></h6>
                                                        <div class="text-primary mb-2">
                                                            <?php for ($i = 0; $i < 5; $i++): ?>
                                                                <?php if ($i < floor($feedback['rating'])): ?>
                                                                    <small class="fa fa-star text-primary mr-1"></small>
                                                                <?php elseif ($i < $feedback['rating']): ?>
                                                                    <small class="fa fa-star-half-alt text-primary mr-1"></small>
                                                                <?php else: ?>
                                                                    <small class="far fa-star text-primary mr-1"></small>
                                                                <?php endif; ?>
                                                            <?php endfor; ?>
                                                        </div>
                                                        <p><?php echo htmlspecialchars($feedback['message']); ?></p>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <p>No reviews yet.</p>
                                    <?php endif; ?>
                                </div>

                                <!-- Phần Leave a review  -->
                                <div class="col-md-6">
                                    <h4 class="mb-4">Leave a review</h4>
                                    <?php if (isset($_SESSION['error'])): ?>
                                        <div class="alert alert-danger">
                                            <?php
                                            echo $_SESSION['error'];
                                            unset($_SESSION['error']); // Xóa thông báo sau khi hiển thị
                                            ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (isset($_SESSION['success'])): ?>
                                        <div class="alert alert-success">
                                            <?php
                                            echo $_SESSION['success'];
                                            unset($_SESSION['success']); // Xóa thông báo sau khi hiển thị
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!$user_id): ?>
                                        <div class="alert alert-warning">Please <a href="../account/login.php">login</a> to
                                            leave a review.</div>
                                    <?php else: ?>
                                        <div class="d-flex my-3">
                                            <p class="mb-0 mr-2">Your Rating * :</p>
                                            <div class="text-primary" id="star-rating">
                                                <i class="far fa-star" data-value="1"></i>
                                                <i class="far fa-star" data-value="2"></i>
                                                <i class="far fa-star" data-value="3"></i>
                                                <i class="far fa-star" data-value="4"></i>
                                                <i class="far fa-star" data-value="5"></i>
                                            </div>
                                        </div>
                                        <form action="../actions/submit_review.php" method="POST">
                                            <input type="hidden" name="product_id" value="<?= $product_id ?>">
                                            <input type="hidden" name="rating" id="rating" value="0">

                                            <div class="form-group">
                                                <label for="message">Your Review *</label>
                                                <textarea id="message" name="message" cols="30" rows="5"
                                                    class="form-control" required></textarea>
                                            </div>
                                            <div class="form-group mb-0">
                                                <input type="submit" value="Leave Your Review" class="btn btn-primary px-3">
                                            </div>
                                        </form>
                                    <?php endif; ?>
                                </div>
                                <script>
                                    let selectedRating = 0;
                                    document.querySelectorAll("#star-rating i").forEach(star => {
                                        star.addEventListener("click", function() {
                                            selectedRating = this.getAttribute("data-value");
                                            document.getElementById("rating").value = selectedRating;
                                            updateStars(selectedRating);
                                        });

                                        star.addEventListener("mouseover", function() {
                                            updateStars(this.getAttribute("data-value"));
                                        });

                                        star.addEventListener("mouseout", function() {
                                            updateStars(selectedRating);
                                        });
                                    });

                                    function updateStars(rating) {
                                        document.querySelectorAll("#star-rating i").forEach(star => {
                                            star.classList.toggle("fas", star.getAttribute("data-value") <= rating);
                                            star.classList.toggle("far", star.getAttribute("data-value") > rating);
                                        });
                                    }
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