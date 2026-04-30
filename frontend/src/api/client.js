const API_BASE_URL = "http://localhost/";

function toQueryString(params = {}) {
  const search = new URLSearchParams();

  Object.entries(params).forEach(([key, value]) => {
    if (value !== undefined && value !== null && value !== "") {
      search.append(key, String(value));
    }
  });

  const query = search.toString();
  return query ? `?${query}` : "";
}

function withAuth(token) {
  if (!token) {
    return {};
  }
  return { Authorization: `Bearer ${token}` };
}

async function request(path, options = {}) {
  const res = await fetch(`${API_BASE_URL}${path}`, {
    headers: {
      "Content-Type": "application/json",
      ...(options.headers || {}),
    },
    ...options,
  });

  const contentType = res.headers.get("content-type") || "";
  const data = contentType.includes("application/json") ? await res.json() : null;

  if (!res.ok) {
    const message = data?.error?.message || `Request failed with status ${res.status}`;
    throw new Error(message);
  }

  return data;
}

export const api = {
  baseUrl: API_BASE_URL,

  // Base / health
  getHealth: () => request("/api"),

  // Auth (GET)
  getAuthMe: (token) =>
    request("/api/auth/me", {
      headers: withAuth(token),
    }),

  // Hero
  getHero: () => request("/api/hero"),

  // Sermons
  getSermons: (params = {}) => request(`/api/sermons${toQueryString(params)}`),
  getSermonById: (id) => request(`/api/sermons/${id}`),

  // Bulletins
  getBulletins: (params = {}) => request(`/api/bulletins${toQueryString(params)}`),
  getBulletinById: (id) => request(`/api/bulletins/${id}`),

  // Announcements
  getAnnouncements: (params = {}) =>
    request(`/api/announcements${toQueryString(params)}`),
  getAnnouncementById: (id) => request(`/api/announcements/${id}`),

  // Together
  getTogether: () => request("/api/together"),
  getTogetherById: (id) => request(`/api/together/${id}`),

  // Departments
  getDepartments: () => request("/api/departments"),
  getDepartmentById: (id) => request(`/api/departments/${id}`),
  getNextgen: () => request("/api/nextgen"),
  getNextgenById: (id) => request(`/api/nextgen/${id}`),
  getMinistry: () => request("/api/ministry"),
  getMinistryById: (id) => request(`/api/ministry/${id}`),

  // News
  getNews: (params = {}) => request(`/api/news${toQueryString(params)}`),
  getNewsById: (id) => request(`/api/news/${id}`),

  // Members
  getMembers: () => request("/api/members"),
  getMemberById: (id) => request(`/api/members/${id}`),
  // Router implementation expects /api/members/{role}/role
  getMembersByRole: (role) => request(`/api/members/${role}/role`),

  // Users
  getUsers: (params = {}) => request(`/api/users${toQueryString(params)}`),
  getUserById: (id) => request(`/api/users/${id}`),

  // Analytics
  getAnalyticsPages: (params = {}) => request(`/api/analytics/pages${toQueryString(params)}`),
  getAnalyticsDevices: () => request("/api/analytics/devices"),
  getAnalyticsBrowsers: () => request("/api/analytics/browsers"),
  getAnalyticsRecent: (params = {}) =>
    request(`/api/analytics/recent${toQueryString(params)}`),

  // Hero links
  getHeroLinks: () => request("/api/hero-links"),
  getHeroLinkById: (id) => request(`/api/hero-links/${id}`),

  // Landing titles
  getLandingTitles: () => request("/api/landing-titles"),
  getLandingTitleById: (id) => request(`/api/landing-titles/${id}`),

  // Existing write endpoint used in UI
  login: (username, password) =>
    request("/api/auth/login", {
      method: "POST",
      body: JSON.stringify({ username, password }),
    }),
};
