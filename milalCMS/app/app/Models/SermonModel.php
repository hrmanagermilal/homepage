<?php
class SermonModel extends BaseModel {
    public function getAll(int $page=1, int $perPage=ITEMS_PER_PAGE): array {
        $offset=($page-1)*$perPage;
        $rows=$this->fetchAll('SELECT * FROM sermons ORDER BY sermon_date DESC,id DESC LIMIT ? OFFSET ?',[$perPage,$offset]);
        $total=$this->countQuery('SELECT COUNT(*) FROM sermons');
        return ['rows'=>$rows,'total'=>$total];
    }
    public function findById(int $id): ?array { return $this->fetch('SELECT * FROM sermons WHERE id=?',[$id]); }
    public function create(array $d): string {
        $ytId=$this->extractYoutubeId($d['youtube_url']??'');
        $thumb=$ytId?"https://img.youtube.com/vi/{$ytId}/hqdefault.jpg":null;
        return $this->insert(
            'INSERT INTO sermons(title,youtube_url,youtube_id,description,preacher,sermon_date,thumbnail) VALUES(?,?,?,?,?,?,?)',
            [$d['title'],$d['youtube_url'],$ytId,$d['description']??null,$d['preacher']??null,$d['sermon_date']??null,$d['thumbnail']??$thumb]
        );
    }
    public function update(int $id, array $d): int {
        $ytId=$this->extractYoutubeId($d['youtube_url']??'');
        $thumb=$ytId?"https://img.youtube.com/vi/{$ytId}/hqdefault.jpg":null;
        return $this->execute(
            'UPDATE sermons SET title=?,youtube_url=?,youtube_id=?,description=?,preacher=?,sermon_date=?,thumbnail=?,updated_at=NOW() WHERE id=?',
            [$d['title'],$d['youtube_url'],$ytId,$d['description']??null,$d['preacher']??null,$d['sermon_date']??null,$d['thumbnail']??$thumb,$id]
        );
    }
    public function delete(int $id): int { return $this->execute('DELETE FROM sermons WHERE id=?',[$id]); }
    public function urlExists(string $url, int $ex=0): bool {
        return (bool)$this->fetch('SELECT id FROM sermons WHERE youtube_url=? AND id!=?',[$url,$ex]);
    }
    public function extractYoutubeId(string $url): ?string {
        if(preg_match('/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_-]{11})/',$url,$m)) return $m[1];
        return null;
    }
    public function buildPagination(int $total, int $cur, int $perPage=ITEMS_PER_PAGE): array {
        $totalPages=(int)ceil($total/$perPage);
        $half=(int)floor(PAGE_RANGE/2);
        $start=max(1,$cur-$half); $end=min($totalPages,$start+PAGE_RANGE-1);
        if($end-$start+1<PAGE_RANGE) $start=max(1,$end-PAGE_RANGE+1);
        return ['total'=>$total,'per_page'=>$perPage,'current'=>$cur,'total_pages'=>$totalPages,'start_page'=>$start,'end_page'=>$end,'has_prev'=>$cur>1,'has_next'=>$cur<$totalPages];
    }
}
