<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt <?= e($payment['payment_number']) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: white;
            padding: 15px;
            font-size: 12px;
        }
        .receipt-container {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            padding: 20px;
        }
        .receipt-header {
            text-align: center;
            border-bottom: 3px solid #185490;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .receipt-header .logo {
            width: 80px;
            height: 80px;
            margin-bottom: 10px;
            object-fit: contain;
        }
        .receipt-header h1 {
            margin: 0;
            color: #185490;
            font-size: 16px;
        }
        .receipt-header h2 {
            margin: 3px 0;
            color: #666;
            font-size: 13px;
            font-weight: normal;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .receipt-header .contact {
            margin-top: 5px;
            font-size: 11px;
            color: #555;
        }
        .receipt-info {
            margin-bottom: 15px;
        }
        .receipt-info h3 {
            font-size: 13px;
            margin-bottom: 8px;
            color: #333;
        }
        .receipt-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .receipt-info td {
            padding: 4px 0;
            border-bottom: 1px solid #eee;
            font-size: 12px;
        }
        .receipt-info td:first-child {
            font-weight: bold;
            width: 45%;
            color: #555;
        }
        .receipt-amount {
            background: #f8f9fa;
            padding: 12px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            color: #28a745;
            margin: 15px 0;
        }
        .receipt-footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #eee;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        .receipt-footer p {
            margin: 2px 0;
        }
        .btn-group {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
        }
        .btn:hover {
            background: #0056b3;
        }
        @media print {
            .btn-group {
                display: none;
            }
            body {
                background: white;
                padding: 0;
            }
            .receipt-container {
                padding: 0;
            }
            @page {
                margin: 10mm;
            }
        }
    </style>
</head>
<body>
    <div class="btn-group">
        <button class="btn" onclick="window.print()">Print Receipt</button>
        <button class="btn" onclick="downloadPDF()">Download PDF</button>
    </div>
    
    <div class="receipt-container">
        <div class="receipt-header">
            <img src="/assets/images/logo.png" alt="College Logo" class="logo" onerror="this.style.display='none'">
            <h1>St. Mary's Mother and Child Hospital Medical Training College</h1>
            <div class="contact">
                <?php if (!empty($settings['email'])): ?><p>Email: <?= e($settings['email']) ?></p><?php endif; ?>
                <?php if (!empty($settings['phone'])): ?><p>Phone: <?= e($settings['phone']) ?></p><?php endif; ?>
            </div>
            <?php if (!empty($settings['location'])): ?><p class="location"><?= e($settings['location']) ?></p><?php endif; ?>
        </div>
        <h2 style="text-align: center; text-transform: uppercase; letter-spacing: 1px; margin: 10px 0 15px 0; color: #666; font-size: 13px;">Official Payment Receipt</h2>
        
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
                    <td><?= e($payment['transaction_code'] ?: $payment['cheque_number'] ?: 'N/A') ?></td>
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
                    <td style="font-weight: bold;">KES <?= number_format($payment['invoice_amount'] - $totalPaid, 2) ?></td>
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

    <!-- html2pdf.js library for PDF generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function downloadPDF() {
            // Get the receipt container element
            const element = document.querySelector('.receipt-container');
            
            // Configure PDF options
            const opt = {
                margin:       10,
                filename:     'Receipt_<?= e($payment['payment_number']) ?>.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };
            
            // Generate and download PDF
            html2pdf().set(opt).from(element).save();
        }
        
        // Auto-trigger download on load if URL has download parameter
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('download') === '1') {
                // Wait for page to fully render, then download
                setTimeout(() => {
                    downloadPDF();
                }, 1000);
            }
        };
    </script>
</body>
</html>
