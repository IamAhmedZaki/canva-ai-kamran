import Sidebar from "../Components/Sidebar";
import { Outlet } from "react-router-dom";

export default function Dashboard() {
  return (
    <div className="flex">
      <Sidebar />
      <div className="flex-1 p-6 bg-gray-100 min-h-screen">
        <Outlet />
      </div>
    </div>
  );
}
