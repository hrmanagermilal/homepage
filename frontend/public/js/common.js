(function () {
  var fallbackTemplates = {
  "head.html": [
    "<header class=\"site-header\" role=\"banner\">",
    "  <h1 id=\"site-title\" class=\"sound-only\">밀알교회</h1>",
    "  <div class=\"site-header__inner\">",
    "",
    "    <!-- 로고 -->",
    "    <a class=\"site-header__logo\" href=\"{{root}}main.html\">",
    "      <img src=\"{{root}}images/common/logo.png\" alt=\"밀알교회\" />",
    "    </a>",
    "",
    "    <!-- 볼륨 -->",
    "    <button class=\"site-header__volume\" type=\"button\" aria-label=\"볼륨\">",
    "      <img src=\"{{root}}images/common/icon-volume.svg\" alt=\"\" />",
    "    </button>",
    "",
    "    <!-- GNB -->",
    "    <nav class=\"site-header__gnb\" aria-label=\"주 메뉴\">",
    "      <ul class=\"site-header__gnb-list\">",
    "        <li class=\"site-header__gnb-item-wrap\">",
    "          <a class=\"site-header__gnb-item\" href=\"{{subRoot}}01-introduction.html#introduction01\">Introduction</a>",
    "          <ul class=\"site-header__gnb-sub\">",
    "            <li><a class=\"site-header__gnb-sub-item\" href=\"{{subRoot}}01-introduction.html#introduction01\">교회비전</a></li>",
    "            <li><a class=\"site-header__gnb-sub-item\" href=\"{{subRoot}}01-introduction.html#introduction02\">섬기는 분들</a></li>",
    "            <li><a class=\"site-header__gnb-sub-item\" href=\"{{subRoot}}01-introduction.html#introduction03\">함께하는 교회</a></li>",
    "          </ul>",
    "        </li>",
    "        <li class=\"site-header__gnb-item-wrap\">",
    "          <a class=\"site-header__gnb-item\" href=\"{{subRoot}}02-next-generation.html\">다음세대</a>",
    "          <ul class=\"site-header__gnb-sub\">",
    "            <li><a class=\"site-header__gnb-sub-item\" href=\"{{subRoot}}02-next-generation.html\">청년부</a></li>",
    "            <li><a class=\"site-header__gnb-sub-item\" href=\"#\">KM 청년부</a></li>",
    "            <li><a class=\"site-header__gnb-sub-item\" href=\"#\">EM 청년부</a></li>",
    "            <li><a class=\"site-header__gnb-sub-item\" href=\"#\">아동부</a></li>",
    "            <li><a class=\"site-header__gnb-sub-item\" href=\"#\">유치부</a></li>",
    "            <li><a class=\"site-header__gnb-sub-item\" href=\"#\">유아부</a></li>",
    "            <li><a class=\"site-header__gnb-sub-item\" href=\"#\">영아부</a></li>",
    "          </ul>",
    "        </li>",
    "        <li class=\"site-header__gnb-item-wrap\">",
    "          <a class=\"site-header__gnb-item\" href=\"{{subRoot}}03-ministry.html\">사역</a>",
    "          <ul class=\"site-header__gnb-sub\">",
    "            <li><a class=\"site-header__gnb-sub-item\" href=\"#\">양육</a></li>",
    "            <li><a class=\"site-header__gnb-sub-item\" href=\"#\">소그룹</a></li>",
    "            <li><a class=\"site-header__gnb-sub-item\" href=\"#\">가정</a></li>",
    "            <li><a class=\"site-header__gnb-sub-item\" href=\"#\">선교</a></li>",
    "            <li><a class=\"site-header__gnb-sub-item\" href=\"#\">장학</a></li>",
    "            <li><a class=\"site-header__gnb-sub-item\" href=\"#\">가스펠프로젝트</a></li>",
    "            <li><a class=\"site-header__gnb-sub-item\" href=\"#\">다니엘한글문화학교</a></li>",
    "            <li><a class=\"site-header__gnb-sub-item\" href=\"#\">러브토론토</a></li>",
    "          </ul>",
    "        </li>",
    "        <li class=\"site-header__gnb-item-wrap\">",
    "          <a class=\"site-header__gnb-item\" href=\"{{subRoot}}04-notice.html\">소식</a>",
    "          <ul class=\"site-header__gnb-sub\">",
    "            <li><a class=\"site-header__gnb-sub-item\" href=\"#\">공지</a></li>",
    "            <li><a class=\"site-header__gnb-sub-item\" href=\"{{subRoot}}04-obituary.html\">부고알림</a></li>",
    "          </ul>",
    "        </li>",
    "        <li class=\"site-header__gnb-item-wrap\">",
    "          <a class=\"site-header__gnb-item\" href=\"{{subRoot}}05-online-donation.html\">온라인 헌금</a>",
    "        </li>",
    "      </ul>",
    "    </nav>",
    "",
    "    <!-- 우측 유틸 -->",
    "    <div class=\"site-header__util\">",
    "      <a class=\"site-header__news-btn\" href=\"#\">",
    "        <img src=\"{{root}}images/common/icon-header-news.svg\" alt=\"\" />",
    "        <span>밀알 소식 바로가기</span>",
    "      </a>",
    "      <button class=\"site-header__hamburger\" type=\"button\" aria-label=\"전체 메뉴 열기\">",
    "        <span class=\"site-header__hamburger-line\"></span>",
    "        <span class=\"site-header__hamburger-line\"></span>",
    "      </button>",
    "    </div>",
    "",
    "  </div>",
    "</header>",
    "",
    "<!-- 풀페이지 메뉴 -->",
    "<div class=\"full-menu\" id=\"fullMenu\" role=\"dialog\" aria-modal=\"true\" aria-label=\"전체 메뉴\">",
    "",
    "  <div class=\"full-menu__bg\" aria-hidden=\"true\"></div>",
    "  <div class=\"full-menu__texture\" aria-hidden=\"true\"></div>",
    "  <div class=\"full-menu__right-bg\" aria-hidden=\"true\"></div>",
    "",
    "  <!-- 헤더 (.wrap 으로 감쌈 → 로고 좌, CLOSE 우) -->",
    "  <div class=\"full-menu__header\">",
    "    <div class=\"wrap full-menu__header-inner\">",
    "      <a class=\"full-menu__logo\" href=\"{{root}}main.html\">",
    "        <img src=\"{{root}}images/common/logo.png\" alt=\"밀알교회\" />",
    "      </a>",
    "      <button class=\"full-menu__close-btn\" id=\"fullMenuClose\" type=\"button\" aria-label=\"메뉴 닫기\">",
    "        <i></i>",
    "        CLOSE",
    "      </button>",
    "    </div>",
    "  </div>",
    "",
    "  <!-- 본문 (.wrap 으로 감쌈) -->",
    "  <div class=\"full-menu__body\">",
    "    <div class=\"wrap full-menu__body-inner\">",
    "      <div class=\"full-menu__list\">",
    "        <!-- GNB: 866rem 고정, position:relative (2차 메뉴 기준점) -->",
    "        <nav class=\"full-menu__gnb\" aria-label=\"전체 메뉴 내비게이션\">",
    "          <ul class=\"full-menu__gnb-list\">",
    "            <li class=\"full-menu__gnb-item\">",
    "              <div class=\"full-menu__gnb-label\">",
    "                <span class=\"full-menu__gnb-num\">01</span>",
    "                <a class=\"full-menu__gnb-title\" href=\"#\">Introduction</a>",
    "              </div>",
    "            </li>",
    "            <li class=\"full-menu__gnb-item\">",
    "              <div class=\"full-menu__gnb-label\">",
    "                <span class=\"full-menu__gnb-num\">02</span>",
    "                <a class=\"full-menu__gnb-title\" href=\"#\">다음세대</a>",
    "              </div>",
    "            </li>",
    "            <li class=\"full-menu__gnb-item\">",
    "              <div class=\"full-menu__gnb-label\">",
    "                <span class=\"full-menu__gnb-num\">03</span>",
    "                <a class=\"full-menu__gnb-title\" href=\"#\">사역</a>",
    "              </div>",
    "            </li>",
    "            <li class=\"full-menu__gnb-item\">",
    "              <div class=\"full-menu__gnb-label\">",
    "                <span class=\"full-menu__gnb-num\">04</span>",
    "                <a class=\"full-menu__gnb-title\" href=\"#\">소식</a>",
    "              </div>",
    "            </li>",
    "            <li class=\"full-menu__gnb-item\">",
    "              <div class=\"full-menu__gnb-label\">",
    "                <span class=\"full-menu__gnb-num\">05</span>",
    "                <a class=\"full-menu__gnb-title\" href=\"{{subRoot}}05-online-donation.html\">온라인 헌금</a>",
    "              </div>",
    "            </li>",
    "          </ul>",
    "",
    "          <!-- 2차 메뉴: GNB 내부 absolute, top:23rem, right:50rem -->",
    "          <ul class=\"full-menu__gnb-sub\">",
    "            <li><a class=\"full-menu__gnb-sub-link\" href=\"{{subRoot}}01-introduction.html#introduction01\">교회비전</a></li>",
    "            <li><a class=\"full-menu__gnb-sub-link\" href=\"{{subRoot}}01-introduction.html#introduction02\">섬기는 분들</a></li>",
    "            <li><a class=\"full-menu__gnb-sub-link\" href=\"{{subRoot}}01-introduction.html#introduction03\">함께하는 교회</a></li>",
    "          </ul>",
    "          <ul class=\"full-menu__gnb-sub\">",
    "            <li><a class=\"full-menu__gnb-sub-link\" href=\"{{subRoot}}02-next-generation.html\">청년부</a></li>",
    "            <li><a class=\"full-menu__gnb-sub-link\" href=\"#\">KM 청년부</a></li>",
    "            <li><a class=\"full-menu__gnb-sub-link\" href=\"#\">EM 청년부</a></li>",
    "            <li><a class=\"full-menu__gnb-sub-link\" href=\"#\">아동부</a></li>",
    "            <li><a class=\"full-menu__gnb-sub-link\" href=\"#\">유치부</a></li>",
    "            <li><a class=\"full-menu__gnb-sub-link\" href=\"#\">유아부</a></li>",
    "            <li><a class=\"full-menu__gnb-sub-link\" href=\"#\">영아부</a></li>",
    "          </ul>",
    "          <ul class=\"full-menu__gnb-sub\">",
    "            <li><a class=\"full-menu__gnb-sub-link\" href=\"{{subRoot}}03-ministry.html\">양육</a></li>",
    "            <li><a class=\"full-menu__gnb-sub-link\" href=\"#\">소그룹</a></li>",
    "            <li><a class=\"full-menu__gnb-sub-link\" href=\"#\">가정</a></li>",
    "            <li><a class=\"full-menu__gnb-sub-link\" href=\"#\">선교</a></li>",
    "            <li><a class=\"full-menu__gnb-sub-link\" href=\"#\">장학</a></li>",
    "            <li><a class=\"full-menu__gnb-sub-link\" href=\"#\">가스펠프로젝트</a></li>",
    "            <li><a class=\"full-menu__gnb-sub-link\" href=\"#\">다니엘한글문화학교</a></li>",
    "            <li><a class=\"full-menu__gnb-sub-link\" href=\"https://lovetoronto.org/\" target=\"_blank\">러브토론토</a></li>",
    "          </ul>",
    "          <ul class=\"full-menu__gnb-sub\">",
    "            <li><a class=\"full-menu__gnb-sub-link\" href=\"#\">공지</a></li>",
    "            <li><a class=\"full-menu__gnb-sub-link\" href=\"{{subRoot}}04-obituary.html\">부고알림</a></li>",
    "          </ul>",
    "          <!-- 05 온라인 헌금: 2차 메뉴 없음 -->",
    "        </nav>",
    "        <p class=\"full-menu__copy\">ⓒ MILAL CHURCH .All Right Reserved.</p>",
    "        <!-- 정보 패널: 481rem, margin-left:auto (wrap 우측 끝 정렬) -->",
    "      </div>",
    "      <div class=\"full-menu__info\">",
    "",
    "        <!-- 공지사항 -->",
    "        <div class=\"full-menu__info-section\">",
    "          <h2 class=\"full-menu__info-title\">공지사항</h3>",
    "          <a class=\"full-menu__info-card\" href=\"#\">",
    "            <span class=\"full-menu__info-card-text\">밀알교회 홈페이지가 새롭게 리뉴얼 되었습니다.</span>",
    "            <span class=\"full-menu__info-arrow\"><svg width=\"14\" height=\"10\" viewBox=\"0 0 14 10\" fill=\"none\"><path d=\"M1 5H13M13 5L9 1M13 5L9 9\" stroke=\"white\" stroke-width=\"1.2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/></svg></span>",
    "          </a>",
    "        </div>",
    "",
    "        <!-- 최근 부고 소식 -->",
    "        <div class=\"full-menu__info-section\">",
    "          <h2 class=\"full-menu__info-title\">최근 부고 소식</h3>",
    "          <a class=\"full-menu__info-card\" href=\"#\">",
    "            <span class=\"full-menu__obituary-icon\"><img src=\"{{root}}images/common/icon-obituary-cross.svg\" alt=\"\" /></span>",
    "            <span class=\"full-menu__info-card-text\">박주희 집사(김주환 집사) 모친 소천(영광 2순)</span>",
    "            <span class=\"full-menu__info-arrow\"><svg width=\"14\" height=\"10\" viewBox=\"0 0 14 10\" fill=\"none\"><path d=\"M1 5H13M13 5L9 1M13 5L9 9\" stroke=\"white\" stroke-width=\"1.2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/></svg></span>",
    "          </a>",
    "          <a class=\"full-menu__info-card\" href=\"#\">",
    "            <span class=\"full-menu__obituary-icon\"><img src=\"{{root}}images/common/icon-obituary-cross.svg\" alt=\"\" /></span>",
    "            <span class=\"full-menu__info-card-text\">이효숙 성도 부친 소천 (청장년 1순)</span>",
    "            <span class=\"full-menu__info-arrow\"><svg width=\"14\" height=\"10\" viewBox=\"0 0 14 10\" fill=\"none\"><path d=\"M1 5H13M13 5L9 1M13 5L9 9\" stroke=\"white\" stroke-width=\"1.2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/></svg></span>",
    "          </a>",
    "        </div>",
    "",
    "        <!-- 바로가기 -->",
    "        <div class=\"full-menu__info-section\">",
    "          <h2 class=\"full-menu__info-title\">바로가기</h3>",
    "          <ul class=\"full-menu__shortcuts\">",
    "            <li><a class=\"full-menu__shortcut\" href=\"https://youtube.com/@milalchurch?si=xbmgxeIMCL6XbyAv\" target=\"_blank\">",
    "              <span class=\"full-menu__shortcut-icon\"><img src=\"{{root}}images/common/ic-fullmenu01.svg\" alt=\"\" aria-hidden=\"true\" /></span>",
    "              <span class=\"full-menu__shortcut-label\">실시간 예배 보러가기</span>",
    "            </a></li>",
    "            <li><a class=\"full-menu__shortcut\" href=\"#\" target=\"_blank\">",
    "              <span class=\"full-menu__shortcut-icon\"><img src=\"{{root}}images/common/ic-fullmenu02.svg\" alt=\"\" aria-hidden=\"true\" /></span>",
    "              <span class=\"full-menu__shortcut-label\">다니엘한글문화학교 바로가기</span>",
    "            </a></li>",
    "            <li><a class=\"full-menu__shortcut\" href=\"https://lovetoronto.org/\" target=\"_blank\">",
    "              <span class=\"full-menu__shortcut-icon\"><img src=\"{{root}}images/common/ic-fullmenu03.png\" alt=\"\" aria-hidden=\"true\" /></span>",
    "              <span class=\"full-menu__shortcut-label\">러브 토론토 바로가기</span>",
    "            </a></li>",
    "          </ul>",
    "        </div>",
    "",
    "      </div><!-- /full-menu__info -->",
    "",
    "    </div><!-- /wrap full-menu__body-inner -->",
    "  </div><!-- /full-menu__body -->",
    "",
    "  ",
    "",
    "</div><!-- /full-menu -->",
    ""
  ].join("\n"),
  "footer.html": [
    "<footer id=\"footer\">",
    "  <div class=\"footer__texture\" aria-hidden=\"true\">",
    "    <img src=\"{{root}}images/common/footer-texture.jpg\" alt=\"\" />",
    "  </div>",
    "",
    "  <button class=\"btn-top\" type=\"button\" aria-label=\"맨 위로 이동\" data-include-if=\"top\">",
    "    <img src=\"{{root}}images/common/icon-scroll-top.svg\" alt=\"\" />",
    "  </button>",
    "",
    "  <div class=\"wrap\">",
    "",
    "    <div class=\"footer__top\">",
    "",
    "      <div class=\"footer__brand\">",
    "        <img class=\"footer__logo\" src=\"{{root}}images/common/footer-logo.png\" alt=\"밀알교회\" />",
    "        <p class=\"footer__copy\">ⓒ MILAL CHURCH .All Right Reserved.</p>",
    "      </div>",
    "",
    "      <nav class=\"footer__nav\" aria-label=\"사이트 메뉴\"></nav>",
    "",
    "    </div>",
    "",
    "    <div class=\"footer__divider\" role=\"separator\"></div>",
    "",
    "    <div class=\"footer__bottom\">",
    "",
    "      <address class=\"footer__info\">",
    "        <dl class=\"footer__info-item\">",
    "          <dt class=\"footer__info-label\">ADDRESS</dt>",
    "          <dd class=\"footer__info-value\">405 Gordon Baker Rd. Toronto Ontario Canada M2H 2S6</dd>",
    "        </dl>",
    "        <dl class=\"footer__info-item\">",
    "          <dt class=\"footer__info-label\">TEL</dt>",
    "          <dd class=\"footer__info-value\"><a href=\"tel:+14162264190\">416-226-4190</a></dd>",
    "        </dl>",
    "        <dl class=\"footer__info-item\">",
    "          <dt class=\"footer__info-label\">FAX</dt>",
    "          <dd class=\"footer__info-value\">416-226-5308</dd>",
    "        </dl>",
    "        <dl class=\"footer__info-item\">",
    "          <dt class=\"footer__info-label\">E-MAIL</dt>",
    "          <dd class=\"footer__info-value\"><a href=\"mailto:milalchurch405@gmail.com\">milalchurch405@gmail.com</a></dd>",
    "        </dl>",
    "      </address>",
    "",
    "      <ul class=\"footer__policy\">",
    "        <li><a class=\"footer-policy-btn\" href=\"{{root}}privacy/01-privacy01.html\">개인정보처리방침</a></li>",
    "        <li><a class=\"footer-policy-btn\" href=\"{{root}}privacy/01-privacy02.html\">이메일무단수집거부</a></li>",
    "      </ul>",
    "",
    "    </div>",
    "",
    "  </div>",
    "</footer>",
    "",
    "<aside class=\"quick-menu\" aria-label=\"빠른 메뉴\">",
    "  <ul>",
    "    <li>",
    "      <a class=\"quick-menu__btn\" href=\"https://milalbookcafe.com/\" target=\"_black\" aria-label=\"밀알 도서관 바로가기\">",
    "        <i><img src=\"{{root}}images/common/ic-quick01.svg\" alt=\"\" /></i>",
    "        <span>밀알 도서관</span>",
    "      </a>",
    "    </li>",
    "    <li>",
    "      <a class=\"quick-menu__btn\" href=\"https://lovetoronto.org/\" target=\"_blank\" aria-label=\"러브 토론토 바로가기\">",
    "        <i><img src=\"{{root}}images/common/icon-lovetoronto.png\" alt=\"\" /></i>",
    "        <span>러브 토론토</span>",
    "      </a>",
    "    </li>",
    "    <li>",
    "      <a class=\"quick-menu__btn\" href=\"#\" target=\"_blank\" aria-label=\"다니엘한글문화학교 바로가기\">",
    "        <i><img src=\"{{root}}images/common/ic-quick03.png\" alt=\"\" /></i>",
    "        <span>다니엘한글문화학교</span>",
    "      </a>",
    "    </li>",
    "    <li>",
    "      <a class=\"quick-menu__btn quick-menu__btn--dark\" href=\"https://youtube.com/@milalchurch?si=xbmgxeIMCL6XbyAv\" target=\"_blank\" aria-label=\"온라인 예배 바로가기\">",
    "        <i><img src=\"{{root}}images/common/icon-live-stream.svg\" alt=\"\" /></i>",
    "        <span>실시간 예배보기</span>",
    "      </a>",
    "    </li>",
    "  </ul>",
    "</aside>",
    ""
  ].join("\n"),
  "lnb.html": [
    "<div class=\"lnb-wrap\" data-lnb=\"{{menu}}\" data-active=\"{{active}}\">",
    "  <nav class=\"lnb\" aria-label=\"{{menu}} 메뉴\"></nav>",
    "</div>",
    ""
  ].join("\n"),
  "sub-visual.html": [
    "<section class=\"sub-visual\" aria-label=\"{{label}} 서브 비주얼\">",
    "",
    "    <div class=\"sub-visual__bg\" aria-hidden=\"true\">",
    "      <figure class=\"sub-visual__bg-img\"></figure>",
    "    </div>",
    "",
    "    <div class=\"sub-visual__ellipse\" aria-hidden=\"true\">",
    "      <img src=\"{{root}}images/main/main-visual-ellipse.svg\" alt=\"\" />",
    "    </div>",
    "",
    "    <div class=\"sub-visual__cont\">",
    "      <nav class=\"sub-visual__lnb\" aria-label=\"현재 위치\">",
    "        <a class=\"sub-visual__lnb-home\" href=\"{{root}}main.html\" aria-label=\"홈\">",
    "          <img src=\"{{root}}images/common/ic-nav-w.svg\" alt=\"\" />",
    "        </a>",
    "        <span class=\"sub-visual__lnb-sep\" aria-hidden=\"true\">",
    "          <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"10\" height=\"10\" viewBox=\"0 0 10 10\" fill=\"none\">",
    "            <path d=\"M3.46447 1.46447L7 5L3.46447 8.53553\" stroke=\"white\" stroke-opacity=\"0.2\" stroke-width=\"2\" stroke-linecap=\"round\"/>",
    "          </svg>",
    "        </span>",
    "        <span class=\"sub-visual__lnb-text\">{{title}}</span>",
    "      </nav>",
    "      <h1 class=\"sub-visual__title\">{{title}}</h1>",
    "    </div>",
    "",
    "    <div class=\"sub-visual__scroll-down\" aria-hidden=\"true\">",
    "       <i></i>",
    "      <span>SCROLL DOWN</span>",
    "    </div>",
    "",
    "  </section>",
    ""
  ].join("\n")
};

  function getIncludeName(src) {
    return (src || '').split('?')[0].split('/').pop();
  }

  function applyTemplate(html, data) {
    return html.replace(/{{\s*([\w-]+)\s*}}/g, function (_, key) {
      return data[key] || '';
    });
  }

  function removeOptional(container, data) {
    container.querySelectorAll('[data-include-if]').forEach(function (el) {
      var key = el.getAttribute('data-include-if');
      if (!data[key]) el.remove();
      else el.removeAttribute('data-include-if');
    });
  }

  function prepareIncludeData(data) {
    data.subRoot = data.root ? './' : 'sub/';
    return data;
  }

  function getIncludeHtml(src) {
    var requestSrc = src;
    if (window.location.protocol !== 'file:') {
      requestSrc += (src.indexOf('?') === -1 ? '?' : '&') + 'v=' + Date.now();
    }

    return fetch(requestSrc, {cache: 'no-store'})
      .then(function (res) {
        if (!res.ok) throw new Error('include failed');
        return res.text();
      })
      .catch(function () {
        return fallbackTemplates[getIncludeName(src)] || '';
      });
  }

  function loadIncludes() {
    var targets = Array.prototype.slice.call(document.querySelectorAll('[data-include]'));
    return Promise.all(targets.map(function (target) {
      var data = prepareIncludeData(Object.assign({}, target.dataset));
      var src = target.getAttribute('data-include');

      return getIncludeHtml(src).then(function (html) {
        var wrap = document.createElement('div');
        wrap.innerHTML = applyTemplate(html, data);
        removeOptional(wrap, data);
        target.replaceWith.apply(target, Array.prototype.slice.call(wrap.childNodes));
      });
    }));
  }

  function getLnbHref(href) {
    if (!href || href === '#') return '#';
    if (/^(\.\/)?sub\//.test(href)) return './' + href.replace(/^(\.\/)?sub\//, '');
    return href;
  }

  function initLnb() {
    document.querySelectorAll('[data-lnb]').forEach(function (wrap) {
      var menu = wrap.getAttribute('data-lnb');
      var active = wrap.getAttribute('data-active');
      var nav = wrap.querySelector('.lnb');
      if (!menu || !nav || nav.dataset.lnbReady === 'true') return;

      var gnbWraps = Array.prototype.slice.call(document.querySelectorAll('.site-header__gnb-item-wrap'));
      var current = null;
      gnbWraps.some(function (item) {
        var title = item.querySelector('.site-header__gnb-item');
        if (title && title.textContent.trim() === menu) {
          current = item;
          return true;
        }
        return false;
      });
      var subItems = current ? Array.prototype.slice.call(current.querySelectorAll('.site-header__gnb-sub-item')) : [];

      if (!subItems.length) {
        wrap.remove();
        return;
      }

      nav.setAttribute('aria-label', menu + ' 메뉴');
      nav.innerHTML = '';

      subItems.forEach(function (item, index) {
        var text = item.textContent.trim();
        var isActive = active ? text === active : index === 0;
        var btn = document.createElement('a');
        btn.className = 'lnb__btn' + (isActive ? ' is-active' : '') + (index > 1 ? ' lnb__btn--sep' : '');
        btn.setAttribute('href', getLnbHref(item.getAttribute('href')));
        btn.textContent = text;
        nav.appendChild(btn);
      });

      nav.dataset.lnbReady = 'true';
    });
  }

  function initCommonUi() {
    if (/^((?!chrome|android).)*safari/i.test(navigator.userAgent)) {
      document.documentElement.classList.add('safari');
    }

    var fullMenu = document.getElementById('fullMenu');
    if (fullMenu && fullMenu.dataset.commonReady !== 'true') {
      var btnHamburger = document.querySelector('.site-header__hamburger');
      var btnMenuClose = document.getElementById('fullMenuClose');
      var fmItems = Array.prototype.slice.call(fullMenu.querySelectorAll('.full-menu__gnb-item'));
      var fmSubs = Array.prototype.slice.call(fullMenu.querySelectorAll('.full-menu__gnb-sub'));

      function setActiveFmItem(idx) {
        fmItems.forEach(function (el) { el.classList.remove('is-active'); });
        fmSubs.forEach(function (el) { el.classList.remove('is-active'); });
        if (fmItems[idx]) fmItems[idx].classList.add('is-active');
        if (fmItems[idx] && fmSubs[idx]) {
          var midY = fmItems[idx].offsetTop + Math.round(fmItems[idx].offsetHeight / 2);
          fmSubs[idx].style.top = midY + 'px';
          fmSubs[idx].classList.add('is-active');
        }
      }

      function getCurrentMenuIndex() {
        var path = window.location.pathname.split('/').pop();
        var matched = 0;
        fmItems.some(function (item, idx) {
          var href = item.getAttribute('href') || '';
          if (href && href.indexOf(path) !== -1) {
            matched = idx;
            return true;
          }
          return false;
        });
        return matched;
      }

      function openFullMenu() {
        fullMenu.classList.add('is-open');
        document.body.style.overflow = 'hidden';
        if (!window.matchMedia('(max-width: 540px)').matches) {
          setTimeout(function () { setActiveFmItem(getCurrentMenuIndex()); }, 1350);
        }
      }

      function closeFullMenu() {
        fullMenu.classList.remove('is-open');
        document.body.style.overflow = '';
        fmItems.forEach(function (el) { el.classList.remove('is-active'); });
        fmSubs.forEach(function (el) { el.classList.remove('is-active'); });
      }

      if (btnHamburger) btnHamburger.addEventListener('click', openFullMenu);
      if (btnMenuClose) btnMenuClose.addEventListener('click', closeFullMenu);
      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && fullMenu.classList.contains('is-open')) closeFullMenu();
      });
      fmItems.forEach(function (item, idx) {
        item.addEventListener('mouseenter', function () { setActiveFmItem(idx); });
      });

      fullMenu.dataset.commonReady = 'true';
    }

    document.querySelectorAll('.btn-top').forEach(function (btn) {
      if (btn.dataset.commonReady === 'true') return;
      btn.addEventListener('click', function () {
        window.scrollTo({top: 0, behavior: 'smooth'});
      });
      btn.dataset.commonReady = 'true';
    });

    var footerNav = document.querySelector('.footer__nav');
    if (footerNav && footerNav.dataset.commonReady !== 'true') {
      var gnbWraps = Array.prototype.slice.call(document.querySelectorAll('.site-header__gnb-item-wrap'));
      var fnHtml = '<ul>';
      gnbWraps.forEach(function (wrap) {
        var title = wrap.querySelector('.site-header__gnb-item');
        var subItems = Array.prototype.slice.call(wrap.querySelectorAll('.site-header__gnb-sub-item'));
        if (!title) return;
        fnHtml += '<li class="footer-nav__col">';
        fnHtml += '<a class="footer-nav__title" href="' + title.getAttribute('href') + '">' + title.textContent.trim() + '</a>';
        if (subItems.length) {
          fnHtml += '<ul class="footer-nav__links">';
          subItems.forEach(function (sub) {
            fnHtml += '<li><a class="footer-nav__link" href="' + sub.getAttribute('href') + '">' + sub.textContent.trim() + '</a></li>';
          });
          fnHtml += '</ul>';
        }
        fnHtml += '</li>';
      });
      fnHtml += '</ul>';
      footerNav.innerHTML = fnHtml;
      footerNav.dataset.commonReady = 'true';
    }
  }

  function initSiteBgm() {
    var btn = document.querySelector('.site-header__volume');
    if (!btn || btn.dataset.bgmReady === 'true') return;

    var icon = btn.querySelector('img');
    if (!icon) return;

    var defaultIcon = icon.getAttribute('src');
    var root = defaultIcon.split('images/common/')[0];
    var muteIcon = root + 'images/common/icon-volume--mute.svg';
    var audio = new Audio(root + 'milal-bgm.wav');
    var waitingForGesture = false;

    btn.dataset.bgmReady = 'true';
    audio.loop = true;
    audio.preload = 'auto';

    function setPlayingState(isPlaying) {
      icon.setAttribute('src', isPlaying ? defaultIcon : muteIcon);
      btn.setAttribute('aria-label', isPlaying ? '배경음악 일시 정지' : '배경음악 재생');
      btn.setAttribute('aria-pressed', isPlaying ? 'true' : 'false');
    }

    function playBgm() {
      var playPromise = audio.play();
      if (playPromise && typeof playPromise.then === 'function') {
        playPromise.then(function () {
          waitingForGesture = false;
          setPlayingState(true);
        }).catch(function () {
          waitingForGesture = true;
          setPlayingState(false);
        });
      } else {
        setPlayingState(true);
      }
    }

    function pauseBgm() {
      audio.pause();
      setPlayingState(false);
    }

    function playAfterGesture(e) {
      if (e && e.target && e.target.closest && e.target.closest('.site-header__volume')) return;
      if (!waitingForGesture || !audio.paused) return;
      playBgm();
    }

    btn.addEventListener('click', function () {
      if (audio.paused) playBgm();
      else pauseBgm();
    });

    document.addEventListener('pointerdown', playAfterGesture);
    document.addEventListener('keydown', playAfterGesture);

    setPlayingState(false);
    playBgm();
  }

  window.MilalIncludes = window.MilalIncludes || {};
  window.MilalIncludes.ready = new Promise(function (resolve) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', function () { loadIncludes().then(function () { initCommonUi(); initLnb(); initSiteBgm(); resolve(); }); });
    } else {
      loadIncludes().then(function () { initCommonUi(); initLnb(); initSiteBgm(); resolve(); });
    }
  });
})();


