<?php
// NOTE: `news` table does not exist in the current schema.
// All methods return empty data / no-ops to prevent 500 errors.
class NewsModel extends BaseModel {
    public function getAll(int $page=1, int $perPage=ITEMS_PER_PAGE, string $category=''): array {
        try {
            $offset=($page-1)*$perPage;
            $where=''; $p=[];
            if($category){$where='WHERE category=?';$p[]=$category;}
            $rows=$this->fetchAll("SELECT * FROM news $where ORDER BY id DESC LIMIT ? OFFSET ?",array_merge($p,[$perPage,$offset]));
            $total=$this->countQuery("SELECT COUNT(*) FROM news $where",$p);
            return ['rows'=>$rows,'total'=>$total];
        } catch (PDOException $e) { return ['rows'=>[],'total'=>0]; }
    }
    public function findById(int $id): ?array {
        try { return $this->fetch('SELECT * FROM news WHERE id=?',[$id]); }
        catch (PDOException $e) { return null; }
    }
    public function create(array $d): string {
        try {
            return $this->insert('INSERT INTO news(title,content,image,author,category,tags) VALUES(?,?,?,?,?,?)',
                [$d['title'],$d['content'],$d['image']??null,$d['author']??null,$d['category']??'news',$d['tags']??null]);
        } catch (PDOException $e) { return '0'; }
    }
    public function update(int $id, array $d): int {
        try {
            $f=['title=?','content=?','author=?','category=?','tags=?','updated_at=NOW()'];
            $p=[$d['title'],$d['content'],$d['author']??null,$d['category']??'news',$d['tags']??null];
            if(isset($d['image'])){$f[]='image=?';$p[]=$d['image'];}
            $p[]=$id;
            return $this->execute('UPDATE news SET '.implode(',',$f).' WHERE id=?',$p);
        } catch (PDOException $e) { return 0; }
    }
    public function delete(int $id): int {
        try { return $this->execute('DELETE FROM news WHERE id=?',[$id]); }
        catch (PDOException $e) { return 0; }
    }
    public function incrementViews(int $id): void {
        try { $this->execute('UPDATE news SET views=views+1 WHERE id=?',[$id]); }
        catch (PDOException $e) { /* silently skip */ }
    }
    public function buildPagination(int $total, int $cur, int $perPage=ITEMS_PER_PAGE): array {
        $totalPages=(int)ceil($total/$perPage);
        $half=(int)floor(PAGE_RANGE/2);
        $start=max(1,$cur-$half); $end=min($totalPages,$start+PAGE_RANGE-1);
        if($end-$start+1<PAGE_RANGE) $start=max(1,$end-PAGE_RANGE+1);
        return ['total'=>$total,'per_page'=>$perPage,'current'=>$cur,'total_pages'=>$totalPages,'start_page'=>$start,'end_page'=>$end,'has_prev'=>$cur>1,'has_next'=>$cur<$totalPages];
    }
}
