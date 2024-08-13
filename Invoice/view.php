<?php
include 'config.php'; // Include the database connection file

// Retrieve the invoice ID from the URL
$id = $_GET['id'] ?? null;

if (!$id) {
    die('Invoice ID is missing.');
}

// Prepare and execute the query to fetch the invoice details
$sql = "SELECT invoice_no, customer_name, amount FROM invoices WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Invoice not found.');
}

$invoice = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Invoice</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Invoice Details</h2>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Invoice No: <?php echo htmlspecialchars($invoice['invoice_no']); ?></h5>
            <p class="card-text"><strong>Customer Name:</strong> <?php echo htmlspecialchars($invoice['customer_name']); ?></p>
            <p class="card-text"><strong>Amount:</strong> $<?php echo number_format($invoice['amount'], 2); ?></p>
            <a href="index.php" class="btn btn-primary">Back to List</a>
        </div>
    </div>
</div>
</body>
</html>
