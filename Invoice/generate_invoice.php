<?php
include 'config.php'; // Include the database connection file

function generateInvoiceNumber($conn) {
    $sql = "SELECT invoice_no FROM invoices ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);

    $prefix = 'INV-';
    $number = 1;

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastInvoiceNo = $row['invoice_no'];
        $number = (int)str_replace($prefix, '', $lastInvoiceNo) + 1;
    }

    return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $invoiceNo = generateInvoiceNumber($conn);
    $customerName = $_POST['customer_name'];
    $amount = $_POST['amount'];

    $stmt = $conn->prepare("INSERT INTO invoices (invoice_no, customer_name, amount) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $invoiceNo, $customerName, $amount);

    if ($stmt->execute()) {
        $invoiceId = $stmt->insert_id; // Get the ID of the newly inserted record
        $stmt->close();
        $conn->close();

        // Redirect to view.php with the newly created invoice ID
        header("Location: view.php?id=" . $invoiceId);
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Invoice</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Create Invoice</h2>
    <form action="generate_invoice.php" method="POST">
        <div class="form-group">
            <label for="customer_name">Customer Name</label>
            <input type="text" class="form-control" id="customer_name" name="customer_name" required>
        </div>
        <div class="form-group">
            <label for="amount">Amount</label>
            <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
        </div>
        <button type="submit" class="btn btn-primary">Create Invoice</button>
    </form>
</div>
</body>
</html>
