<?php
class MemberModel extends BaseModel {
    public function getAll(int $page=1, int $perPage=ITEMS_PER_PAGE): array {
        $offset=($page-1)*$perPage;
        $rows=$this->fetchAll('SELECT * FROM members ORDER BY sort_order ASC,id ASC LIMIT ? OFFSET ?',[$perPage,$offset]);
        $total=$this->countQuery('SELECT COUNT(*) FROM members');
        return ['rows'=>$rows,'total'=>$total];
    }
    public function getAllActive(): array {
        return $this->fetchAll('SELECT * FROM members WHERE is_active=1 ORDER BY sort_order ASC');
    }
    public function findById(int $id): ?array { return $this->fetch('SELECT * FROM members WHERE id=?',[$id]); }
    public function create(array $d): string {
        return $this->insert(
            'INSERT INTO members(name,name_en,email,title,category,role,picture,position,biography,tags,tags_en,sort_order,is_active)
             VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)',
            [
                $d['name'],
                $d['name_en']??null,
                $d['email']??null,
                $d['title']??null,
                $d['category']??'간사',
                $d['role']??null,
                $d['picture']??null,
                $d['position']??null,
                $d['biography']??null,
                $d['tags']??null,
                $d['tags_en']??null,
                $d['sort_order']??0,
                $d['is_active']??1,
            ]
        );
    }
    public function update(int $id, array $d): int {
        $f=['name=?','name_en=?','email=?','title=?','category=?','role=?','position=?','biography=?','tags=?','tags_en=?','sort_order=?','is_active=?','updated_at=NOW()'];
        $p=[
            $d['name'],
            $d['name_en']??null,
            $d['email']??null,
            $d['title']??null,
            $d['category']??'간사',
            $d['role']??null,
            $d['position']??null,
            $d['biography']??null,
            $d['tags']??null,
            $d['tags_en']??null,
            $d['sort_order']??0,
            $d['is_active']??1,
        ];
        if(isset($d['picture'])){$f[]='picture=?';$p[]=$d['picture'];}
        $p[]=$id;
        return $this->execute('UPDATE members SET '.implode(',',$f).' WHERE id=?',$p);
    }
    public function delete(int $id): int { return $this->execute('DELETE FROM members WHERE id=?',[$id]); }
    public function updateOrder(array $orders): void {
        foreach($orders as $item) $this->execute('UPDATE members SET sort_order=? WHERE id=?',[(int)$item['order'],(int)$item['id']]);
    }
    public function buildPagination(int $total, int $cur, int $perPage=ITEMS_PER_PAGE): array {
        $totalPages=(int)ceil($total/$perPage);
        $half=(int)floor(PAGE_RANGE/2);
        $start=max(1,$cur-$half);
        $end=min($totalPages,$start+PAGE_RANGE-1);
        if($end-$start+1<PAGE_RANGE) $start=max(1,$end-PAGE_RANGE+1);
        return ['total'=>$total,'per_page'=>$perPage,'current'=>$cur,'total_pages'=>$totalPages,'start_page'=>$start,'end_page'=>$end,'has_prev'=>$cur>1,'has_next'=>$cur<$totalPages];
    }
}
