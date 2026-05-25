<?php
class SermonModel extends BaseModel {

    /* ── 설교 ─────────────────────────────────────────── */
    public function getAll(int $page=1, int $perPage=ITEMS_PER_PAGE, int $categoryId=0): array {
        $offset = ($page-1) * $perPage;
        $where  = $categoryId ? 'WHERE s.category_id=?' : '';
        $params = $categoryId ? [$categoryId, $perPage, $offset] : [$perPage, $offset];
        $rows   = $this->fetchAll(
            "SELECT s.*, sc.title AS category_name
             FROM sermons s
             LEFT JOIN sermon_categories sc ON sc.id = s.category_id
             $where ORDER BY s.sermon_date DESC, s.id DESC LIMIT ? OFFSET ?",
            $params
        );
        $countWhere  = $categoryId ? 'WHERE category_id=?' : '';
        $countParams = $categoryId ? [$categoryId] : [];
        $total = $this->countQuery("SELECT COUNT(*) FROM sermons $countWhere", $countParams);
        return ['rows' => $rows, 'total' => $total];
    }
    public function findById(int $id): ?array {
        return $this->fetch(
            'SELECT s.*, sc.title AS category_name
             FROM sermons s LEFT JOIN sermon_categories sc ON sc.id=s.category_id
             WHERE s.id=?', [$id]
        );
    }
    public function create(array $d): string {
        $ytId  = $this->extractYoutubeId($d['youtube_url'] ?? '');
        $thumb = $ytId ? "https://img.youtube.com/vi/{$ytId}/hqdefault.jpg" : null;
        return $this->insert(
            'INSERT INTO sermons(title,category_id,youtube_url,youtube_id,description,preacher,sermon_date,thumbnail) VALUES(?,?,?,?,?,?,?,?)',
            [$d['title'], $d['category_id']??null, $d['youtube_url'], $ytId,
             $d['description']??null, $d['preacher']??null, $d['sermon_date']??null, $d['thumbnail']??$thumb]
        );
    }
    public function update(int $id, array $d): int {
        $ytId  = $this->extractYoutubeId($d['youtube_url'] ?? '');
        $thumb = $ytId ? "https://img.youtube.com/vi/{$ytId}/hqdefault.jpg" : null;
        return $this->execute(
            'UPDATE sermons SET title=?,category_id=?,youtube_url=?,youtube_id=?,description=?,preacher=?,sermon_date=?,thumbnail=?,updated_at=NOW() WHERE id=?',
            [$d['title'], $d['category_id']??null, $d['youtube_url'], $ytId,
             $d['description']??null, $d['preacher']??null, $d['sermon_date']??null, $d['thumbnail']??$thumb, $id]
        );
    }
    public function delete(int $id): int { return $this->execute('DELETE FROM sermons WHERE id=?', [$id]); }
    public function urlExists(string $url, int $ex=0): bool {
        return (bool)$this->fetch('SELECT id FROM sermons WHERE youtube_url=? AND id!=?', [$url, $ex]);
    }
    public function extractYoutubeId(string $url): ?string {
        if (preg_match('/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_-]{11})/', $url, $m)) return $m[1];
        return null;
    }

    /* ── 카테고리 ─────────────────────────────────────── */
    public function getCategories(): array {
        return $this->fetchAll(
            'SELECT sc.*, COUNT(s.id) AS sermon_count
             FROM sermon_categories sc
             LEFT JOIN sermons s ON s.category_id = sc.id
             GROUP BY sc.id ORDER BY sc.title ASC'
        );
    }
    public function findCategory(int $id): ?array {
        return $this->fetch('SELECT * FROM sermon_categories WHERE id=?', [$id]);
    }
    public function createCategory(array $d): string {
        return $this->insert(
            'INSERT INTO sermon_categories(title, image) VALUES(?,?)',
            [trim($d['title']), $d['image'] ?? null]
        );
    }
    public function updateCategory(int $id, array $d): int {
        $f = ['title=?', 'updated_at=NOW()'];
        $p = [trim($d['title'])];
        if (array_key_exists('image', $d)) { $f[] = 'image=?'; $p[] = $d['image']; }
        $p[] = $id;
        return $this->execute('UPDATE sermon_categories SET ' . implode(',', $f) . ' WHERE id=?', $p);
    }
    public function deleteCategory(int $id): int {
        return $this->execute('DELETE FROM sermon_categories WHERE id=?', [$id]);
    }
    public function categoryInUse(int $id): bool {
        return (bool)$this->fetch('SELECT id FROM sermons WHERE category_id=? LIMIT 1', [$id]);
    }

    /* ── 페이지네이션 ─────────────────────────────────── */
    public function buildPagination(int $total, int $cur, int $perPage=ITEMS_PER_PAGE): array {
        $totalPages = max(1, (int)ceil($total / $perPage));
        $half  = (int)floor(PAGE_RANGE / 2);
        $start = max(1, $cur - $half);
        $end   = min($totalPages, $start + PAGE_RANGE - 1);
        if ($end - $start + 1 < PAGE_RANGE) $start = max(1, $end - PAGE_RANGE + 1);
        return ['total'=>$total,'per_page'=>$perPage,'current'=>$cur,'total_pages'=>$totalPages,
                'start_page'=>$start,'end_page'=>$end,'has_prev'=>$cur>1,'has_next'=>$cur<$totalPages];
    }
}
