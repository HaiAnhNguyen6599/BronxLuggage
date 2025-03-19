<?php
require "../config.php"; // File kết nối database
require_once '../functions.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../account/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Xử lý cập nhật số lượng sản phẩm bằng AJAX
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cart_id']) && isset($_POST['quantity'])) {
    $cart_id = $_POST['cart_id'];
    $quantity = $_POST['quantity'];

    if ($quantity > 0) {
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("iii", $quantity, $cart_id, $user_id);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cart_id, $user_id);
        $stmt->execute();
    }

    // Lấy tổng tiền mới và tổng tiền từng sản phẩm
    $stmt = $conn->prepare("SELECT products.price, cart.quantity FROM cart JOIN products ON cart.product_id = products.id WHERE cart.id = ? AND cart.user_id = ?");
    $stmt->bind_param("ii", $cart_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    $item_total = $item ? $item['price'] * $item['quantity'] : 0;

    $stmt = $conn->prepare("SELECT SUM(products.price * cart.quantity) as total FROM cart JOIN products ON cart.product_id = products.id WHERE cart.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $total_price = $result->fetch_assoc()['total'] ?? 0;

    echo json_encode(["success" => true, "item_total" => number_format($item_total, 2), "total_price" => number_format($total_price, 2)]);
    exit();
}

// Lấy danh sách sản phẩm trong giỏ hàng
$stmt = $conn->prepare("SELECT cart.id as cart_id, products.id, products.name, products.price, products.inventory, cart.quantity FROM cart JOIN products ON cart.product_id = products.id WHERE cart.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);

$total_price = array_reduce($cart_items, function ($sum, $item) {
    return $sum + ($item['price'] * $item['quantity']);
}, 0);


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
    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-12">
                <nav class="breadcrumb bg-light mb-30">
                    <a class="breadcrumb-item text-dark" href="#">Home</a>
                    <a class="breadcrumb-item text-dark" href="#">Shop</a>
                    <span class="breadcrumb-item active">Shopping Cart</span>
                </nav>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->


    <!-- Cart Start -->

    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-lg-8 table-responsive mb-5">
                <table class="table table-light table-borderless table-hover mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-left">Products</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Remove</th>
                        </tr>
                    </thead>
                    <tbody class="align-middle" id="cart-items">
                        <?php foreach ($cart_items as $item): ?>
                            <tr data-cart-id="<?php echo $item['cart_id']; ?>">
                                <td class="align-middle text-left">
                                    <a href="product.php?id=<?php echo $item['id']; ?>">
                                        <?php echo htmlspecialchars($item['name']); ?>
                                    </a>
                                </td>
                                <td class="align-middle">$<?php echo number_format($item['price'], 2); ?></td>
                                <td class="align-middle">
                                    <div class="input-group quantity mx-auto" style="width: 100px;">
                                        <div class="input-group-btn">
                                            <button class="btn btn-sm btn-primary btn-minus">-</button>
                                        </div>
                                        <input type="text"
                                            class="form-control form-control-sm bg-secondary border-0 text-center quantity-input"
                                            value="<?php echo $item['quantity']; ?>">
                                        <div class="input-group-btn">
                                            <button class="btn btn-sm btn-primary btn-plus">+</button>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">$<span
                                        class="item-total"><?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                </td>
                                <td class="align-middle"><button class="btn btn-sm btn-danger btn-remove"><i
                                            class="fa fa-times"></i></button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-4">
                <h5 class="section-title position-relative text-uppercase mb-3"><span class="bg-secondary pr-3">Cart
                        Summary</span></h5>
                <div class="bg-light p-30 mb-5">
                    <div class="pt-2">
                        <div class="d-flex justify-content-between mt-2">
                            <h5>Total</h5>
                            <h5 id="total-price">$<?php echo number_format($total_price, 2); ?></h5>
                        </div>
                        <a href="checkout.php" class="btn btn-block btn-primary font-weight-bold my-3 py-3">Proceed To
                            Checkout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Cart End -->

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function updateCart(cartId, quantity, row) {
                fetch("cart.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: `cart_id=${cartId}&quantity=${quantity}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (quantity === 0) {
                                row.remove(); // Xóa hàng nếu số lượng là 0
                            } else {
                                row.querySelector(".item-total").textContent = `${data.item_total}`;
                            }
                            document.getElementById("total-price").textContent = `${data.total_price}`;
                        }
                    });
            }

            document.querySelectorAll(".quantity-input").forEach(input => {
                input.addEventListener("input", function() {
                    const row = this.closest("tr");
                    const cartId = row.dataset.cartId;
                    const newQuantity = parseInt(this.value) || 1;
                    updateCart(cartId, newQuantity, row);
                });
            });

            document.querySelectorAll(".btn-minus").forEach(button => {
                button.addEventListener("click", function() {
                    const input = this.closest(".quantity").querySelector(".quantity-input");
                    let value = parseInt(input.value) || 1;
                    if (value > 1) {
                        input.value = --value;
                        input.dispatchEvent(new Event("input"));
                    }
                });
            });

            document.querySelectorAll(".btn-plus").forEach(button => {
                button.addEventListener("click", function() {
                    const input = this.closest(".quantity").querySelector(".quantity-input");
                    input.value = parseInt(input.value) + 1;
                    input.dispatchEvent(new Event("input"));
                });
            });

            // Xử lý sự kiện khi nhấn nút xóa sản phẩm
            document.querySelectorAll(".btn-remove").forEach(button => {
                button.addEventListener("click", function() {
                    const row = this.closest("tr");
                    const cartId = row.dataset.cartId;

                    // Hiển thị hộp thoại xác nhận
                    if (confirm("Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?")) {
                        fetch("cart.php", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/x-www-form-urlencoded"
                                },
                                body: `cart_id=${cartId}&quantity=0` // Đặt số lượng về 0 để xóa
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    row.remove(); // Xóa hàng khỏi giao diện
                                    document.getElementById("total-price").textContent =
                                        `${data.total_price}`;
                                    alert(
                                        "Sản phẩm đã được xóa khỏi giỏ hàng."
                                    ); // Hiển thị thông báo
                                }
                            });
                    }
                });
            });
        });
    </script>


    <!-- Footer Start -->
    <?php include '../includes/footer.php' ?>
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