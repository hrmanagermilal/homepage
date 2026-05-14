export default function FloatingMenu() {
  return (
    <aside className="quick-menu" aria-label="빠른 메뉴">
      <ul>
        <li>
          <a className="quick-menu__btn quick-menu__btn--live" href="https://youtube.com/@milalchurch?si=xbmgxeIMCL6XbyAv" target="_blank" rel="noopener noreferrer" aria-label="온라인 예배 바로가기">
            <i><img src="/images/common/icon-live-stream.svg" alt="" /></i>
            <span>실시간 예배보기</span>
          </a>
        </li>        
        <li>
          <a className="quick-menu__btn quick-menu__btn--book" href="https://milalbookcafe.com/" target="_blank" rel="noopener noreferrer" aria-label="밀알 도서관 바로가기">
            <i><img src="/images/common/ic-quick01.svg" alt="" /></i>
            <span>밀알 도서관</span>
          </a>
        </li>
        <li>
          <a className="quick-menu__btn quick-menu__btn--love" href="https://lovetoronto.org/" target="_blank" rel="noopener noreferrer" aria-label="러브 토론토 바로가기">
            <i><img src="/images/common/icon-lovetoronto.png" alt="" /></i>
            <span>러브 토론토</span>
          </a>
        </li>
        <li>
          <a className="quick-menu__btn quick-menu__btn--school" href="/ministry#ministry06" aria-label="다니엘한글문화학교 바로가기">
            <i><img src="/images/common/ic-quick03.png" alt="" /></i>
            <span>다니엘한글문화학교</span>
          </a>
        </li>
      </ul>
    </aside>
  );
}
