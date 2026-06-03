import InterpreterModeIcon from '@mui/icons-material/InterpreterMode';

export default function FloatingMenu() {
  return (
    <aside className="quick-menu" aria-label="빠른 메뉴">
      <ul>
        <li>
          <a className="quick-menu__btn quick-menu__btn--live" href="https://youtube.com/@milalchurch?si=xbmgxeIMCL6XbyAv" target="_blank" rel="noopener noreferrer" aria-label="온라인 예배 바로가기">
            <i><img src="/images/common/icon-live-stream.svg" alt="" /></i>
            <span>예배 영상</span>
          </a>
        </li>  
        <li>
          <a className="quick-menu__btn quick-menu__btn--translate" href="https://captionkit.io/c/milal-etvynx/l/en-US" target="_blank" rel="noopener noreferrer" aria-label="실시간 예배 동시통역">
            <i>
              <InterpreterModeIcon sx={{ display: 'block', fontSize: 'inherit' }} />
            </i>
            <span>실시간예배 통역</span>
          </a>
        </li>           
        <li>
          <a className="quick-menu__btn quick-menu__btn--book" href="https://milalbookcafe.com/" target="_blank" rel="noopener noreferrer" aria-label="밀알 도서관 바로가기">
            <i>
              <svg width="21" height="29" viewBox="0 0 21 29" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style={{display:'block',flexShrink:0,pointerEvents:'none'}}>
                <path d="M6.94637 0.265747C5.10465 -0.703651 1.47489 1.16946 0.43217 2.81847C-0.0323616 3.55638 0.000505058 4.08814 0.000505058 4.38984V20.5301L13.6123 29L16.1719 27.6024V11.8837L2.19626 3.85955C2.94623 2.91567 4.63292 1.76471 5.89763 2.22697L18.346 8.88484L18.346 26.3955L20.9121 24.9954V7.48508L6.94637 0.265747Z" fill="currentColor"/>
              </svg>
            </i>
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
