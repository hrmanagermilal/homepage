export default function ObituarySearchForm({ onSearch, searchQuery, onSearchChange }) {
  const handleSubmit = (e) => {
    e.preventDefault();
    if (onSearch) {
      onSearch(searchQuery);
    }
  };

  return (
    <form className="obituary-search" role="search" onSubmit={handleSubmit}>
      <div className="obituary-search__field-wrap">
        <input
          className="obituary-search__input"
          type="search"
          name="q"
          placeholder="검색어를 입력해주세요"
          aria-label="검색어 입력"
          value={searchQuery}
          onChange={(e) => onSearchChange(e.target.value)}
        />
        <button className="obituary-search__btn" type="submit" aria-label="검색">
          <img src="/images/sub/04-obituary/ic-search.svg" alt="" />
        </button>
      </div>
    </form>
  );
}
