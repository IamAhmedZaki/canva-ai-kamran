import React from "react";

function ConnectCanvaButton() {
  const handleConnect = () => {
    window.location.href = "http://127.0.0.1:8000/canva/authorize";
  };

  return (
    <button
      style={{
        padding: "10px 16px",
        borderRadius: "8px",
        backgroundColor: "#00C4CC",
        color: "white",
        border: "none",
        cursor: "pointer",
      }}
      onClick={handleConnect}
    >
      Connect Canva
    </button>
  );
}

export default ConnectCanvaButton;
