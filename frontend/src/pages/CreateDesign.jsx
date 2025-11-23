import { useState } from "react";
import { apiFetch } from "../api";
import ConnectCanvaButton from "../Components/ConnectCanvaButton";

export default function CreateDesign() {
  const [name, setName] = useState("");

  const submit = async () => {
    await apiFetch("/designs", {
      method: "POST",
      body: JSON.stringify({ name }),
    });
    alert("Design created!");
  };

  return (
    <div>
      {
        <ConnectCanvaButton/>
      }
    </div>
  );
}
