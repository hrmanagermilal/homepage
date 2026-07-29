export default function AlbumPagination({ currentPage = 1, totalPages = 4, onPageChange }) {
  const pages = Array.from({ length: totalPages }, (_, i) => i + 1);

  return (
    <nav className="album-pagination" aria-label="페이지 네비게이션">
      <div className="album-pagination__nums">
        {pages.map((page) => (
          <a
            key={page}
            className={`album-pagination__btn${page === currentPage ? " is-active" : ""}`}
            href="#"
            onClick={(e) => {
              e.preventDefault();
              if (onPageChange) {
                onPageChange(page);
              }
            }}
            {...(page === currentPage ? { "aria-current": "page" } : {})}
          >
            {page}
          </a>
        ))}
      </div>
      <a
        className="album-pagination__next"
        href="#"
        aria-label="다음 페이지"
        onClick={(e) => {
          e.preventDefault();
          if (currentPage < totalPages && onPageChange) {
            onPageChange(currentPage + 1);
          }
        }}
      >
        <svg width="5" height="10" viewBox="0 0 5 10" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path d="M1 1L4.5 5L1 9" stroke="#252820" strokeWidth="1.3" strokeLinecap="round" strokeLinejoin="round" />
        </svg>
      </a>
    </nav>
  );
}
