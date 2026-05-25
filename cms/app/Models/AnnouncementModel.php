<?php
class AnnouncementModel extends BaseModel {
    public function getAll(int $page=1, int $perPage=ITEMS_PER_PAGE, string $category=''): array {
        $offset=($page-1)*$perPage;
        $where=''; $params=[];
        if($category){$where='WHERE a.category=?';$params[]=$category;}
        $rows=$this->fetchAll(
            "SELECT a.*,u.name AS author_name FROM announcements a
             LEFT JOIN users u ON u.id=a.admin_id
             $where ORDER BY a.is_pinned DESC,a.id DESC LIMIT ? OFFSET ?",
            array_merge($params,[$perPage,$offset])
        );
        $total=$this->countQuery("SELECT COUNT(*) FROM announcements a $where",$params);
        return ['rows'=>$rows,'total'=>$total];
    }
    public function findById(int $id): ?array {
        return $this->fetch('SELECT a.*,u.name AS author_name FROM announcements a LEFT JOIN users u ON u.id=a.admin_id WHERE a.id=?',[$id]);
    }
    public function create(array $d, int $userId): string {
        return $this->insert(
            'INSERT INTO announcements(admin_id,title,content,link,image,category,is_pinned,is_active) VALUES(?,?,?,?,?,?,?,?)',
            [$userId,$d['title'],$d['content'],$d['link']??null,$d['image']??null,$d['category']??'general',$d['is_pinned']??0,$d['is_active']??1]
        );
    }
    public function update(int $id, array $d): int {
        $f=['title=?','content=?','link=?','category=?','is_pinned=?','is_active=?','updated_at=NOW()'];
        $p=[$d['title'],$d['content'],$d['link']??null,$d['category']??'general',$d['is_pinned']??0,$d['is_active']??1];
        if(isset($d['image'])){$f[]='image=?';$p[]=$d['image'];}
        $p[]=$id;
        return $this->execute('UPDATE announcements SET '.implode(',',$f).' WHERE id=?',$p);
    }
    public function delete(int $id): int { return $this->execute('DELETE FROM announcements WHERE id=?',[$id]); }
    public function incrementViews(int $id): void { $this->execute('UPDATE announcements SET views=views+1 WHERE id=?',[$id]); }
    public function togglePin(int $id): void {
        $this->execute('UPDATE announcements SET is_pinned=IF(is_pinned=1,0,1) WHERE id=?',[$id]);
    }
    public function buildPagination(int $total, int $cur, int $perPage=ITEMS_PER_PAGE): array {
        $totalPages=(int)ceil($total/$perPage);
        $half=(int)floor(PAGE_RANGE/2);
        $start=max(1,$cur-$half); $end=min($totalPages,$start+PAGE_RANGE-1);
        if($end-$start+1<PAGE_RANGE) $start=max(1,$end-PAGE_RANGE+1);
        return ['total'=>$total,'per_page'=>$perPage,'current'=>$cur,'total_pages'=>$totalPages,'start_page'=>$start,'end_page'=>$end,'has_prev'=>$cur>1,'has_next'=>$cur<$totalPages];
    }
}
