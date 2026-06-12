<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transcript <?= !empty($transcript['student']['admission_number']) ? e((string)$transcript['student']['admission_number']) : '' ?></title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            padding: 16px;
            font-family: Arial, sans-serif;
            color: #1f2937;
            background: #f3f6fb;
            font-size: 12px;
        }
        .action-bar {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 20;
        }
        .btn {
            border: 0;
            border-radius: 6px;
            padding: 10px 16px;
            text-decoration: none;
            color: #ffffff;
            background: #185490;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
        }
        .btn-secondary {
            background: #4b5563;
        }
        .transcript-sheet {
            max-width: 1120px;
            margin: 0 auto;
            background: #ffffff;
            padding: 28px;
            box-shadow: 0 8px 28px rgba(15, 23, 42, 0.08);
        }
        .transcript-header {
            display: flex;
            align-items: center;
            gap: 18px;
            border-bottom: 3px solid #185490;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .transcript-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            flex-shrink: 0;
        }
        .transcript-title {
            flex: 1;
        }
        .transcript-title h1 {
            margin: 0 0 6px;
            color: #185490;
            font-size: 22px;
        }
        .transcript-title p {
            margin: 3px 0;
            color: #4b5563;
        }
        .document-label {
            margin-top: 8px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1.4px;
            color: #6b7280;
            font-weight: 700;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }
        .meta-card {
            border: 1px solid #dbe4f0;
            border-radius: 8px;
            padding: 14px 16px;
            background: #fbfdff;
        }
        .meta-card h2 {
            margin: 0 0 10px;
            font-size: 14px;
            color: #185490;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 5px 0;
            vertical-align: top;
            border-bottom: 1px solid #edf2f7;
        }
        .meta-table tr:last-child td {
            border-bottom: 0;
        }
        .meta-table td:first-child {
            width: 42%;
            color: #4b5563;
            font-weight: 700;
        }
        .table-wrap {
            border: 1px solid #dbe4f0;
            border-radius: 8px;
            overflow: hidden;
        }
        .results-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        .results-table thead th {
            background: #185490;
            color: #ffffff;
            padding: 10px 8px;
            text-align: left;
            white-space: nowrap;
        }
        .results-table tbody td {
            padding: 9px 8px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }
        .results-table tbody tr:nth-child(even) {
            background: #f8fbff;
        }
        .results-table tbody tr:last-child td {
            border-bottom: 0;
        }
        .grade-pill {
            display: inline-block;
            min-width: 40px;
            padding: 4px 8px;
            border-radius: 999px;
            background: #e8f1fb;
            color: #185490;
            font-weight: 700;
            text-align: center;
        }
        .document-footer {
            margin-top: 20px;
            padding-top: 12px;
            border-top: 1px solid #dbe4f0;
            color: #4b5563;
            font-size: 11px;
            display: flex;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }
        .empty-state {
            padding: 18px;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            color: #4b5563;
            background: #f8fafc;
        }
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .action-bar {
                display: none;
            }
            .transcript-sheet {
                max-width: none;
                padding: 0;
                box-shadow: none;
            }
            @page {
                size: A4 landscape;
                margin: 12mm;
            }
        }
        @media (max-width: 768px) {
            .action-bar {
                position: static;
                margin-bottom: 12px;
                justify-content: flex-end;
                flex-wrap: wrap;
            }
            .transcript-sheet {
                padding: 18px;
            }
            .transcript-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .meta-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="action-bar">
        <a href="<?= e(base_url('portal/grades')) ?>" class="btn btn-secondary">Back to Results</a>
        <button type="button" class="btn btn-secondary" onclick="window.print()">Print Transcript</button>
        <button type="button" class="btn" id="downloadBtn" onclick="downloadPDF()">Download PDF</button>
    </div>

    <div class="transcript-sheet" id="transcriptSheet">
        <div class="transcript-header">
            <img src="<?= e(base_url('assets/images/logo.png')) ?>" alt="College Logo" class="transcript-logo" onerror="this.style.display='none'">
            <div class="transcript-title">
                <h1>St. Mary's Mother and Child Hospital Medical Training College</h1>
                <?php if (!empty($transcript['settings']['email'])): ?><p>Email: <?= e((string)$transcript['settings']['email']) ?></p><?php endif; ?>
                <?php if (!empty($transcript['settings']['phone'])): ?><p>Phone: <?= e((string)$transcript['settings']['phone']) ?></p><?php endif; ?>
                <?php if (!empty($transcript['settings']['location'])): ?><p><?= e((string)$transcript['settings']['location']) ?></p><?php endif; ?>
                <div class="document-label">Official Academic Transcript</div>
            </div>
        </div>

        <div class="meta-grid">
            <div class="meta-card">
                <h2>Student Details</h2>
                <table class="meta-table">
                    <tr>
                        <td>Name</td>
                        <td><?= e((string)($transcript['student']['name'] ?? '-')) ?></td>
                    </tr>
                    <tr>
                        <td>Admission Number</td>
                        <td><?= e((string)($transcript['student']['admission_number'] ?? '-')) ?></td>
                    </tr>
                    <tr>
                        <td>Course</td>
                        <td><?= e((string)($transcript['student']['programme_name'] ?? '-')) ?></td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td><?= e((string)($transcript['student']['email'] ?? '-')) ?></td>
                    </tr>
                </table>
            </div>

            <div class="meta-card">
                <h2>Academic Period</h2>
                <table class="meta-table">
                    <tr>
                        <td>Term</td>
                        <td><?= e((string)($transcript['term_name'] ?? '-')) ?></td>
                    </tr>
                    <tr>
                        <td>Session</td>
                        <td><?= e((string)($transcript['session_name'] ?? '-')) ?></td>
                    </tr>
                    <tr>
                        <td>Date Issued</td>
                        <td><?= e(date('F j, Y')) ?></td>
                    </tr>
                    <tr>
                        <td>Document Type</td>
                        <td>Transcript</td>
                    </tr>
                </table>
            </div>
        </div>

        <?php if (empty($transcript['rows'] ?? [])): ?>
            <div class="empty-state">No grades are available to include in this transcript yet.</div>
        <?php else: ?>
            <div class="table-wrap">
                <table class="results-table">
                    <thead>
                        <tr>
                            <th>Unit Code</th>
                            <th>Unit Name</th>
                            <?php foreach (($transcript['examColumns'] ?? []) as $examColumn): ?>
                                <th><?= e((string)$examColumn) ?></th>
                            <?php endforeach; ?>
                            <th>Grade</th>
                            <th>Comment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (($transcript['rows'] ?? []) as $gradeRow): ?>
                            <tr>
                                <td><?= e((string)($gradeRow['course_code'] ?? 'N/A')) ?></td>
                                <td><?= e((string)($gradeRow['course_title'] ?? '-')) ?></td>
                                <?php foreach (($transcript['examColumns'] ?? []) as $examColumn): ?>
                                    <?php $examMark = $gradeRow['exam_marks'][$examColumn] ?? null; ?>
                                    <td><?= $examMark === null || $examMark === '' ? '-' : e((string)$examMark) ?></td>
                                <?php endforeach; ?>
                                <td>
                                    <?php if (!empty($gradeRow['grade'])): ?>
                                        <span class="grade-pill"><?= e((string)$gradeRow['grade']) ?></span>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><?= !empty($gradeRow['comment']) ? e((string)$gradeRow['comment']) : '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <div class="document-footer">
            <div>This transcript is generated from the student portal records.</div>
            <div>Generated on <?= e(date('F j, Y g:i A')) ?></div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function downloadPDF() {
            if (typeof html2pdf === 'undefined') {
                window.print();
                return;
            }

            const element = document.getElementById('transcriptSheet');
            const button = document.getElementById('downloadBtn');
            const originalText = button ? button.textContent : '';

            if (!element) {
                alert('Transcript content could not be found.');
                return;
            }

            if (button) {
                button.textContent = 'Generating PDF...';
                button.disabled = true;
            }

            const fileName = 'Transcript_<?= e((string)($transcript['student']['admission_number'] ?? 'student')) ?>.pdf';
            const options = {
                margin: 8,
                filename: fileName,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true, logging: false },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
            };

            html2pdf().set(options).from(element).save().then(function () {
                if (button) {
                    button.textContent = originalText;
                    button.disabled = false;
                }
            }).catch(function () {
                if (button) {
                    button.textContent = originalText;
                    button.disabled = false;
                }
                alert('PDF generation failed. Please try again or use Print Transcript.');
            });
        }

        window.addEventListener('load', function () {
            const params = new URLSearchParams(window.location.search);
            if (params.get('download') === '1') {
                setTimeout(downloadPDF, 800);
            }
        });
    </script>
</body>
</html>
