import React, { useEffect } from "react";

function ConnectCanvaButton() {

  // Example in React/JavaScript
useEffect(() => {
    const params = new URLSearchParams(window.location.search);
    const authStatus = params.get('auth');
    
    if (authStatus === 'success') {
        // Show success message
        console.log('Successfully connected to Canva!');
        // Clean up URL
        window.history.replaceState({}, '', window.location.pathname);
    } else if (authStatus === 'failed') {
        const error = params.get('error');
        console.error('Canva connection failed:', error);
    }
}, []);
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
