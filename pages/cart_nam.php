<?php

require "../config.php";
require_once '../functions.php';

$userId = 1;

function getCartItems($userId) {
    global $conn;
    $sql = "SELECT p.id, p.name, p.price, SUM(c.quantity) AS quantity, p.inventory
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = $userId
            GROUP BY p.id";  // Group by product ID để không bị trùng sản phẩm
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Hàm tính tổng số tiền (subtotal + shipping)
function getCartTotal($userId) {
    global $conn;
    $sql = "SELECT SUM(p.price * c.quantity) AS subtotal
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = $userId";
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($result);
    $subtotal = $data['subtotal'];
    $shipping = 0; // Giả sử phí vận chuyển cố định là 10
    $total = $subtotal + $shipping;
    return ['subtotal' => $subtotal, 'shipping' => $shipping, 'total' => $total];
}

// Hàm lấy số lượng tên sản phẩm trong giỏ hàng (không tính số lượng, chỉ tính số sản phẩm duy nhất)
function getCartProductCount($userId) {
    global $conn;
    $sql = "SELECT COUNT(DISTINCT product_id) AS product_count
            FROM cart 
            WHERE user_id = $userId";
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($result);
    return $data['product_count'];
}

// Thêm sản phẩm vào giỏ hàng
function addToCart($userId, $productId) {
    global $conn;
    // Kiểm tra sản phẩm đã có trong giỏ hàng chưa
    $sql = "SELECT * FROM cart WHERE user_id = $userId AND product_id = $productId";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        // Nếu sản phẩm đã có, cập nhật số lượng
        $sql = "UPDATE cart SET quantity = quantity + 1 WHERE user_id = $userId AND product_id = $productId";
    } else {
        // Nếu chưa có, thêm mới vào giỏ hàng
        $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES ($userId, $productId, 1)";
    }
    mysqli_query($conn, $sql);
}

function removeFromCart($userId, $productId) {
    global $conn;
    
    // Thực hiện xóa sản phẩm trong giỏ hàng
    $sql = "DELETE FROM cart WHERE user_id = $userId AND product_id = $productId";
    $result = mysqli_query($conn, $sql);
    return $result;
}

// Xử lý yêu cầu add hoặc remove từ form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    if ($action == 'add') {
        $productId = $_POST['product_id'];
        addToCart($userId, $productId);
    } elseif ($action == 'remove') {
        $productId = $_POST['product_id'];
        if (removeFromCart($userId, $productId)) {
            echo 'success';  // Trả về "success" nếu xóa thành công
        } else {
            echo 'error';  // Trả về "error" nếu có lỗi
        }
    } elseif ($action == 'update') {
        $productId = $_POST['product_id'];
        $quantity = $_POST['quantity'];
        $sql = "UPDATE cart SET quantity = $quantity WHERE user_id = $userId AND product_id = $productId";
        mysqli_query($conn, $sql);
    }
}

$cartItems = getCartItems($userId);
$cartTotal = getCartTotal($userId);
$cartCount = getCartProductCount($userId); // Số lượng tên sản phẩm trong giỏ hàng
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php include '../includes/head.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <!-- Topbar Start -->
    <?php include '../includes/topbar.php'; ?>
    <!-- Topbar End -->

    <!-- Navbar Start -->
    <?php include '../includes/navbar.php'; ?>
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

    <!-- Cart start -->
    <div class="container-fluid">
        <div class="row px-xl-5">
            <div id="cart-table" class="col-lg-8 table-responsive mb-5">
                <table class="table table-light table-borderless table-hover text-center mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th>Products</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Remove</th>
                        </tr>
                    </thead>

                    <tbody class="align-middle">
                        <?php foreach ($cartItems as $item): ?>
                        <tr data-product-id="<?php echo $item['id']; ?>"
                            data-inventory="<?php echo $item['inventory']; ?>">
                            <td class="align-middle" style="text-align:left;">
                                <a style="color:#6C757D;"
                                    href="product.php?id=<?php echo $item['id']; ?>"><?php echo $item['name']; ?></a>
                            </td>

                            <td class="align-middle price">$<?php echo number_format($item['price'], 2); ?></td>

                            <td class="align-middle">
                                <div class="input-group mx-auto" style="width: 100px; justify-content: center;">
                                    <div class="input-group-btn">
                                        <button class="btn btn-sm btn-primary quantity-decrease">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                    <div style="padding: 0 10px;" class="quantity"><?php echo $item['quantity']; ?>
                                    </div>
                                    <div class="input-group-btn">
                                        <button class="btn btn-sm btn-primary quantity-increase">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </td>

                            <td class="align-middle subtotal">
                                $<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>

                            <td class="align-middle">
                                <button class="btn btn-sm btn-danger remove-item">
                                    <i class="fa fa-times"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="col-lg-4">
                <h5 class="section-title position-relative text-uppercase mb-3"><span class="bg-secondary pr-3">Cart
                        Summary</span></h5>
                <div class="bg-light p-30 mb-5">
                    <div class="border-bottom pb-2">
                        <div class="d-flex justify-content-between mb-3">
                            <h6>Subtotal</h6>
                            <h6 id="subtotal">$<?php echo number_format($cartTotal['subtotal'], 2); ?></h6>
                        </div>
                        <div class="d-flex justify-content-between">
                            <h6 class="font-weight-medium">Shipping</h6>
                            <h6 id="shipping">$<?php echo number_format($cartTotal['shipping'], 2); ?></h6>
                        </div>
                    </div>

                    <div class="pt-2">
                        <div class="d-flex justify-content-between mt-2">
                            <h5>Total</h5>
                            <h5 id="total">$<?php echo number_format($cartTotal['total'], 2); ?></h5>
                        </div>
                        <a href="checkout.php" class="btn btn-block btn-primary font-weight-bold my-3 py-3">Proceed To
                            Checkout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Cart end -->

    <?php include '../includes/footer.php'; ?>

    <script>
    // Xử lý sự kiện thay đổi số lượng và cập nhật giỏ hàng
    $(document).ready(function() {
        // Tăng số lượng
        $('.quantity-increase').on('click', function() {
            var row = $(this).closest('tr');
            var currentQuantity = parseInt(row.find('.quantity').text());
            var maxQuantity = parseInt(row.data('inventory')); // Lấy số lượng tồn kho
            var price = parseFloat(row.find('.price').text().replace('$', ''));

            // Kiểm tra nếu số lượng hiện tại < số lượng tồn kho
            if (currentQuantity < maxQuantity) {
                var newQuantity = currentQuantity + 1; // Tăng số lượng lên 1
                row.find('.quantity').text(newQuantity);

                var subtotal = price * newQuantity;
                row.find('.subtotal').text('$' + subtotal.toFixed(2));

                updateCartTotal();

                // Cập nhật số lượng vào database
                var productId = row.data('product-id');
                $.post('cart.php', {
                    action: 'update',
                    product_id: productId,
                    quantity: newQuantity
                });
                updateCartIcon(); // Cập nhật icon giỏ hàng
            } else {
                alert('Quantity is maximum in the inventory!');
            }
        });

        // Giảm số lượng
        $('.quantity-decrease').on('click', function() {
            var row = $(this).closest('tr');
            var currentQuantity = parseInt(row.find('.quantity').text());
            if (currentQuantity > 1) { // Không giảm xuống 0
                var price = parseFloat(row.find('.price').text().replace('$', ''));
                var newQuantity = currentQuantity - 1; // Giảm số lượng đi 1
                row.find('.quantity').text(newQuantity);

                var subtotal = price * newQuantity;
                row.find('.subtotal').text('$' + subtotal.toFixed(2));

                updateCartTotal();

                // Cập nhật số lượng vào database
                var productId = row.data('product-id');
                $.post('cart.php', {
                    action: 'update',
                    product_id: productId,
                    quantity: newQuantity
                });
                updateCartIcon(); // Cập nhật icon giỏ hàng
            }
        });

        // Cập nhật tổng giỏ hàng
        function updateCartTotal() {
            var subtotal = 0;
            $('#cart-table .subtotal').each(function() {
                subtotal += parseFloat($(this).text().replace('$', ''));
            });

            var shipping = 0; // Giả sử phí vận chuyển cố định
            var total = subtotal + shipping;

            $('#subtotal').text('$' + subtotal.toFixed(2));
            $('#shipping').text('$' + shipping.toFixed(2));
            $('#total').text('$' + total.toFixed(2));
        }

        $('.remove-item').on('click', function() {
            var row = $(this).closest('tr');
            var productId = row.data('product-id'); // Lấy product_id từ dữ liệu HTML

            // Kiểm tra xem product_id có tồn tại không
            if (!productId) {
                alert("Product ID is missing!");
                return;
            }

            if (confirm('Are you sure to delete the product in the cart?')) {
                $.post('cart.php', {
                    action: 'remove',
                    product_id: productId
                }, function(response) {
                    // Kiểm tra phản hồi từ máy chủ
                    if (response == 'success') {
                        row.remove(); // Xóa sản phẩm khỏi giỏ hàng trên giao diện
                        updateCartTotal(); // Cập nhật lại tổng giỏ hàng
                        updateCartIcon(); // Cập nhật icon giỏ hàng
                        window.location.reload();
                    }
                });
            }
        });
    });
    </script>

</body>

</html>