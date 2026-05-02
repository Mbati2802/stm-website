<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt <?= e($payment['payment_number']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .receipt-header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .receipt-header h1 {
            margin: 0;
            color: #333;
        }
        .receipt-header p {
            margin: 5px 0;
            color: #666;
        }
        .receipt-info {
            margin-bottom: 30px;
        }
        .receipt-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .receipt-info td {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .receipt-info td:first-child {
            font-weight: bold;
            width: 40%;
            color: #555;
        }
        .receipt-amount {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
            margin: 20px 0;
        }
        .receipt-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .print-btn:hover {
            background: #0056b3;
        }
        @media print {
            .print-btn {
                display: none;
            }
            body {
                background: white;
                padding: 0;
            }
            .receipt-container {
                box-shadow: none;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">Print Receipt</button>
    
    <div class="receipt-container">
        <div class="receipt-header">
            <h1>St. Mary's Mother and Child Hospital Medical Training College</h1>
            <p>Official Payment Receipt</p>
        </div>
        
        <div class="receipt-info">
            <table>
                <tr>
                    <td>Receipt Number:</td>
                    <td><?= e($payment['payment_number']) ?></td>
                </tr>
                <tr>
                    <td>Date:</td>
                    <td><?= date('F j, Y', strtotime($payment['payment_date'])) ?></td>
                </tr>
                <tr>
                    <td>Payment Method:</td>
                    <td><?= e($payment['payment_method_name']) ?></td>
                </tr>
                <tr>
                    <td>Transaction/Cheque #:</td>
                    <td><?= e($payment['transaction_code'] ?? $payment['cheque_number'] ?? 'N/A') ?></td>
                </tr>
                <?php if ($payment['bank_name']): ?>
                <tr>
                    <td>Bank:</td>
                    <td><?= e($payment['bank_name']) ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
        
        <div class="receipt-info">
            <h3>Student Information</h3>
            <table>
                <tr>
                    <td>Name:</td>
                    <td><?= e($payment['student_name']) ?></td>
                </tr>
                <tr>
                    <td>Admission Number:</td>
                    <td><?= e($payment['admission_number']) ?></td>
                </tr>
                <tr>
                    <td>Email:</td>
                    <td><?= e($payment['student_email']) ?></td>
                </tr>
            </table>
        </div>
        
        <div class="receipt-info">
            <h3>Invoice Information</h3>
            <table>
                <tr>
                    <td>Invoice Number:</td>
                    <td><?= e($payment['invoice_number']) ?></td>
                </tr>
                <tr>
                    <td>Invoice Title:</td>
                    <td><?= e($payment['invoice_title']) ?></td>
                </tr>
                <tr>
                    <td>Invoice Amount:</td>
                    <td>KES <?= number_format($payment['invoice_amount'], 2) ?></td>
                </tr>
                <tr>
                    <td>Total Paid (This Invoice):</td>
                    <td>KES <?= number_format($totalPaid, 2) ?></td>
                </tr>
                <tr>
                    <td>Remaining Balance:</td>
                    <td>KES <?= number_format($payment['invoice_amount'] - $totalPaid, 2) ?></td>
                </tr>
            </table>
        </div>
        
        <div class="receipt-amount">
            KES <?= number_format($payment['amount'], 2) ?>
        </div>
        
        <?php if ($payment['notes']): ?>
        <div class="receipt-info">
            <h3>Notes</h3>
            <p><?= e($payment['notes']) ?></p>
        </div>
        <?php endif; ?>
        
        <div class="receipt-footer">
            <p>This is an official receipt from St. Mary's Mother and Child Hospital Medical Training College.</p>
            <p>For any inquiries, please contact the finance office.</p>
            <p>Generated on: <?= date('F j, Y g:i A') ?></p>
        </div>
    </div>
</body>
</html>
