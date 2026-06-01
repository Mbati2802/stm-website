<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice <?= e($invoice['invoice_number']) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .btn-group {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn {
            padding: 12px 24px;
            margin: 0 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            background: #f0f0f0;
            color: #333;
            transition: all 0.2s ease;
        }
        .btn:hover {
            background: #e0e0e0;
        }
        .btn-primary {
            background: #185490;
            color: white;
        }
        .btn-primary:hover {
            background: #124070;
        }
        .btn-back {
            background: #6c757d;
            color: white;
        }
        .btn-back:hover {
            background: #5a6268;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .invoice-header {
            border-bottom: 3px solid #185490;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .invoice-header .logo {
            width: 80px;
            height: 80px;
            margin-bottom: 10px;
            object-fit: contain;
        }
        .invoice-header h1 {
            margin: 0;
            color: #185490;
            font-size: 16px;
        }
        .invoice-header h2 {
            margin: 3px 0;
            color: #666;
            font-size: 13px;
            font-weight: normal;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .invoice-header .contact {
            margin-top: 10px;
            font-size: 12px;
            color: #666;
        }
        .invoice-header .contact p {
            margin: 2px 0;
        }
        .invoice-header .location {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .invoice-title {
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 10px 0 15px 0;
            color: #666;
            font-size: 13px;
        }
        .invoice-info {
            margin-bottom: 20px;
        }
        .invoice-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-info td {
            padding: 8px 5px;
            font-size: 13px;
        }
        .invoice-info td:first-child {
            font-weight: bold;
            color: #666;
            width: 40%;
        }
        .student-info {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .student-info h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #185490;
        }
        .student-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .student-info td {
            padding: 5px;
            font-size: 13px;
        }
        .student-info td:first-child {
            font-weight: bold;
            color: #666;
            width: 40%;
        }
        .fee-items {
            margin-bottom: 20px;
        }
        .fee-items table {
            width: 100%;
            border-collapse: collapse;
        }
        .fee-items th {
            background: #185490;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 13px;
        }
        .fee-items td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            font-size: 13px;
        }
        .fee-items tr:last-child td {
            border-bottom: none;
        }
        .invoice-total {
            text-align: right;
            margin-top: 20px;
        }
        .invoice-total table {
            width: 50%;
            margin-left: auto;
            border-collapse: collapse;
        }
        .invoice-total td {
            padding: 8px;
            font-size: 14px;
            border-bottom: 1px solid #eee;
        }
        .invoice-total tr:last-child td {
            border-bottom: none;
            font-size: 16px;
            font-weight: bold;
        }
        .invoice-footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .invoice-footer p {
            margin: 5px 0;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .invoice-container {
                box-shadow: none;
                border-radius: 0;
                padding: 20px;
            }
            .btn-group {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="btn-group">
        <a href="<?= e(base_url('portal/fees')) ?>" class="btn btn-back">← Back to Fees</a>
        <button class="btn" onclick="window.print()">Print Invoice</button>
        <button class="btn btn-primary" onclick="downloadPDF()">Download PDF</button>
    </div>
    
    <div class="invoice-container">
        <div class="invoice-header">
            <img src="/assets/images/logo.png" alt="College Logo" class="logo" onerror="this.style.display='none'">
            <h1>St. Mary's Mother and Child Hospital Medical Training College</h1>
            <div class="contact">
                <?php if (!empty($settings['email'])): ?><p>Email: <?= e($settings['email']) ?></p><?php endif; ?>
                <?php if (!empty($settings['phone'])): ?><p>Phone: <?= e($settings['phone']) ?></p><?php endif; ?>
            </div>
            <?php if (!empty($settings['location'])): ?><p class="location"><?= e($settings['location']) ?></p><?php endif; ?>
        </div>
        <h2 style="text-align: center; text-transform: uppercase; letter-spacing: 1px; margin: 10px 0 15px 0; color: #666; font-size: 13px;">Official Invoice</h2>
        
        <div class="invoice-info">
            <table>
                <tr>
                    <td>Invoice Number:</td>
                    <td><?= e($invoice['invoice_number']) ?></td>
                </tr>
                <tr>
                    <td>Invoice Date:</td>
                    <td><?= e($invoice['created_at']) ?></td>
                </tr>
                <tr>
                    <td>Due Date:</td>
                    <td><?= e($invoice['due_date'] ?? 'Not specified') ?></td>
                </tr>
                <tr>
                    <td>Status:</td>
                    <td><?= ucfirst(e($invoice['status'])) ?></td>
                </tr>
            </table>
        </div>

        <div class="student-info">
            <h3>Bill To</h3>
            <table>
                <tr>
                    <td>Student Name:</td>
                    <td><?= e($invoice['student_name']) ?></td>
                </tr>
                <tr>
                    <td>Admission Number:</td>
                    <td><?= e($invoice['admission_number']) ?></td>
                </tr>
                <tr>
                    <td>Programme:</td>
                    <td><?= e($invoice['programme_name']) ?> (<?= e($invoice['programme_abbr']) ?>)</td>
                </tr>
                <tr>
                    <td>Term:</td>
                    <td><?= e($invoice['term_name'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td>Session:</td>
                    <td><?= e($invoice['session_name'] ?? '-') ?></td>
                </tr>
            </table>
        </div>

        <div class="fee-items">
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th style="text-align: right;">Amount (KES)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($feeItems)): ?>
                        <tr>
                            <td colspan="2">No fee items found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($feeItems as $item): ?>
                            <tr>
                                <td><?= e($item['description']) ?></td>
                                <td style="text-align: right;"><?= number_format($item['amount'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="invoice-total">
            <table>
                <tr>
                    <td>Subtotal:</td>
                    <td style="text-align: right;">KES <?= number_format($invoice['amount'], 2) ?></td>
                </tr>
                <tr>
                    <td>Paid:</td>
                    <td style="text-align: right;">KES <?= number_format($totalPaid, 2) ?></td>
                </tr>
                <tr>
                    <td>Balance:</td>
                    <td style="text-align: right; color: <?= $balance > 0 ? '#d32f2f' : '#2e7d32' ?>; font-weight: bold;">KES <?= number_format($balance, 2) ?></td>
                </tr>
            </table>
        </div>

        <div class="invoice-footer">
            <p>This is an official invoice from St. Mary's Mother and Child Hospital Medical Training College.</p>
            <p>For any inquiries, please contact the finance office.</p>
            <p>Generated on: <?= date('F j, Y g:i A') ?></p>
        </div>
    </div>

    <!-- html2pdf.js library for PDF generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function downloadPDF() {
            // Get the invoice container element
            const element = document.querySelector('.invoice-container');
            
            // Configure PDF options
            const opt = {
                margin:       10,
                filename:     'Invoice_<?= e($invoice['invoice_number']) ?>.pdf',
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
