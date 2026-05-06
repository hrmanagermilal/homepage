<?php
class BulletinModel extends BaseModel {
    public function getAll(int $page=1, int $perPage=ITEMS_PER_PAGE): array {
        $offset=($page-1)*$perPage;
        $rows=$this->fetchAll(
            'SELECT b.*,COUNT(bi.id) AS image_count FROM bulletins b
             LEFT JOIN bulletin_images bi ON bi.bulletin_id=b.id
             GROUP BY b.id ORDER BY b.year DESC,b.week_number DESC LIMIT ? OFFSET ?',
            [$perPage,$offset]
        );
        $total=$this->countQuery('SELECT COUNT(*) FROM bulletins');
        return ['rows'=>$rows,'total'=>$total];
    }
    public function findById(int $id): ?array { return $this->fetch('SELECT * FROM bulletins WHERE id=?',[$id]); }
    public function create(array $d): string {
        return $this->insert('INSERT INTO bulletins(title,week_number,year) VALUES(?,?,?)',[$d['title'],$d['week_number']??null,$d['year']??date('Y')]);
    }
    public function update(int $id, array $d): int {
        return $this->execute('UPDATE bulletins SET title=?,week_number=?,year=?,updated_at=NOW() WHERE id=?',[$d['title'],$d['week_number']??null,$d['year']??date('Y'),$id]);
    }
    public function delete(int $id): int { return $this->execute('DELETE FROM bulletins WHERE id=?',[$id]); }
    // Images
    public function getImages(int $bulletinId): array {
        return $this->fetchAll('SELECT * FROM bulletin_images WHERE bulletin_id=? ORDER BY `order` ASC',[$bulletinId]);
    }
    public function addImage(int $bulletinId, string $url, int $order=0): string {
        return $this->insert('INSERT INTO bulletin_images(bulletin_id,image_url,`order`) VALUES(?,?,?)',[$bulletinId,$url,$order]);
    }
    public function deleteImage(int $id): ?array {
        $row=$this->fetch('SELECT * FROM bulletin_images WHERE id=?',[$id]);
        if($row) $this->execute('DELETE FROM bulletin_images WHERE id=?',[$id]);
        return $row;
    }
    public function reorderImages(array $orders): void {
        foreach($orders as $item) $this->execute('UPDATE bulletin_images SET `order`=? WHERE id=?',[(int)$item['order'],(int)$item['id']]);
    }
    public function buildPagination(int $total, int $cur, int $perPage=ITEMS_PER_PAGE): array {
        $totalPages=(int)ceil($total/$perPage);
        $half=(int)floor(PAGE_RANGE/2);
        $start=max(1,$cur-$half); $end=min($totalPages,$start+PAGE_RANGE-1);
        if($end-$start+1<PAGE_RANGE) $start=max(1,$end-PAGE_RANGE+1);
        return ['total'=>$total,'per_page'=>$perPage,'current'=>$cur,'total_pages'=>$totalPages,'start_page'=>$start,'end_page'=>$end,'has_prev'=>$cur>1,'has_next'=>$cur<$totalPages];
    }
}
