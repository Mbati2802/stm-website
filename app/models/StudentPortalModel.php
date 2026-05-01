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
}
