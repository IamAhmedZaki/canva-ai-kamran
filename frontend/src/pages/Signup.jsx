import { useState } from "react";
import { apiFetch } from "../api";

export default function Signup() {
  const [form, setForm] = useState({ name: "", email: "", password: "" });

  const submit = async (e) => {
    e.preventDefault();
    await apiFetch("/signup", {
      method: "POST",
      body: JSON.stringify(form),
    });
    alert("Signup successful!");
  };

  return (
    <div className="flex items-center justify-center h-screen bg-gray-100">
      <form onSubmit={submit} className="bg-white p-8 rounded-xl shadow-lg w-80 space-y-4">
        <h2 className="text-2xl font-semibold text-center">Sign Up</h2>

        <input type="text" placeholder="Name"
          className="w-full px-3 py-2 border rounded"
          onChange={(e) => setForm({ ...form, name: e.target.value })} />

        <input type="email" placeholder="Email"
          className="w-full px-3 py-2 border rounded"
          onChange={(e) => setForm({ ...form, email: e.target.value })} />

        <input type="password" placeholder="Password"
          className="w-full px-3 py-2 border rounded"
          onChange={(e) => setForm({ ...form, password: e.target.value })} />

        <button className="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
          Create Account
        </button>
      </form>
    </div>
  );
}
