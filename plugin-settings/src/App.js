// src/App.js

import React, { useState } from "react";
import {  Routes, Route, BrowserRouter } from "react-router-dom";
import ApiKeyPage from "./ApiKeyPage";
import SyncPage from "./SyncPage";
import Welcome from "./Welcome"
function App() {
  const [showSettingsLoader, setShowSettingsLoader] = useState(false);
  const apiKeyFromWP = window.wpData && window.wpData.apiKey;
  console.log(apiKeyFromWP, "testmonark");

  const [apiKey, setApiKey] = useState(""); // Initially empty
  const [hasApiKey, setHasApiKey] = useState(apiKeyFromWP ? true : false);

  // Inside your component
  function handleAPIKeyDisconnect() {
    setShowSettingsLoader(true);

    console.log("testtt");
    const ajaxURL = window.wpData && window.wpData.ajax_url;
    fetch(ajaxURL, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({
        action: "disconnect_api_key",
      }),
    })
      .then((response) => response.json())
      .then((data) => {
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
    const ajaxURL = window.wpData && window.wpData.ajax_url;
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
        console.log("tess3");
        setShowSettingsLoader(false);

        if (data.valid) {
          setHasApiKey(true);
          console.log("API Key is valid");
          return { valid: true };
        } else {
          console.log("API Key is invalid");
          return { valid: false, message: "Invalid API Key" };
        }
      })
      .catch((error) => {
        console.log("Error during the validation:", error);
        setShowSettingsLoader(false);
        return { valid: false, message: "Invalid API Key" };
      });
  };
  return (
    <div className="App">
      {/* Your other components or routes here */}
      <BrowserRouter>
        <Routes>
          <Route
            path="/welcome"
            element={
            
           <Welcome/>
            }

          ></Route>
          <Route path="/" element={
            <ApiKeyPage
            showLoader={showSettingsLoader}
            handleSubmit={handleAPIKeySubmit}
            apiKey={apiKey}
            setApiKey={setApiKey}
            setHasApiKey={setHasApiKey}
            handleDisconnect={handleAPIKeyDisconnect}
          />
          }>

          </Route>
        </Routes>
      </BrowserRouter>
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
