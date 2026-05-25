<?php
class ObituaryModel extends BaseModel {

    public function getAll(int $page=1, int $perPage=ITEMS_PER_PAGE): array {
        $offset = ($page-1) * $perPage;
        $rows  = $this->fetchAll('SELECT * FROM obituary ORDER BY date DESC, id DESC LIMIT ? OFFSET ?', [$perPage, $offset]);
        $total = $this->countQuery('SELECT COUNT(*) FROM obituary');
        return ['rows' => $rows, 'total' => $total];
    }

    public function findById(int $id): ?array {
        return $this->fetch('SELECT * FROM obituary WHERE id=?', [$id]);
    }

    public function create(array $d): string {
        return $this->insert(
            'INSERT INTO obituary(title,description,content,date,is_active) VALUES(?,?,?,?,?)',
            [$d['title'], $d['description']??null, $d['content']??null, $d['date']??null, $d['is_active']??1]
        );
    }

    public function update(int $id, array $d): int {
        return $this->execute(
            'UPDATE obituary SET title=?,description=?,content=?,date=?,is_active=?,updated_at=NOW() WHERE id=?',
            [$d['title'], $d['description']??null, $d['content']??null, $d['date']??null, $d['is_active']??1, $id]
        );
    }

    public function delete(int $id): int {
        return $this->execute('DELETE FROM obituary WHERE id=?', [$id]);
    }

    public function buildPagination(int $total, int $cur, int $perPage=ITEMS_PER_PAGE): array {
        $totalPages = max(1, (int)ceil($total / $perPage));
        $half = (int)floor(PAGE_RANGE / 2);
        $start = max(1, $cur - $half); $end = min($totalPages, $start + PAGE_RANGE - 1);
        if ($end - $start + 1 < PAGE_RANGE) $start = max(1, $end - PAGE_RANGE + 1);
        return ['total'=>$total,'per_page'=>$perPage,'current'=>$cur,'total_pages'=>$totalPages,
                'start_page'=>$start,'end_page'=>$end,'has_prev'=>$cur>1,'has_next'=>$cur<$totalPages];
    }
}
