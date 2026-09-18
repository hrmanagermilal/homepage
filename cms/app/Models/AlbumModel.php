<?php
class AlbumModel extends BaseModel {
    public function getAll(int $page = 1, int $perPage = ITEMS_PER_PAGE): array {
        $offset = ($page - 1) * $perPage;
        $rows = $this->fetchAll(
            'SELECT id, title, date, created_at FROM album ORDER BY created_at DESC LIMIT ? OFFSET ?',
            [$perPage, $offset]
        );
        $total = $this->countQuery('SELECT COUNT(*) FROM album');
        return ['rows' => $rows, 'total' => $total];
    }

    public function findById(int $id): ?array {
        return $this->fetch('SELECT * FROM album WHERE id = ?', [$id]);
    }

    public function create(array $data): string {
        return $this->insert(
            'INSERT INTO album (title, content, date, is_active) VALUES (?, ?, ?, ?)',
            [$data['title'], $data['content'], $data['date'] ?? date('Y-m-d'), $data['is_active'] ?? 1]
        );
    }

    public function delete(int $id): int {
        return $this->execute('DELETE FROM album WHERE id = ?', [$id]);
    }

    public function getImages(int $albumId): array {
        return $this->fetchAll(
            'SELECT id, album_id, image_url, alt_text, sort_order, uploaded_at
             FROM album_images
             WHERE album_id = ?
             ORDER BY sort_order ASC, id ASC',
            [$albumId]
        );
    }

    public function addImage(int $albumId, string $imageUrl, string $altText, int $sortOrder): string {
        return $this->insert(
            'INSERT INTO album_images (album_id, image_url, alt_text, sort_order) VALUES (?, ?, ?, ?)',
            [$albumId, $imageUrl, $altText, $sortOrder]
        );
    }

    public function buildPagination(int $total, int $cur, int $perPage = ITEMS_PER_PAGE): array {
        $totalPages = max(1, (int)ceil($total / $perPage));
        $half = (int)floor(PAGE_RANGE / 2);
        $start = max(1, $cur - $half);
        $end = min($totalPages, $start + PAGE_RANGE - 1);
        if ($end - $start + 1 < PAGE_RANGE) {
            $start = max(1, $end - PAGE_RANGE + 1);
        }

        return [
            'total' => $total,
            'per_page' => $perPage,
            'current' => $cur,
            'total_pages' => $totalPages,
            'start_page' => $start,
            'end_page' => $end,
            'has_prev' => $cur > 1,
            'has_next' => $cur < $totalPages,
        ];
    }
}
