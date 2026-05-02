<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Letter</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
            background: #f0f0f0;
            padding: 20px;
        }
        
        .letter {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #185490;
            padding-bottom: 20px;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            margin-bottom: 10px;
        }
        
        .college-name {
            font-size: 18pt;
            font-weight: bold;
            color: #185490;
            margin-bottom: 5px;
        }
        
        .contact-line {
            font-size: 10pt;
            color: #333;
            margin-bottom: 3px;
        }
        
        .letter-title {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            color: #185490;
            margin: 30px 0;
            text-transform: uppercase;
        }
        
        .letter-type {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin: 20px 0;
            color: #00aae8;
        }
        
        .content {
            margin: 20px 0;
            text-align: justify;
        }
        
        .content p {
            margin-bottom: 15px;
        }
        
        .student-details {
            background: #f8f9fa;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #185490;
        }
        
        .student-details strong {
            color: #185490;
        }
        
        .fee-section {
            background: #fff3cd;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #ffc107;
        }
        
        .fee-section strong {
            color: #856404;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 10pt;
            text-align: center;
            color: #666;
        }
        
        .signature {
            margin-top: 60px;
            text-align: right;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            width: 200px;
            margin-left: auto;
            padding-top: 5px;
        }
        
        .validity {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            margin: 20px 0;
            text-align: center;
            border-radius: 5px;
            font-weight: bold;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .letter {
                box-shadow: none;
                padding: 20px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; cursor: pointer;">
            <i class="bi bi-printer"></i> Print Letter
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; cursor: pointer; margin-left: 10px;">
            <i class="bi bi-x"></i> Close
        </button>
    </div>
    
    <div class="letter">
        <div class="header">
            <img src="/assets/images/logo.png" alt="College Logo" class="logo" onerror="this.style.display='none'">
            <div class="college-name">St. Mary's MCH Medical Training College</div>
            <div class="contact-line">Email: <?= e($settings['email'] ?? 'info@stmarysmchmcollege.ac.ke') ?></div>
            <div class="contact-line">Phone: <?= e($settings['phone'] ?? '+254 700 000 000') ?></div>
            <div class="contact-line">Location: <?= e($settings['location'] ?? 'Nairobi, Kenya') ?></div>
        </div>
        
        <div class="letter-title">ADMISSION LETTER</div>
        
        <div class="letter-type">
            <?php if ($offer['offer_type'] === 'provisional'): ?>
                PROVISIONAL ADMISSION OFFER
            <?php else: ?>
                CONFIRMED ADMISSION OFFER
            <?php endif; ?>
        </div>
        
        <div class="content">
            <p><strong>Date:</strong> <?= date('F j, Y', strtotime($offer['issued_date'])) ?></p>
            
            <p><strong>To,</strong><br>
            <?= e($lead['name']) ?><br>
            <?= e($lead['email'] ?? '') ?><br>
            <?= e($lead['phone']) ?></p>
            
            <p><strong>Subject:</strong> Admission to <?= e($lead['program_interest'] ?? 'Medical Training Program') ?></p>
            
            <p>Dear <?= e(explode(' ', $lead['name'])[0]) ?>,</p>
            
            <p>We are pleased to inform you that you have been offered admission to pursue the <strong><?= e($lead['program_interest'] ?? 'Medical Training Program') ?></strong> at St. Mary's MCH Medical Training College for the <strong><?= e($lead['intake_name'] ?? 'Upcoming Intake') ?></strong>.</p>

            <div class="student-details">
                <strong>Student Details:</strong><br>
                Name: <?= e($lead['name']) ?><br>
                Phone: <?= e($lead['phone']) ?><br>
                Program: <?= e($lead['program_interest'] ?? '-') ?><br>
                Intake: <?= e($lead['intake_name'] ?? '-') ?>
            </div>
            
            <div class="fee-section">
                <strong>Registration Fee:</strong> KES 5,000<br>
                <strong>Payment Instructions:</strong><br>
                - M-PESA: Pay to our business number<br>
                - Bank: Account details available on request<br>
                - Cash: Visit the college admissions office
            </div>
            
            <?php if ($offer['offer_type'] === 'provisional'): ?>
                <p><strong>Please Note:</strong></p>
                <ul>
                    <li>This is a provisional admission offer valid until <strong><?= date('F j, Y', strtotime($offer['expiry_date'])) ?></strong></li>
                    <li>Your admission will be confirmed upon payment of the registration fee</li>
                    <li>Seats are limited and will be allocated on a first-come, first-served basis</li>
                    <li>Early payment is encouraged to secure your seat</li>
                </ul>
            <?php else: ?>
                <p><strong>Congratulations!</strong></p>
                <p>Your admission has been confirmed following successful payment of the registration fee. Please report to the college on the reporting date for orientation and enrollment.</p>
            <?php endif; ?>
            
            <p><strong>Required Documents for Enrollment:</strong></p>
            <ul>
                <li>National ID / Birth Certificate</li>
                <li>Academic Certificates (KCSE or equivalent)</li>
                <li>Passport-size photographs (4 copies)</li>
                <li>Medical certificate</li>
            </ul>
            
            <p>For any inquiries, please contact the admissions office using the contact details provided above.</p>
            
            <p>We look forward to welcoming you to St. Mary's MCH Medical Training College.</p>
        </div>
        
        <div class="validity">
            <?php if ($offer['offer_type'] === 'provisional'): ?>
                This offer expires on <?= date('F j, Y', strtotime($offer['expiry_date'])) ?>
            <?php else: ?>
                Confirmed Admission - Valid for Enrollment
            <?php endif; ?>
        </div>
        
        <div class="signature">
            <p><strong>Admissions Officer</strong></p>
            <p>St. Mary's MCH Medical Training College</p>
            <div class="signature-line">Signature</div>
        </div>
        
        <div class="footer">
            <p>Generated on <?= date('F j, Y g:i A') ?> | CRM System</p>
            <p>This is an electronically generated document. No physical signature required.</p>
        </div>
    </div>
    
    <script>
    // Set document title for print
    document.title = 'Admission Letter - <?= e($lead['name']) ?>';
    
    // Auto-print if download parameter is present
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('download') === '1') {
        window.print();
    }
    
    function e(string) {
        return string ? string.replace(/[&<>"']/g, function(m) {
            return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[m];
        }) : '';
    }
    </script>
</body>
</html>
