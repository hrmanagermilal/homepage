<?php
class NoticeModel extends BaseModel {

    public function getAll(int $page=1, int $perPage=ITEMS_PER_PAGE, string $level=''): array {
        $offset = ($page-1) * $perPage;
        $where = ''; $params = [];
        if ($level) { $where = 'WHERE emergency_level=?'; $params[] = $level; }
        $rows  = $this->fetchAll("SELECT * FROM notice $where ORDER BY emergency_level='urgent' DESC, created_date DESC, id DESC LIMIT ? OFFSET ?",
                                 array_merge($params, [$perPage, $offset]));
        $total = $this->countQuery("SELECT COUNT(*) FROM notice $where", $params);
        return ['rows' => $rows, 'total' => $total];
    }

    public function findById(int $id): ?array {
        return $this->fetch('SELECT * FROM notice WHERE id=?', [$id]);
    }

    public function create(array $d): string {
        return $this->insert(
            'INSERT INTO notice(title,content,writer_name,emergency_level,link,link_text,image,created_date,views)
             VALUES(?,?,?,?,?,?,?,?,0)',
            [$d['title'], $d['content'], $d['writer_name'], $d['emergency_level']??'normal',
             $d['link']??null, $d['link_text']??null, $d['image']??null, $d['created_date']]
        );
    }

    public function update(int $id, array $d): int {
        $f = ['title=?','content=?','writer_name=?','emergency_level=?','link=?','link_text=?','created_date=?','updated_at=NOW()'];
        $p = [$d['title'], $d['content'], $d['writer_name'], $d['emergency_level']??'normal',
              $d['link']??null, $d['link_text']??null, $d['created_date']];
        if (isset($d['image'])) { $f[] = 'image=?'; $p[] = $d['image']; }
        $p[] = $id;
        return $this->execute('UPDATE notice SET ' . implode(',', $f) . ' WHERE id=?', $p);
    }

    public function delete(int $id): int {
        return $this->execute('DELETE FROM notice WHERE id=?', [$id]);
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
