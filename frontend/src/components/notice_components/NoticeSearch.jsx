import { useState } from "react";

export default function NoticeSearch({ onSearch = () => {} }) {
  const [searchQuery, setSearchQuery] = useState("");

  const handleSearch = (e) => {
    e.preventDefault();
    onSearch(searchQuery);
  };

  const handleInputChange = (e) => {
    const value = e.target.value;
    setSearchQuery(value);
    onSearch(value);
  };

  return (
    <form className="notice-search" onSubmit={handleSearch}>
      <div className="notice-search__field-wrap">
        <input
          type="text"
          className="notice-search__input"
          placeholder="검색어를 입력하세요"
          value={searchQuery}
          onChange={handleInputChange}
          aria-label="공지사항 검색"
        />
        <button
          type="submit"
          className="notice-search__btn"
          aria-label="검색"
        >
          <img src="/images/sub/04-obituary/ic-search.svg" alt="" />
        </button>
      </div>
    </form>
  );
}
