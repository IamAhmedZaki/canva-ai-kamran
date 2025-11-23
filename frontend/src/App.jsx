import { BrowserRouter, Routes, Route } from "react-router-dom";
import Signup from "./pages/Signup";
import Login from "./pages/Login";
import Dashboard from "./pages/Dashboard";
import CreateDesign from "./pages/CreateDesign";
import MyDesigns from "./pages/MyDesigns";

export default function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<Login />} />
        <Route path="/signup" element={<Signup />} />
        <Route path="/dashboard" element={<Dashboard />}>
          <Route path="create-design" element={<CreateDesign />} />
          <Route path="my-designs" element={<MyDesigns />} />
        </Route>
      </Routes>
    </BrowserRouter>
  );
}
