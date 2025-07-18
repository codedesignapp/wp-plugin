// src/App.js

import React, { useState, useEffect } from "react";
import { Routes, Route, useNavigate } from "react-router-dom";
import ApiKeyPage from "./ApiKeyPage";
import SyncPage from "./SyncPage";
import Welcome from "./Welcome";
function App() {
  const navigate = useNavigate();

  const [showSettingsLoader, setShowSettingsLoader] = useState(false);
  const apiKeyFromWP = window.wpData && window.wpData.apiKey;
  console.log(apiKeyFromWP, "testmonark");

  const [apiKey, setApiKey] = useState(""); // Initially empty
  const [hasApiKey, setHasApiKey] = useState(apiKeyFromWP ? true : false);

  useEffect(() => {
    const initialPage = localStorage.getItem("initialPage");
    console.log(initialPage, "testtt");
    if (initialPage) {
      navigate(initialPage);
      localStorage.removeItem("initialPage"); // Clear the item after redirecting
    }
  }, [navigate]);

  // Inside your component
  function handleAPIKeyDisconnect() {
    console.log("Disconnect button clicked!"); // Debug log
    setShowSettingsLoader(true);

    console.log("testtt");
    // Use relative path in development (for proxy), full URL in production
    const ajaxURL =
      process.env.NODE_ENV === "development"
        ? "/wp-admin/admin-ajax.php"
        : window.wpData && window.wpData.ajax_url;
    console.log("AJAX URL:", ajaxURL); // Debug log

    fetch(ajaxURL, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({
        action: "disconnect_api_key",
      }),
    })
      .then((response) => {
        console.log("Disconnect response:", response); // Debug log
        return response.json();
      })
      .then((data) => {
        console.log("Disconnect data:", data); // Debug log
        if (data.success) {
          console.log("Disconnected successfully");
          setHasApiKey(false);

          // Update your state or UI here, if necessary
        } else {
          console.log("Error disconnecting");
          setHasApiKey(false);
        }
        setShowSettingsLoader(false);
      })
      .catch((error) => {
        console.log("Error during the disconnection:", error);
        setShowSettingsLoader(false);
      });
  }

  const handleAPIKeySubmit = async (e) => {
    // Use relative path in development (for proxy), full URL in production
    const ajaxURL =
      process.env.NODE_ENV === "development"
        ? "/wp-admin/admin-ajax.php"
        : window.wpData && window.wpData.ajax_url;
    console.log("tess2");
    setShowSettingsLoader(true);
    return fetch(ajaxURL, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({
        action: "validate_api_key",
        api_key: apiKey,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        console.log("tess3", data);
        setShowSettingsLoader(false);

        if (data.valid) {
          setHasApiKey(true);
          console.log("API Key is valid");
          return {
            valid: true,
            message: data.message || "API key validated successfully!",
          };
        } else {
          console.log("API Key validation failed:", data);
          return {
            valid: false,
            message: data.error_message || "Invalid API Key",
            suggestion: data.suggestion || "",
            error_type: data.error_type || "unknown_error",
            raw_error: data.raw_error || "",
          };
        }
      })
      .catch((error) => {
        console.log("Error during the validation:", error);
        setShowSettingsLoader(false);
        return {
          valid: false,
          message: "Network Error - Unable to connect to server",
          suggestion: "Please check your internet connection and try again.",
          error_type: "network_error",
        };
      });
  };
  return (
    <div className="App">
      {/* Your other components or routes here */}
      <Routes>
        <Route path="/welcome" element={<Welcome />}></Route>
        <Route
          path="/"
          element={
            hasApiKey ? (
              <SyncPage
                setHasApiKey={setHasApiKey}
                showLoader={showSettingsLoader}
                handleDisconnect={handleAPIKeyDisconnect}
              />
            ) : (
              <ApiKeyPage
                showLoader={showSettingsLoader}
                handleSubmit={handleAPIKeySubmit}
                apiKey={apiKey}
                setApiKey={setApiKey}
                setHasApiKey={setHasApiKey}
                handleDisconnect={handleAPIKeyDisconnect}
              />
            )
          }
        ></Route>
      </Routes>
      {/* 
      {hasApiKey ? (
        <SyncPage
          setHasApiKey={setHasApiKey}
          showLoader={showSettingsLoader}
          handleDisconnect={handleAPIKeyDisconnect}
        />
      ) : (
        <ApiKeyPage
          showLoader={showSettingsLoader}
          handleSubmit={handleAPIKeySubmit}
          apiKey={apiKey}
          setApiKey={setApiKey}
          setHasApiKey={setHasApiKey}
          handleDisconnect={handleAPIKeyDisconnect}
        />
      )} */}
    </div>
  );
}

export default App;
