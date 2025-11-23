import { useState } from "react";
import { apiFetch } from "../api";
import { useNavigate } from "react-router-dom";

export default function Login() {
  const nav = useNavigate();
  const [form, setForm] = useState({ email: "", password: "" });

  const submit = async (e) => {
    e.preventDefault();

    const data = await apiFetch("/login", {
      method: "POST",
      body: JSON.stringify(form),
    });

    if (data.token) {
      localStorage.setItem("token", data.token);
      nav("/dashboard");
    } else {
      alert("Invalid credentials");
    }
  };

  return (
    <div className="flex items-center justify-center h-screen bg-gray-100">
      <form onSubmit={submit} className="bg-white p-8 rounded-xl shadow-lg w-80 space-y-4">
        <h2 className="text-2xl font-semibold text-center">Login</h2>

        <input type="email" placeholder="Email"
          className="w-full px-3 py-2 border rounded"
          onChange={(e) => setForm({ ...form, email: e.target.value })} />

        <input type="password" placeholder="Password"
          className="w-full px-3 py-2 border rounded"
          onChange={(e) => setForm({ ...form, password: e.target.value })} />

        <button className="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
          Login
        </button>
      </form>
    </div>
  );
}
