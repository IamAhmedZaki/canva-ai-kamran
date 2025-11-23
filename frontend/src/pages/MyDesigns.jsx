import { useEffect, useState } from "react";
import { apiFetch } from "../api";

export default function MyDesigns() {
  const [designs, setDesigns] = useState([]);

  useEffect(() => {
    apiFetch("/designs").then((data) => setDesigns(data));
  }, []);

  return (
    <div>
      <h2 className="text-2xl font-semibold mb-4">My Designs</h2>

      <ul className="space-y-2">
        {designs.map((d) => (
          <li key={d.id} className="p-3 bg-white rounded shadow">
            {d.name}
          </li>
        ))}
      </ul>
    </div>
  );
}
