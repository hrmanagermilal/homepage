<?php
class NewsModel extends BaseModel {
    public function getAll(int $page=1, int $perPage=ITEMS_PER_PAGE, string $category=''): array {
        $offset=($page-1)*$perPage;
        $where=''; $p=[];
        if($category){$where='WHERE category=?';$p[]=$category;}
        $rows=$this->fetchAll("SELECT * FROM news $where ORDER BY id DESC LIMIT ? OFFSET ?",array_merge($p,[$perPage,$offset]));
        $total=$this->countQuery("SELECT COUNT(*) FROM news $where",$p);
        return ['rows'=>$rows,'total'=>$total];
    }
    public function findById(int $id): ?array { return $this->fetch('SELECT * FROM news WHERE id=?',[$id]); }
    public function create(array $d): string {
        return $this->insert('INSERT INTO news(title,content,image,author,category,tags) VALUES(?,?,?,?,?,?)',
            [$d['title'],$d['content'],$d['image']??null,$d['author']??null,$d['category']??'news',$d['tags']??null]);
    }
    public function update(int $id, array $d): int {
        $f=['title=?','content=?','author=?','category=?','tags=?','updated_at=NOW()'];
        $p=[$d['title'],$d['content'],$d['author']??null,$d['category']??'news',$d['tags']??null];
        if(isset($d['image'])){$f[]='image=?';$p[]=$d['image'];}
        $p[]=$id;
        return $this->execute('UPDATE news SET '.implode(',',$f).' WHERE id=?',$p);
    }
    public function delete(int $id): int { return $this->execute('DELETE FROM news WHERE id=?',[$id]); }
    public function incrementViews(int $id): void { $this->execute('UPDATE news SET views=views+1 WHERE id=?',[$id]); }
    public function buildPagination(int $total, int $cur, int $perPage=ITEMS_PER_PAGE): array {
        $totalPages=(int)ceil($total/$perPage);
        $half=(int)floor(PAGE_RANGE/2);
        $start=max(1,$cur-$half); $end=min($totalPages,$start+PAGE_RANGE-1);
        if($end-$start+1<PAGE_RANGE) $start=max(1,$end-PAGE_RANGE+1);
        return ['total'=>$total,'per_page'=>$perPage,'current'=>$cur,'total_pages'=>$totalPages,'start_page'=>$start,'end_page'=>$end,'has_prev'=>$cur>1,'has_next'=>$cur<$totalPages];
    }
}
