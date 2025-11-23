const BASE_URL = "http://127.0.0.1:8000/api";

export async function apiFetch(endpoint, options = {}) {
  const token = localStorage.getItem("token");

  const headers = {
    "Content-Type": "application/json",
    ...(token ? { Authorization: `Bearer ${token}` } : {})
  };

  const res = await fetch(`${BASE_URL}${endpoint}`, {
    headers,
    ...options,
  });

  return res.json();
}
