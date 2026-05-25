<?php
class OnlineGivingModel extends BaseModel {

    /* ── Service Times ─────────────────────────────────── */
    public function getServiceTimes(): array {
        return $this->fetchAll('SELECT * FROM service_times ORDER BY category, sort_order ASC, id ASC');
    }
    public function findServiceTime(int $id): ?array {
        return $this->fetch('SELECT * FROM service_times WHERE id=?', [$id]);
    }
    public function createServiceTime(array $d): string {
        return $this->insert(
            'INSERT INTO service_times(category,name,day,time,sort_order,is_active) VALUES(?,?,?,?,?,?)',
            [$d['category'], $d['name'], $d['day']??null, $d['time'], $d['sort_order']??0, $d['is_active']??1]
        );
    }
    public function updateServiceTime(int $id, array $d): int {
        return $this->execute(
            'UPDATE service_times SET category=?,name=?,day=?,time=?,sort_order=?,is_active=?,updated_at=NOW() WHERE id=?',
            [$d['category'], $d['name'], $d['day']??null, $d['time'], $d['sort_order']??0, $d['is_active']??1, $id]
        );
    }
    public function deleteServiceTime(int $id): int {
        return $this->execute('DELETE FROM service_times WHERE id=?', [$id]);
    }
    public function reorderServiceTimes(array $orders): void {
        foreach ($orders as $item) {
            $this->execute('UPDATE service_times SET sort_order=? WHERE id=?', [(int)$item['order'], (int)$item['id']]);
        }
    }

    /* ── Shuttle Bus ───────────────────────────────────── */
    public function getShuttleBus(): array {
        return $this->fetchAll("SELECT * FROM shuttle_bus_schedule ORDER BY FIELD(direction,'finch_to_church','church_to_finch'), sort_order ASC");
    }
    public function findShuttle(int $id): ?array {
        return $this->fetch('SELECT * FROM shuttle_bus_schedule WHERE id=?', [$id]);
    }
    public function createShuttle(array $d): string {
        return $this->insert(
            'INSERT INTO shuttle_bus_schedule(direction,time,service_label,sort_order,is_active) VALUES(?,?,?,?,?)',
            [$d['direction'], $d['time'], $d['service_label'], $d['sort_order']??0, $d['is_active']??1]
        );
    }
    public function updateShuttle(int $id, array $d): int {
        return $this->execute(
            'UPDATE shuttle_bus_schedule SET direction=?,time=?,service_label=?,sort_order=?,is_active=?,updated_at=NOW() WHERE id=?',
            [$d['direction'], $d['time'], $d['service_label'], $d['sort_order']??0, $d['is_active']??1, $id]
        );
    }
    public function deleteShuttle(int $id): int {
        return $this->execute('DELETE FROM shuttle_bus_schedule WHERE id=?', [$id]);
    }

    /* ── Parking Lot ───────────────────────────────────── */
    public function getParkingItems(): array {
        return $this->fetchAll('SELECT * FROM parking_lot ORDER BY sort_order ASC, id ASC');
    }
    public function findParkingItem(int $id): ?array {
        return $this->fetch('SELECT * FROM parking_lot WHERE id=?', [$id]);
    }
    public function createParkingItem(array $d): string {
        return $this->insert(
            'INSERT INTO parking_lot(content,sort_order,is_active) VALUES(?,?,?)',
            [$d['content'], $d['sort_order']??0, $d['is_active']??1]
        );
    }
    public function updateParkingItem(int $id, array $d): int {
        return $this->execute(
            'UPDATE parking_lot SET content=?,sort_order=?,is_active=?,updated_at=NOW() WHERE id=?',
            [$d['content'], $d['sort_order']??0, $d['is_active']??1, $id]
        );
    }
    public function deleteParkingItem(int $id): int {
        return $this->execute('DELETE FROM parking_lot WHERE id=?', [$id]);
    }
    public function reorderParkingItems(array $orders): void {
        foreach ($orders as $item) {
            $this->execute('UPDATE parking_lot SET sort_order=? WHERE id=?', [(int)$item['order'], (int)$item['id']]);
        }
    }

    /* ── Parking Map ───────────────────────────────────── */
    public function getParkingMap(): ?array {
        return $this->fetch('SELECT * FROM parking_map ORDER BY id ASC LIMIT 1');
    }
    public function upsertParkingMap(array $d): void {
        $existing = $this->getParkingMap();
        if ($existing) {
            $f = ['alt_text=?','is_active=?'];
            $p = [$d['alt_text']??null, $d['is_active']??1];
            if (isset($d['image_url'])) { $f[] = 'image_url=?'; $p[] = $d['image_url']; }
            $p[] = $existing['id'];
            $this->execute('UPDATE parking_map SET ' . implode(',', $f) . ',updated_at=NOW() WHERE id=?', $p);
        } else {
            $this->insert('INSERT INTO parking_map(image_url,alt_text,is_active) VALUES(?,?,?)',
                [$d['image_url']??'', $d['alt_text']??null, $d['is_active']??1]);
        }
    }

    /* ── Banner Image ──────────────────────────────────── */
    public function getBanner(): ?array {
        return $this->fetch('SELECT * FROM banner_image ORDER BY id ASC LIMIT 1');
    }
    public function upsertBanner(array $d): void {
        $existing = $this->getBanner();
        if ($existing) {
            $f = ['alt_text=?','is_active=?'];
            $p = [$d['alt_text']??null, $d['is_active']??1];
            if (isset($d['image_url'])) { $f[] = 'image_url=?'; $p[] = $d['image_url']; }
            $p[] = $existing['id'];
            $this->execute('UPDATE banner_image SET ' . implode(',', $f) . ',updated_at=NOW() WHERE id=?', $p);
        } else {
            $this->insert('INSERT INTO banner_image(image_url,alt_text,is_active) VALUES(?,?,?)',
                [$d['image_url']??'', $d['alt_text']??null, $d['is_active']??1]);
        }
    }
}
