<?php
class UserModel extends BaseModel {

    public function findByUsername(string $username): ?array {
        return $this->fetch(
            'SELECT u.*, r.name AS role_name, r.slug AS role_slug
             FROM users u
             JOIN roles r ON r.id = u.role_id
             WHERE u.username = ? AND u.is_active = 1',
            [$username]
        );
    }

    public function findById(int $id): ?array {
        return $this->fetch(
            'SELECT u.*, r.name AS role_name, r.slug AS role_slug
             FROM users u
             JOIN roles r ON r.id = u.role_id
             WHERE u.id = ?',
            [$id]
        );
    }

    public function getAll(int $page = 1, int $perPage = 20): array {
        $offset = ($page - 1) * $perPage;
        $rows = $this->fetchAll(
            'SELECT u.id, u.username, u.email, u.name, u.is_active, u.last_login, u.created_at,
                    r.name AS role_name
             FROM users u
             JOIN roles r ON r.id = u.role_id
             ORDER BY u.id DESC
             LIMIT ? OFFSET ?',
            [$perPage, $offset]
        );
        $total = $this->countQuery('SELECT COUNT(*) FROM users');
        return ['rows' => $rows, 'total' => $total];
    }

    public function create(array $data): string {
        return $this->insert(
            'INSERT INTO users (role_id, username, email, password_hash, name, is_active)
             VALUES (?, ?, ?, ?, ?, ?)',
            [
                $data['role_id'],
                $data['username'],
                $data['email'],
                password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]),
                $data['name'] ?? '',
                $data['is_active'] ?? 1,
            ]
        );
    }

    public function update(int $id, array $data): int {
        $fields = ['role_id=?', 'name=?', 'is_active=?', 'updated_at=NOW()'];
        $params = [$data['role_id'], $data['name'] ?? '', $data['is_active'] ?? 1];

        if (!empty($data['password'])) {
            $fields[] = 'password_hash=?';
            $params[] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        }

        $params[] = $id;
        return $this->execute(
            'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id=?',
            $params
        );
    }

    public function delete(int $id): int {
        return $this->execute('DELETE FROM users WHERE id=?', [$id]);
    }

    public function updateLastLogin(int $id): void {
        $this->execute('UPDATE users SET last_login=NOW() WHERE id=?', [$id]);
    }

    public function getPermissions(int $userId): array {
        $rows = $this->fetchAll(
            'SELECT p.slug, p.module, p.action
             FROM users u
             JOIN role_permissions rp ON rp.role_id = u.role_id
             JOIN permissions p ON p.id = rp.permission_id
             WHERE u.id = ?',
            [$userId]
        );
        $perms = [];
        foreach ($rows as $row) {
            $perms[$row['slug']] = true;
            $perms[$row['module']][$row['action']] = true;
        }
        return $perms;
    }

    public function getRoles(): array {
        return $this->fetchAll('SELECT * FROM roles ORDER BY id ASC');
    }

    public function usernameExists(string $username, int $excludeId = 0): bool {
        return (bool) $this->fetch(
            'SELECT id FROM users WHERE username=? AND id!=?',
            [$username, $excludeId]
        );
    }

    public function emailExists(string $email, int $excludeId = 0): bool {
        return (bool) $this->fetch(
            'SELECT id FROM users WHERE email=? AND id!=?',
            [$email, $excludeId]
        );
    }

    public function verifyPassword(string $plain, string $hash): bool {
        return password_verify($plain, $hash);
    }
}
