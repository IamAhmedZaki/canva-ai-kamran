import React, { useEffect, useState } from "react";

function ConnectCanvaButton() {
  const [isConnected, setIsConnected] = useState(false);
  const [showCreateForm, setShowCreateForm] = useState(false);
  const [loading, setLoading] = useState(false);
  const [formData, setFormData] = useState({
    title: "",
    design_name: "doc",
    asset_id: "",
  });
  const [message, setMessage] = useState({ type: "", text: "" });

  useEffect(() => {
    const params = new URLSearchParams(window.location.search);
    const authStatus = params.get("auth");

    if (authStatus === "success") {
      setMessage({ type: "success", text: "Successfully connected to Canva!" });
      setIsConnected(true);
      window.history.replaceState({}, "", window.location.pathname);
    } else if (authStatus === "failed") {
      const error = params.get("error");
      setMessage({ type: "error", text: `Canva connection failed: ${error}` });
    }
  }, []);

  const handleConnect = () => {
    window.location.href = "http://127.0.0.1:8000/canva/authorize";
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: value,
    }));
  };

  const handleCreateDesign = async () => {
    setLoading(true);
    setMessage({ type: "", text: "" });

    try {
      const response = await fetch("http://127.0.0.1:8000/canva/designs/create", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json",
        },
        credentials: "include",
        body: JSON.stringify(formData),
      });

      const data = await response.json();

      if (response.ok && data.success) {
        setMessage({
          type: "success",
          text: `Design "${data.design.title}" created successfully!`,
        });
        
        // Open the design in Canva editor
        if (data.design.edit_url) {
  window.location.href = data.design.edit_url;
}


        // Reset form
        setFormData({
          title: "",
          design_name: "doc",
          asset_id: "",
        });
        setShowCreateForm(false);
      } else {
        setMessage({
          type: "error",
          text: data.error || "Failed to create design",
        });
      }
    } catch (error) {
      setMessage({
        type: "error",
        text: "Network error. Please try again.",
      });
    } finally {
      setLoading(false);
    }
  };

  return (
    <div style={{ padding: "20px", fontFamily: "Arial, sans-serif" }}>
      {/* Status Messages */}
      {message.text && (
        <div
          style={{
            padding: "12px",
            borderRadius: "8px",
            marginBottom: "16px",
            backgroundColor: message.type === "success" ? "#d4edda" : "#f8d7da",
            color: message.type === "success" ? "#155724" : "#721c24",
            border: `1px solid ${message.type === "success" ? "#c3e6cb" : "#f5c6cb"}`,
          }}
        >
          {message.text}
        </div>
      )}

      {/* Connect Button */}
      {!isConnected && (
        <button
          style={{
            padding: "10px 16px",
            borderRadius: "8px",
            backgroundColor: "#00C4CC",
            color: "white",
            border: "none",
            cursor: "pointer",
            fontSize: "14px",
            fontWeight: "500",
          }}
          onClick={handleConnect}
        >
          Connect Canva
        </button>
      )}

      {/* Create Design Section */}
      {isConnected && (
        <div>
          <button
            style={{
              padding: "10px 16px",
              borderRadius: "8px",
              backgroundColor: "#00C4CC",
              color: "white",
              border: "none",
              cursor: "pointer",
              fontSize: "14px",
              fontWeight: "500",
              marginBottom: "16px",
            }}
            onClick={() => setShowCreateForm(!showCreateForm)}
          >
            {showCreateForm ? "Cancel" : "Create New Design"}
          </button>

          {showCreateForm && (
            <div
              style={{
                backgroundColor: "#f8f9fa",
                padding: "20px",
                borderRadius: "8px",
                maxWidth: "500px",
              }}
            >
              <h3 style={{ marginTop: 0, marginBottom: "16px" }}>Create Design</h3>

              {/* Title Input */}
              <div style={{ marginBottom: "16px" }}>
                <label
                  htmlFor="title"
                  style={{ display: "block", marginBottom: "8px", fontWeight: "500" }}
                >
                  Design Title (Optional)
                </label>
                <input
                  type="text"
                  id="title"
                  name="title"
                  value={formData.title}
                  onChange={handleInputChange}
                  placeholder="New Design"
                  style={{
                    width: "100%",
                    padding: "8px 12px",
                    borderRadius: "4px",
                    border: "1px solid #ced4da",
                    fontSize: "14px",
                    boxSizing: "border-box",
                  }}
                />
              </div>

              {/* Design Type Select */}
              <div style={{ marginBottom: "16px" }}>
                <label
                  htmlFor="design_name"
                  style={{ display: "block", marginBottom: "8px", fontWeight: "500" }}
                >
                  Design Type *
                </label>
                <select
                  id="design_name"
                  name="design_name"
                  value={formData.design_name}
                  onChange={handleInputChange}
                  style={{
                    width: "100%",
                    padding: "8px 12px",
                    borderRadius: "4px",
                    border: "1px solid #ced4da",
                    fontSize: "14px",
                    boxSizing: "border-box",
                  }}
                >
                  <option value="doc">Document</option>
                  <option value="presentation">Presentation</option>
                  <option value="whiteboard">Whiteboard</option>
                </select>
              </div>

              {/* Asset ID Input */}
              <div style={{ marginBottom: "20px" }}>
                <label
                  htmlFor="asset_id"
                  style={{ display: "block", marginBottom: "8px", fontWeight: "500" }}
                >
                  Asset ID (Optional)
                </label>
                <input
                  type="text"
                  id="asset_id"
                  name="asset_id"
                  value={formData.asset_id}
                  onChange={handleInputChange}
                  placeholder="Enter asset ID to auto-insert"
                  style={{
                    width: "100%",
                    padding: "8px 12px",
                    borderRadius: "4px",
                    border: "1px solid #ced4da",
                    fontSize: "14px",
                    boxSizing: "border-box",
                  }}
                />
              </div>

              {/* Submit Button */}
              <button
                onClick={handleCreateDesign}
                disabled={loading}
                style={{
                  padding: "10px 20px",
                  borderRadius: "8px",
                  backgroundColor: loading ? "#6c757d" : "#00C4CC",
                  color: "white",
                  border: "none",
                  cursor: loading ? "not-allowed" : "pointer",
                  fontSize: "14px",
                  fontWeight: "500",
                  width: "100%",
                }}
              >
                {loading ? "Creating..." : "Create Design"}
              </button>
            </div>
          )}
        </div>
      )}
    </div>
  );
}

export default ConnectCanvaButton;