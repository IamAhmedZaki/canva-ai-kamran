import { Link } from "react-router-dom";

export default function Sidebar() {
  return (
    <div className="w-64 h-screen bg-gray-800 text-white p-5 space-y-4">
      <h3 className="text-xl font-semibold">Menu</h3>

      <Link to="/dashboard/create-design" className="block px-3 py-2 rounded hover:bg-gray-700">
        Create Design
      </Link>

      <Link to="/dashboard/my-designs" className="block px-3 py-2 rounded hover:bg-gray-700">
        View My Designs
      </Link>
    </div>
  );
}
