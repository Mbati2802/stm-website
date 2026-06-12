<?php
class StudentPortalModel
{
    private PDO $pdo;

    public function __construct(array $config)
    {
        $this->pdo = Database::getInstance($config['db']);
    }

    public function findStudentByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM student_accounts WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public function findStudentByNationalId(string $nationalId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM student_accounts WHERE national_id = :national_id LIMIT 1');
        $stmt->execute(['national_id' => $nationalId]);
        return $stmt->fetch() ?: null;
    }

    public function findStudentByAdmissionNumber(string $admissionNumber): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM student_accounts WHERE admission_number = :admission_number LIMIT 1');
        $stmt->execute(['admission_number' => $admissionNumber]);
        return $stmt->fetch() ?: null;
    }

    public function createStudent(string $name, string $email, string $passwordHash): bool
    {
        $stmt = $this->pdo->prepare('INSERT INTO student_accounts(name, email, password, created_at) VALUES(:name, :email, :password, NOW())');
        return $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => $passwordHash,
        ]);
    }

    public function createStudentWithDetails(
        string $name,
        string $email,
        string $passwordHash,
        string $nationalId,
        string $gender,
        string $dateOfBirth,
        string $phone,
        string $county,
        string $subCounty,
        string $guardianName,
        string $guardianRelationship,
        string $guardianPhone,
        string $guardianEmail,
        string $previousSchool,
        string $kcseYear,
        string $kcseGrade,
        string $kcseIndex,
        int $programmeId,
        string $preferredIntake,
        string $disabilityStatus,
        string $referralSource,
        string $additionalNotes
    ): int {
        $stmt = $this->pdo->prepare(<<<SQL
            INSERT INTO student_accounts(
                name, email, password, national_id, gender, date_of_birth, phone, county, sub_county,
                guardian_name, guardian_relationship, guardian_phone, guardian_email,
                previous_school, kcse_year, kcse_grade, kcse_index,
                programme_id, preferred_intake, disability_status, referral_source, additional_notes,
                created_at
            ) VALUES (
                :name, :email, :password, :national_id, :gender, :date_of_birth, :phone, :county, :sub_county,
                :guardian_name, :guardian_relationship, :guardian_phone, :guardian_email,
                :previous_school, :kcse_year, :kcse_grade, :kcse_index,
                :programme_id, :preferred_intake, :disability_status, :referral_source, :additional_notes,
                NOW()
            )
        SQL);
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => $passwordHash,
            'national_id' => $nationalId,
            'gender' => $gender,
            'date_of_birth' => $dateOfBirth,
            'phone' => $phone,
            'county' => $county,
            'sub_county' => $subCounty,
            'guardian_name' => $guardianName,
            'guardian_relationship' => $guardianRelationship,
            'guardian_phone' => $guardianPhone,
            'guardian_email' => $guardianEmail,
            'previous_school' => $previousSchool,
            'kcse_year' => $kcseYear,
            'kcse_grade' => $kcseGrade,
            'kcse_index' => $kcseIndex,
            'programme_id' => $programmeId,
            'preferred_intake' => $preferredIntake,
            'disability_status' => $disabilityStatus,
            'referral_source' => $referralSource,
            'additional_notes' => $additionalNotes,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function findStudentById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT sa.*, p.abbreviation FROM student_accounts sa LEFT JOIN programmes p ON sa.programme_id = p.id WHERE sa.id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function saveResetCode(int $studentId, string $code, string $expiresAt): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO student_password_resets(student_id, reset_code, expires_at, created_at) VALUES(:student_id, :reset_code, :expires_at, NOW())');
        $stmt->execute([
            'student_id' => $studentId,
            'reset_code' => $code,
            'expires_at' => $expiresAt,
        ]);
    }

    public function findValidResetCode(string $email, string $code): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT spr.*, sa.email
            FROM student_password_resets spr
            INNER JOIN student_accounts sa ON sa.id = spr.student_id
            WHERE sa.email = :email AND spr.reset_code = :reset_code AND spr.used_at IS NULL AND spr.expires_at >= NOW()
            ORDER BY spr.id DESC
            LIMIT 1
        ');
        $stmt->execute([
            'email' => $email,
            'reset_code' => $code,
        ]);
        return $stmt->fetch() ?: null;
    }

    public function markResetCodeUsed(int $resetId): void
    {
        $stmt = $this->pdo->prepare('UPDATE student_password_resets SET used_at = NOW() WHERE id = :id');
        $stmt->execute(['id' => $resetId]);
    }

    public function updateStudentPassword(int $studentId, string $passwordHash): void
    {
        $stmt = $this->pdo->prepare('UPDATE student_accounts SET password = :password WHERE id = :id');
        $stmt->execute(['password' => $passwordHash, 'id' => $studentId]);
    }

    public function updateStudentProfile(int $studentId, array $data): bool
    {
        $stmt = $this->pdo->prepare(<<<SQL
            UPDATE student_accounts SET
                name = :name,
                email = :email,
                phone = :phone,
                national_id = :national_id,
                county = :county,
                sub_county = :sub_county,
                updated_at = NOW()
            WHERE id = :id
        SQL);

        return $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'national_id' => $data['national_id'],
            'county' => $data['county'],
            'sub_county' => $data['sub_county'],
            'id' => $studentId,
        ]);
    }

    public function allStudents(): array
    {
        try {
            return $this->pdo->query('SELECT * FROM student_accounts ORDER BY id DESC')->fetchAll();
        } catch (PDOException) {
            return [];
        }
    }

    public function assignAdmissionNumber(int $studentId, string $admissionNumber): bool
    {
        $stmt = $this->pdo->prepare('UPDATE student_accounts SET admission_number = :admission_number WHERE id = :id');
        return $stmt->execute([
            'admission_number' => $admissionNumber,
            'id' => $studentId,
        ]);
    }

    public function latestTimetables(int $limit = 10): array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM student_timetables ORDER BY created_at DESC LIMIT :lim');
            $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException) {
            return [];
        }
    }

    public function latestAnnouncements(int $limit = 10): array
    {
        try {
            $stmt = $this->pdo->prepare('
                SELECT id, title, body, created_at FROM (
                    SELECT sa.id, sa.title, sa.body, sa.created_at
                    FROM student_announcements sa
                    UNION ALL
                    SELECT
                        (1000000 + e.id) AS id,
                        CONCAT("Event: ", e.title) AS title,
                        COALESCE(NULLIF(TRIM(e.portal_announcement_text), ""), e.summary, e.body, "New event update is available.") AS body,
                        e.created_at
                    FROM events e
                    WHERE COALESCE(e.publish_to_portal, 0) = 1
                ) AS announcements
                ORDER BY created_at DESC
                LIMIT :lim
            ');
            $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException) {
            return [];
        }
    }

    public function allPortalCourses(): array
    {
        try {
            return $this->pdo->query('
                SELECT pc.*, p.name AS programme_name, u.name AS teacher_name
                FROM portal_courses pc
                LEFT JOIN programmes p ON p.id = pc.programme_id
                LEFT JOIN users u ON u.id = pc.teacher_id
                ORDER BY pc.created_at DESC
            ')->fetchAll();
        } catch (PDOException) {
            return [];
        }
    }

    public function getStudentCourses(int $programmeId): array
    {
        try {
            // Get distinct courses for the student's programme using junction table
            // This prevents duplicate units when a course is shared across multiple programmes
            $stmt = $this->pdo->prepare('
                SELECT DISTINCT pc.id, pc.code, pc.title, pc.description, pc.teacher_id,
                       p.name AS programme_name, u.name AS teacher_name
                FROM portal_courses pc
                INNER JOIN portal_course_programmes pcp ON pcp.portal_course_id = pc.id
                LEFT JOIN programmes p ON p.id = pcp.programme_id
                LEFT JOIN users u ON u.id = pc.teacher_id
                WHERE pcp.programme_id = :programme_id
                ORDER BY pc.code ASC
            ');
            $stmt->execute(['programme_id' => $programmeId]);
            return $stmt->fetchAll();
        } catch (PDOException) {
            return [];
        }
    }

    public function allProgrammeTimetables(): array
    {
        try {
            return $this->pdo->query('
                SELECT pt.*, p.name AS programme_name
                FROM programme_timetables pt
                LEFT JOIN programmes p ON p.id = pt.programme_id
                ORDER BY pt.created_at DESC
            ')->fetchAll();
        } catch (PDOException) {
            return [];
        }
    }

    public function allCourseGrades(): array
    {
        try {
            return $this->pdo->query('
                SELECT cg.*, sa.name AS student_name, pc.title AS course_title, pc.code AS course_code
                FROM course_grades cg
                LEFT JOIN student_accounts sa ON sa.id = cg.student_id
                LEFT JOIN portal_courses pc ON pc.id = cg.course_id
                ORDER BY cg.created_at DESC
            ')->fetchAll();
        } catch (PDOException) {
            return [];
        }
    }

    public function getStudentGradesTable(int $studentId): array
    {
        try {
            $gradingSystemStmt = $this->pdo->query('
                SELECT
                    gs.id,
                    gs.name,
                    gs.exam_type_id,
                    et.name AS exam_type_name,
                    et.code AS exam_type_code,
                    et.type AS exam_type
                FROM grading_systems gs
                LEFT JOIN exam_types et ON et.id = gs.exam_type_id
                WHERE gs.is_active = 1
                ORDER BY gs.id ASC
            ');

            $examColumns = [];
            $gradingSystemByExamType = [];
            foreach ($gradingSystemStmt->fetchAll(PDO::FETCH_ASSOC) as $gradingSystemRow) {
                $gradingSystemId = (int)($gradingSystemRow['id'] ?? 0);
                if ($gradingSystemId <= 0) {
                    continue;
                }

                $label = trim((string)($gradingSystemRow['name'] ?? ''));
                if ($label === '') {
                    $label = trim((string)($gradingSystemRow['exam_type_name'] ?? $gradingSystemRow['exam_type_code'] ?? ''));
                }
                if ($label === '') {
                    $label = 'Grading System ' . $gradingSystemId;
                }

                $examTypeId = (int)($gradingSystemRow['exam_type_id'] ?? 0);
                $examColumns[$gradingSystemId] = [
                    'id' => $gradingSystemId,
                    'label' => $label,
                    'exam_type_id' => $examTypeId,
                    'type' => (string)($gradingSystemRow['exam_type'] ?? ''),
                ];

                if ($examTypeId > 0 && !isset($gradingSystemByExamType[$examTypeId])) {
                    $gradingSystemByExamType[$examTypeId] = $gradingSystemId;
                }
            }

            $stmt = $this->pdo->prepare('
                SELECT
                    cg.*,
                    pc.title AS course_title,
                    pc.code AS course_code,
                    gs.name AS grading_system_name,
                    gs.exam_type_id AS grading_system_exam_type_id,
                    et.name AS exam_name,
                    et.code AS exam_code,
                    et.type AS exam_type
                FROM course_grades cg
                LEFT JOIN portal_courses pc ON pc.id = cg.course_id
                LEFT JOIN grading_systems gs ON gs.id = cg.grading_system_id
                LEFT JOIN exam_types et ON et.id = COALESCE(cg.exam_type_id, gs.exam_type_id)
                WHERE cg.student_id = :student_id
                ORDER BY pc.code ASC, cg.created_at ASC, cg.id ASC
            ');
            $stmt->execute(['student_id' => $studentId]);
            $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException) {
            return [
                'examColumns' => [],
                'rows' => [],
            ];
        }

        $rowsByCourse = [];

        foreach ($records as $record) {
            $gradingSystemId = (int)($record['grading_system_id'] ?? 0);
            $examTypeId = (int)($record['exam_type_id'] ?? 0);
            if ($examTypeId <= 0) {
                $examTypeId = (int)($record['grading_system_exam_type_id'] ?? 0);
            }
            if ($gradingSystemId <= 0 && $examTypeId > 0) {
                $gradingSystemId = (int)($gradingSystemByExamType[$examTypeId] ?? 0);
            }

            if ($gradingSystemId > 0 && !isset($examColumns[$gradingSystemId])) {
                $examLabel = trim((string)($record['grading_system_name'] ?? $record['exam_name'] ?? $record['exam_code'] ?? ''));
                if ($examLabel === '') {
                    $examLabel = 'Grading System ' . $gradingSystemId;
                }
                $examColumns[$gradingSystemId] = [
                    'id' => $gradingSystemId,
                    'label' => $examLabel,
                    'exam_type_id' => $examTypeId,
                    'type' => (string)($record['exam_type'] ?? ''),
                ];
            }

            $courseId = (int)($record['course_id'] ?? 0);
            $courseKey = $courseId > 0 ? (string)$courseId : 'grade_' . (string)($record['id'] ?? uniqid('', true));
            if (!isset($rowsByCourse[$courseKey])) {
                $rowsByCourse[$courseKey] = [
                    'course_code' => (string)($record['course_code'] ?? 'N/A'),
                    'course_title' => (string)($record['course_title'] ?? 'Unnamed Unit'),
                    'exam_marks' => [],
                    'grade' => '',
                    'comment' => '',
                    'sort_created_at' => (string)($record['created_at'] ?? ''),
                ];
            }

            if ($gradingSystemId > 0) {
                $rowsByCourse[$courseKey]['exam_marks'][$gradingSystemId] = $record['marks'] ?? $record['marks_percentage'] ?? null;
            }

            $isPreferredSummary = (string)($record['exam_type'] ?? '') === 'consolidated' || $rowsByCourse[$courseKey]['grade'] === '';
            if ($isPreferredSummary) {
                $rowsByCourse[$courseKey]['grade'] = (string)($record['grade'] ?? '');
                $rowsByCourse[$courseKey]['comment'] = (string)($record['remarks'] ?? '');
            }

            if ($rowsByCourse[$courseKey]['comment'] === '' && !empty($record['remarks'])) {
                $rowsByCourse[$courseKey]['comment'] = (string)$record['remarks'];
            }
        }

        ksort($examColumns);

        $rows = array_values(array_map(function (array $row) use ($examColumns): array {
            foreach ($examColumns as $examColumn) {
                $gradingSystemId = (int)$examColumn['id'];
                if (!array_key_exists($gradingSystemId, $row['exam_marks'])) {
                    $row['exam_marks'][$gradingSystemId] = null;
                }
            }

            return $row;
        }, $rowsByCourse));

        return [
            'examColumns' => array_values($examColumns),
            'rows' => $rows,
        ];
    }

    public function getStudentTranscriptData(int $studentId): array
    {
        $gradesTable = $this->getStudentGradesTable($studentId);

        try {
            $studentStmt = $this->pdo->prepare('
                SELECT
                    sa.id,
                    sa.name,
                    sa.admission_number,
                    sa.email,
                    p.name AS programme_name
                FROM student_accounts sa
                LEFT JOIN programmes p ON p.id = sa.programme_id
                WHERE sa.id = :student_id
                LIMIT 1
            ');
            $studentStmt->execute(['student_id' => $studentId]);
            $student = $studentStmt->fetch(PDO::FETCH_ASSOC) ?: [];

            $periodStmt = $this->pdo->prepare('
                SELECT
                    t.name AS term_name,
                    ay.name AS session_name
                FROM course_grades cg
                LEFT JOIN terms t ON t.id = cg.term_id
                LEFT JOIN academic_years ay ON ay.id = cg.academic_session_id
                WHERE cg.student_id = :student_id
                ORDER BY
                    cg.academic_session_id DESC,
                    cg.term_id DESC,
                    cg.created_at DESC,
                    cg.id DESC
                LIMIT 1
            ');
            $periodStmt->execute(['student_id' => $studentId]);
            $academicPeriod = $periodStmt->fetch(PDO::FETCH_ASSOC) ?: [];

            if (empty($academicPeriod['term_name'])) {
                $termStmt = $this->pdo->query('SELECT name FROM terms WHERE is_current = 1 ORDER BY id DESC LIMIT 1');
                $academicPeriod['term_name'] = $termStmt->fetchColumn() ?: '';
            }

            if (empty($academicPeriod['session_name'])) {
                $sessionStmt = $this->pdo->query('SELECT name FROM academic_years WHERE is_current = 1 ORDER BY id DESC LIMIT 1');
                $academicPeriod['session_name'] = $sessionStmt->fetchColumn() ?: '';
            }

            $settingsStmt = $this->pdo->prepare('SELECT setting_key, setting_value FROM settings WHERE setting_key IN (?, ?, ?)');
            $settingsStmt->execute(['phone', 'email', 'location']);
            $settings = [];
            foreach ($settingsStmt->fetchAll(PDO::FETCH_ASSOC) as $settingRow) {
                $settings[(string)$settingRow['setting_key']] = (string)($settingRow['setting_value'] ?? '');
            }
        } catch (PDOException) {
            return [
                'student' => [],
                'settings' => [],
                'term_name' => '',
                'session_name' => '',
                'examColumns' => $gradesTable['examColumns'] ?? [],
                'rows' => $gradesTable['rows'] ?? [],
            ];
        }

        return [
            'student' => $student,
            'settings' => $settings,
            'term_name' => (string)($academicPeriod['term_name'] ?? ''),
            'session_name' => (string)($academicPeriod['session_name'] ?? ''),
            'examColumns' => $gradesTable['examColumns'] ?? [],
            'rows' => $gradesTable['rows'] ?? [],
        ];
    }

    public function allAssignments(): array
    {
        try {
            return $this->pdo->query('
                SELECT ca.*, pc.title AS course_title, pc.code AS course_code
                FROM course_assignments ca
                LEFT JOIN portal_courses pc ON pc.id = ca.course_id
                ORDER BY ca.created_at DESC
            ')->fetchAll();
        } catch (PDOException) {
            return [];
        }
    }

    public function allStudyMaterials(): array
    {
        try {
            return $this->pdo->query('
                SELECT sm.*, pc.title AS course_title, pc.code AS course_code
                FROM study_materials sm
                LEFT JOIN portal_courses pc ON pc.id = sm.course_id
                ORDER BY sm.created_at DESC
            ')->fetchAll();
        } catch (PDOException) {
            return [];
        }
    }

    public function allLibraryResources(): array
    {
        try {
            return $this->pdo->query('SELECT * FROM library_resources ORDER BY created_at DESC')->fetchAll();
        } catch (PDOException) {
            return [];
        }
    }

    public function createSupportTicket(array $data): bool
    {
        try {
            $prefix = '[Portal Support]';
            $subjectBits = array_filter([
                $prefix,
                trim((string)($data['category'] ?? 'General')),
                trim((string)($data['subject'] ?? 'Ticket')),
            ]);
            $subject = implode(' ', $subjectBits);
            $admission = trim((string)($data['admission_number'] ?? ''));
            $ticketBody = trim((string)($data['message'] ?? ''));
            $message = ($admission !== '' ? ('Admission: ' . $admission . "\n") : '') . $ticketBody;
            $stmt = $this->pdo->prepare('INSERT INTO messages(name, email, phone, subject, message, created_at) VALUES(:name, :email, :phone, :subject, :message, NOW())');
            return $stmt->execute([
                'name' => trim((string)($data['name'] ?? 'Student')),
                'email' => trim((string)($data['email'] ?? '')),
                'phone' => '',
                'subject' => $subject,
                'message' => $message,
            ]);
        } catch (PDOException) {
            return false;
        }
    }

    public function getSupportTicketsByStudent(int $studentId): array
    {
        try {
            $student = $this->findStudentById($studentId);
            if ($student === null) {
                return [];
            }
            $email = trim((string)($student['email'] ?? ''));
            if ($email === '') {
                return [];
            }
            $stmt = $this->pdo->prepare("SELECT id, subject, message, created_at FROM messages WHERE email = :email AND subject LIKE '[Portal Support]%' ORDER BY id DESC");
            $stmt->execute(['email' => $email]);
            return $stmt->fetchAll();
        } catch (PDOException) {
            return [];
        }
    }

    public function getAllSupportTickets(): array
    {
        try {
            return $this->pdo->query("SELECT id, name, email, subject, message, created_at FROM messages WHERE subject LIKE '[Portal Support]%' ORDER BY id DESC")->fetchAll();
        } catch (PDOException) {
            return [];
        }
    }

    public function getStudentInvoices(int $studentId): array
    {
        try {
            // Check which session table exists
            $sessionsTable = 'academic_sessions';
            $stmt = $this->pdo->query("SHOW TABLES LIKE 'academic_sessions'");
            if ($stmt->rowCount() === 0) {
                $stmt = $this->pdo->query("SHOW TABLES LIKE 'sessions'");
                if ($stmt->rowCount() > 0) {
                    $sessionsTable = 'sessions';
                }
            }

            $sql = "
                SELECT i.*, p.name AS programme_name, p.abbreviation AS programme_abbr,
                       t.name AS term_name, ses.name AS session_name,
                       COALESCE(SUM(pay.amount), 0) AS paid_amount,
                       i.amount - COALESCE(SUM(pay.amount), 0) AS balance
                FROM invoices i
                LEFT JOIN programmes p ON i.programme_id = p.id
                LEFT JOIN terms t ON i.term_id = t.id
                LEFT JOIN {$sessionsTable} ses ON i.academic_session_id = ses.id
                LEFT JOIN payments pay ON i.id = pay.invoice_id
                WHERE i.student_id = :student_id AND i.status != 'cancelled'
                GROUP BY i.id
                ORDER BY i.created_at DESC
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['student_id' => $studentId]);
            return $stmt->fetchAll();
        } catch (PDOException) {
            return [];
        }
    }

    public function getStudentPayments(int $studentId): array
    {
        try {
            $stmt = $this->pdo->prepare('
                SELECT p.*, i.invoice_number, i.title AS invoice_title, pm.name AS payment_method_name
                FROM payments p
                LEFT JOIN invoices i ON p.invoice_id = i.id
                LEFT JOIN payment_methods pm ON p.payment_method_id = pm.id
                WHERE p.student_id = :student_id
                ORDER BY p.payment_date DESC
            ');
            $stmt->execute(['student_id' => $studentId]);
            return $stmt->fetchAll();
        } catch (PDOException) {
            return [];
        }
    }

    public function getStudentBalance(int $studentId): array
    {
        try {
            // Get total invoiced
            $stmt = $this->pdo->prepare('SELECT COALESCE(SUM(amount), 0) AS total_invoiced FROM invoices WHERE student_id = ? AND status != "cancelled"');
            $stmt->execute([$studentId]);
            $totalInvoiced = $stmt->fetchColumn();

            // Get total paid
            $stmt = $this->pdo->prepare('SELECT COALESCE(SUM(p.amount), 0) AS total_paid FROM payments p LEFT JOIN invoices i ON p.invoice_id = i.id WHERE i.student_id = ? AND i.status != "cancelled"');
            $stmt->execute([$studentId]);
            $totalPaid = $stmt->fetchColumn();

            $balance = $totalInvoiced - $totalPaid;

            return [
                'total_invoiced' => $totalInvoiced,
                'total_paid' => $totalPaid,
                'balance' => $balance
            ];
        } catch (PDOException) {
            return ['total_invoiced' => 0, 'total_paid' => 0, 'balance' => 0];
        }
    }

    public function getInvoiceById(int $invoiceId): ?array
    {
        try {
            $stmt = $this->pdo->prepare('
                SELECT i.*, s.name AS student_name, s.admission_number, s.email AS student_email,
                       p.name AS programme_name, p.abbreviation AS programme_abbr,
                       t.name AS term_name, ses.name AS session_name
                FROM invoices i
                LEFT JOIN student_accounts s ON i.student_id = s.id
                LEFT JOIN programmes p ON i.programme_id = p.id
                LEFT JOIN terms t ON i.term_id = t.id
                LEFT JOIN academic_sessions ses ON i.academic_session_id = ses.id
                WHERE i.id = :id
            ');
            $stmt->execute(['id' => $invoiceId]);
            return $stmt->fetch() ?: null;
        } catch (PDOException) {
            return null;
        }
    }

    public function getInvoicePayments(int $invoiceId): array
    {
        try {
            $stmt = $this->pdo->prepare('
                SELECT p.*, pm.name AS payment_method_name
                FROM payments p
                LEFT JOIN payment_methods pm ON p.payment_method_id = pm.id
                WHERE p.invoice_id = :invoice_id
                ORDER BY p.payment_date DESC
            ');
            $stmt->execute(['invoice_id' => $invoiceId]);
            return $stmt->fetchAll();
        } catch (PDOException) {
            return [];
        }
    }
}
