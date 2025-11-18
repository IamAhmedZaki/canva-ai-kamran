// src/api/fetchApi.js
const API_BASE = 'http://127.0.0.1:8000';

export const apiFetch = async (endpoint, options = {}) => {
  const url = `${API_BASE}${endpoint}`;
  const config = {
    credentials: 'include', // This sends laravel_session cookie
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      ...options.headers,
    },
    ...options,
  };

  const response = await fetch(url, config);

  if (!response.ok) {
    const error = await response.json().catch(() => ({ error: 'Network error' }));
    throw new Error(error.error || `HTTP ${response.status}`);
  }

  // Only parse JSON if response has body
  const text = await response.text();
  return text ? JSON.parse(text) : {};
};