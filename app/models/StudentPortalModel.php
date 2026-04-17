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

    public function createStudent(string $name, string $email, string $passwordHash): bool
    {
        $stmt = $this->pdo->prepare('INSERT INTO student_accounts(name, email, password, created_at) VALUES(:name, :email, :password, NOW())');
        return $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => $passwordHash,
        ]);
    }

    public function findStudentById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM student_accounts WHERE id = :id LIMIT 1');
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
            $stmt = $this->pdo->prepare('SELECT * FROM student_announcements ORDER BY created_at DESC LIMIT :lim');
            $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException) {
            return [];
        }
    }
}
