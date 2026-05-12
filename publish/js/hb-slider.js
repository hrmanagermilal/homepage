/* ═══════════════════════════════════════════════════════════════
 * hb-slider.js  v1.2
 * 박현비 커스텀 슬라이더 플러그인 — 외부 라이브러리 없음
 *
 * ── 타입: slide (기본) ────────────────────────────────────────
 *   new HBSlider('#mySlider', { type: 'slide', delay: 4000 });
 *
 *   <div id="mySlider">
 *     <div class="hb-slider__track">
 *       <div class="hb-slider__slide">...</div>
 *       <div class="hb-slider__slide">...</div>
 *     </div>
 *     <button class="hb-slider__prev"></button>
 *     <button class="hb-slider__next"></button>
 *     <div class="hb-slider__dots"></div>   ← dots 자동 생성
 *   </div>
 *
 * ── 타입: fade ────────────────────────────────────────────────
 *   new HBSlider('.main-visual', {
 *     type:          'fade',
 *     slideSelector: '.main-visual__slide',
 *     prevSelector:  '.main-visual__nav-btn.is-prev',
 *     nextSelector:  '.main-visual__nav-btn.is-next',
 *     dotsSelector:  '.main-visual__dots',
 *     dotClass:      'main-visual__dot',
 *   });
 *
 * ── 타입: thumb (썸네일 페이지네이션) ────────────────────────
 *   new HBSlider('.main-weekly', {
 *     type:          'thumb',
 *     autoplay:      false,
 *     swipe:         true,
 *     imgSelector:   '#weeklyCardImg',
 *     thumbSelector: '.main-weekly__nav-item',
 *     prevSelector:  '.slider-nav__btn.is-prev',
 *     nextSelector:  '.slider-nav__btn.is-next',
 *   });
 *
 *   <div class="stage">                  ← position:relative; overflow:hidden
 *     <img id="weeklyCardImg" />         ← position:absolute; inset:0; width:100%; height:100%; object-fit:cover
 *   </div>
 *   <div class="nav-item is-active"><img src="..." /></div>
 *   <div class="nav-item"><img src="..." /></div>
 *
 * ── 옵션 ────────────────────────────────────────────────────────
 *   type            'fade'|'slide'|'thumb'  전환 방식             기본: 'slide'
 *   autoplay        true | false            오토플레이             기본: true
 *   delay           number (ms)             전환 간격              기본: 5000
 *   fadeSpeed       number (ms)             페이드 속도 (fade)     기본: 1000
 *   swipe           true | false            드래그/터치 스와이프    기본: true
 *   loop            true | false            루프                   기본: true
 *   swipeThreshold  number (px)             스와이프 인식 거리      기본: 50
 *   slideSelector   string                  slide 셀렉터 오버라이드 기본: '.hb-slider__slide'
 *   trackSelector   string                  track 셀렉터 오버라이드 기본: '.hb-slider__track'
 *   prevSelector    string                  prev  셀렉터 오버라이드 기본: '.hb-slider__prev'
 *   nextSelector    string                  next  셀렉터 오버라이드 기본: '.hb-slider__next'
 *   dotsSelector    string                  dots  셀렉터 오버라이드 기본: '.hb-slider__dots'
 *   dotClass        string                  dot 버튼 클래스         기본: 'hb-slider__dot'
 *   thumbSelector   string                  썸네일 항목 셀렉터 (thumb 전용) 기본: '.hb-slider__thumb-item'
 *   imgSelector     string                  메인 img 셀렉터  (thumb 전용) 기본: '.hb-slider__stage-img'
 *
 * ── 필수 CSS (fade) ─────────────────────────────────────────────
 *   [slide]                   { position: absolute; inset: 0; opacity: 0; transition: opacity 1s ease; }
 *   [slide].is-active         { opacity: 1; }
 *
 * ── 필수 CSS (slide) ────────────────────────────────────────────
 *   [track]                   { display: flex; transition: transform 0.45s cubic-bezier(0.4,0,0.2,1); }
 *   [track].is-dragging       { cursor: grabbing; user-select: none; }
 *   [slide]                   { flex: 0 0 Xrem; }
 *
 * ── 필수 CSS (thumb) ────────────────────────────────────────────
 *   [stage]                   { position: relative; overflow: hidden; }
 *   [img]                     { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; pointer-events: none; }
 *   [thumb-item].is-active    { ... 활성 썸네일 스타일 }
 * ═══════════════════════════════════════════════════════════════ */

(function (root) {
  'use strict';

  /* ── 기본 옵션 ── */
  var DEFAULTS = {
    type:           'slide',
    autoplay:       true,
    delay:          5000,
    fadeSpeed:      1000,
    swipe:          true,
    loop:           true,
    swipeThreshold: 50,
    trackSelector:  '.hb-slider__track',
    slideSelector:  '.hb-slider__slide',
    prevSelector:   '.hb-slider__prev',
    nextSelector:   '.hb-slider__next',
    dotsSelector:   '.hb-slider__dots',
    dotClass:       'hb-slider__dot',
    thumbSelector:  '.hb-slider__thumb-item',
    imgSelector:    '.hb-slider__stage-img',
  };

  /* ════════════════════════════════════════
   * HBSlider
   * ════════════════════════════════════════ */
  function HBSlider(selector, options) {
    var el = typeof selector === 'string'
      ? document.querySelector(selector)
      : selector;

    if (!el) {
      console.warn('[HBSlider] 요소를 찾을 수 없습니다 →', selector);
      return;
    }

    this.el   = el;
    this.opts = _merge(DEFAULTS, options || {});

    this.track    = el.querySelector(this.opts.trackSelector);
    this.slides   = el.querySelectorAll(this.opts.slideSelector);
    this.dotsWrap = el.querySelector(this.opts.dotsSelector);
    this.btnPrev  = el.querySelector(this.opts.prevSelector);
    this.btnNext  = el.querySelector(this.opts.nextSelector);

    this.current    = 0;
    this.total      = this.slides.length;
    this.timer      = null;
    this.dots       = [];
    this._dragging  = false;
    this._scrollX   = 0;
    this._animating = false;
    this.thumbItems = [];
    this.thumbImg   = null;

    /* ── 타입별 필수 요소 검사 ── */
    if (this.opts.type === 'thumb') {
      var ti = el.querySelectorAll(this.opts.thumbSelector);
      if (!ti.length) {
        console.warn('[HBSlider] thumb 타입: thumbnail 요소를 찾을 수 없습니다 →', this.opts.thumbSelector);
        return;
      }
    } else {
      if (!this.slides.length) {
        console.warn('[HBSlider] slide 요소를 찾을 수 없습니다 →', this.opts.slideSelector);
        return;
      }
      if (this.opts.type === 'slide' && !this.track) {
        console.warn('[HBSlider] slide 타입은 track 요소가 필요합니다 →', this.opts.trackSelector);
        return;
      }
    }

    this._init();
  }

  HBSlider.prototype = {

    /* ────────────────────────────────────────
     * 초기화
     * ──────────────────────────────────────── */
    _init: function () {
      var self = this;

      if (self.opts.type === 'thumb') {
        self._initThumb();
      } else {
        /* 첫 슬라이드 활성화 */
        self.slides[0].classList.add('is-active');

        /* fade: transition 속도 동기화 */
        if (self.opts.type === 'fade') {
          [].forEach.call(self.slides, function (s) {
            s.style.transitionDuration = self.opts.fadeSpeed + 'ms';
          });
        }

        /* dots 자동 생성 */
        if (self.dotsWrap) self._buildDots();
      }

      /* cursor */
      if (self.opts.swipe && self.track) self.track.style.cursor = 'grab';

      /* prev / next */
      if (self.btnPrev) {
        self.btnPrev.addEventListener('click', function () { self.prev(); self.startAuto(); });
      }
      if (self.btnNext) {
        self.btnNext.addEventListener('click', function () { self.next(); self.startAuto(); });
      }

      /* 호버 → 오토플레이 일시 정지 */
      self.el.addEventListener('mouseenter', function () { self.stopAuto(); });
      self.el.addEventListener('mouseleave', function () { if (!self._dragging) self.startAuto(); });

      /* 스와이프 */
      if (self.opts.swipe) self._initSwipe();

      /* 오토플레이 시작 */
      self.startAuto();
    },

    /* ────────────────────────────────────────
     * thumb 초기화
     * ──────────────────────────────────────── */
    _initThumb: function () {
      var self = this;
      self.thumbItems = self.el.querySelectorAll(self.opts.thumbSelector);
      self.thumbImg   = self.el.querySelector(self.opts.imgSelector);
      self.total      = self.thumbItems.length;

      if (!self.thumbImg) {
        console.warn('[HBSlider] thumb 타입: img 요소를 찾을 수 없습니다 →', self.opts.imgSelector);
        return;
      }

      /* 첫 번째 썸네일 활성화 */
      self.thumbItems[0].classList.add('is-active');

      /* 썸네일 클릭 */
      [].forEach.call(self.thumbItems, function (item, i) {
        item.addEventListener('click', function () { self.goTo(i); });
      });
    },

    /* ────────────────────────────────────────
     * dots 자동 생성
     * ──────────────────────────────────────── */
    _buildDots: function () {
      var self = this;
      self.dotsWrap.innerHTML = '';
      self.dots = [];

      for (var i = 0; i < self.total; i++) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = self.opts.dotClass;
        btn.setAttribute('aria-label', '슬라이드 ' + (i + 1));
        if (i === 0) btn.classList.add('is-active');

        (function (idx) {
          btn.addEventListener('click', function () { self.goTo(idx); self.startAuto(); });
        })(i);

        self.dotsWrap.appendChild(btn);
        self.dots.push(btn);
      }
    },

    /* ────────────────────────────────────────
     * 슬라이드 이동
     * ──────────────────────────────────────── */
    goTo: function (idx) {
      var self = this;
      var prev = self.current;

      /* thumb: 애니메이션 진행 중 차단 */
      if (self.opts.type === 'thumb' && self._animating) return;

      self.current = self.opts.loop
        ? ((idx % self.total) + self.total) % self.total
        : Math.max(0, Math.min(idx, self.total - 1));

      if (prev === self.current) return;

      /* ── fade ── */
      if (self.opts.type === 'fade') {
        self.slides[prev].classList.remove('is-active');
        self.slides[self.current].classList.add('is-active');
      }

      /* ── slide ── */
      if (self.opts.type === 'slide') {
        var gap  = parseFloat(getComputedStyle(self.track).gap) || 0;
        var step = self.slides[0].offsetWidth + gap;
        var max  = self._calcMax();
        self._scrollX = Math.max(0, Math.min(self.current * step, max));
        self.track.style.transform = 'translateX(-' + self._scrollX + 'px)';
      }

      /* ── thumb ── */
      if (self.opts.type === 'thumb') {
        self._goToThumb(prev);
        return;
      }

      /* dots 갱신 (fade / slide) */
      if (self.dots.length) {
        self.dots[prev].classList.remove('is-active');
        if (self.dots[self.current]) self.dots[self.current].classList.add('is-active');
      }
    },

    /* ────────────────────────────────────────
     * thumb 전환 애니메이션
     * ──────────────────────────────────────── */
    _goToThumb: function (prev) {
      var self = this;
      var n    = self.total;

      /* 루프를 고려한 방향 계산 */
      var forward   = ((self.current - prev) + n) % n;
      var backward  = ((prev - self.current) + n) % n;
      var direction = forward <= backward ? 'next' : 'prev';
      var enterX    = direction === 'next' ? '100%'  : '-100%';
      var leaveX    = direction === 'next' ? '-100%' : '100%';

      self._animating = true;

      var nextImg = document.createElement('img');
      nextImg.src = self.thumbItems[self.current].querySelector('img').src;
      nextImg.style.cssText = [
        'position:absolute;top:0;left:0;',
        'width:100%;height:100%;',
        'object-fit:cover;pointer-events:none;',
        'transform:translateX(' + enterX + ');',
        'will-change:transform;',
      ].join('');
      self.thumbImg.parentElement.appendChild(nextImg);

      requestAnimationFrame(function () {
        requestAnimationFrame(function () {
          var easing = 'transform 0.45s cubic-bezier(0.4,0,0.2,1)';
          self.thumbImg.style.transition = easing;
          nextImg.style.transition       = easing;
          self.thumbImg.style.transform  = 'translateX(' + leaveX + ')';
          nextImg.style.transform        = 'translateX(0)';
        });
      });

      setTimeout(function () {
        self.thumbImg.src              = nextImg.src;
        self.thumbImg.style.transition = '';
        self.thumbImg.style.transform  = '';
        self.thumbImg.parentElement.removeChild(nextImg);

        if (self.thumbItems[prev]) self.thumbItems[prev].classList.remove('is-active');
        if (self.thumbItems[self.current]) self.thumbItems[self.current].classList.add('is-active');
        self._animating = false;
      }, 450);
    },

    prev: function () { this.goTo(this.current - 1); },
    next: function () { this.goTo(this.current + 1); },

    /* ────────────────────────────────────────
     * slide — maxScroll 계산
     * ──────────────────────────────────────── */
    _calcMax: function () {
      var gap   = parseFloat(getComputedStyle(this.track).gap) || 0;
      var cw    = this.slides[0].offsetWidth;
      var total = this.total * cw + (this.total - 1) * gap;
      var left  = this.track.parentElement.getBoundingClientRect().left;
      return Math.max(0, total - (document.documentElement.clientWidth - left));
    },

    /* ────────────────────────────────────────
     * 오토플레이
     * ──────────────────────────────────────── */
    startAuto: function () {
      var self = this;
      if (!self.opts.autoplay) return;
      self.stopAuto();
      self.timer = setInterval(function () { self.next(); }, self.opts.delay);
    },

    stopAuto: function () {
      if (this.timer) { clearInterval(this.timer); this.timer = null; }
    },

    /* ────────────────────────────────────────
     * 드래그 / 터치 스와이프
     * ──────────────────────────────────────── */
    _initSwipe: function () {
      var self       = this;
      var startX     = 0;
      var startSX    = 0;
      var dragging   = false;
      var hasDragged = false;

      function onStart(x) {
        dragging       = true;
        hasDragged     = false;
        startX         = x;
        startSX        = self._scrollX;
        self._dragging = true;
        if (self.track) self.track.style.cursor = 'grabbing';
        if (self.opts.type === 'slide') {
          self.track.style.transition = 'none';
          self.track.classList.add('is-dragging');
        }
        self.stopAuto();
      }

      function onMove(x) {
        if (!dragging || self.opts.type !== 'slide') return;
        var delta = startX - x;
        if (Math.abs(delta) > 5) hasDragged = true;
        var max = self._calcMax();
        self._scrollX = Math.max(0, Math.min(startSX + delta, max));
        self.track.style.transform = 'translateX(-' + self._scrollX + 'px)';
      }

      function onEnd(x) {
        if (!dragging) return;
        dragging       = false;
        self._dragging = false;
        if (self.track) self.track.style.cursor = 'grab';

        var delta = startX - x;

        /* ── slide: 스냅 처리 ── */
        if (self.opts.type === 'slide') {
          self.track.style.transition = '';
          self.track.classList.remove('is-dragging');

          var gap  = parseFloat(getComputedStyle(self.track).gap) || 0;
          var step = self.slides[0].offsetWidth + gap;
          var max  = self._calcMax();

          if (Math.abs(delta) > self.opts.swipeThreshold) {
            hasDragged = true;
            var target = delta > 0
              ? Math.min(self._scrollX + (step - Math.abs(delta) % step), max)
              : Math.max(self._scrollX - (step - Math.abs(delta) % step), 0);
            self._scrollX = target;
            self.track.style.transform = 'translateX(-' + target + 'px)';
            self.current = Math.round(target / step);
          } else {
            var snapped = Math.max(0, Math.min(Math.round(self._scrollX / step) * step, max));
            self._scrollX = snapped;
            self.track.style.transform = 'translateX(-' + snapped + 'px)';
            self.current = Math.round(snapped / step);
          }

          if (self.dots.length) {
            self.dots.forEach(function (d) { d.classList.remove('is-active'); });
            if (self.dots[self.current]) self.dots[self.current].classList.add('is-active');
          }
        }

        /* ── fade / thumb: 방향만 감지 ── */
        if (self.opts.type === 'fade' || self.opts.type === 'thumb') {
          if (Math.abs(delta) > self.opts.swipeThreshold) {
            hasDragged = true;
            delta > 0 ? self.next() : self.prev();
          }
        }

        self.startAuto();
      }

      self.el.addEventListener('mousedown',  function (e) { onStart(e.clientX); });
      document.addEventListener('mousemove', function (e) { onMove(e.clientX); });
      document.addEventListener('mouseup',   function (e) { onEnd(e.clientX); });
      self.el.addEventListener('touchstart', function (e) { onStart(e.touches[0].clientX); },        {passive: true});
      self.el.addEventListener('touchmove',  function (e) { onMove(e.touches[0].clientX); },         {passive: true});
      self.el.addEventListener('touchend',   function (e) { onEnd(e.changedTouches[0].clientX); });
      self.el.addEventListener('click',      function (e) { if (hasDragged) { e.preventDefault(); hasDragged = false; } }, true);
      self.el.addEventListener('dragstart',  function (e) { e.preventDefault(); });

      /* resize → slide 위치 재계산 */
      window.addEventListener('resize', function () {
        if (self.opts.type !== 'slide') return;
        var gap  = parseFloat(getComputedStyle(self.track).gap) || 0;
        var step = self.slides[0].offsetWidth + gap;
        var max  = self._calcMax();
        self._scrollX = Math.min(self.current * step, max);
        self.track.style.transform = 'translateX(-' + self._scrollX + 'px)';
      });
    },

    /* ────────────────────────────────────────
     * 공개 API
     * ──────────────────────────────────────── */
    destroy: function () {
      this.stopAuto();
      if (this.track) this.track.style.cursor = '';
    }
  };

  /* ── 유틸: 옵션 병합 ── */
  function _merge(defaults, options) {
    var result = {};
    for (var k in defaults) if (Object.prototype.hasOwnProperty.call(defaults, k)) result[k] = defaults[k];
    for (var k in options)  if (Object.prototype.hasOwnProperty.call(options,  k)) result[k] = options[k];
    return result;
  }

  root.HBSlider = HBSlider;

})(window);
