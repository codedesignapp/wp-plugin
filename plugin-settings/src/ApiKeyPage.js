import React, { useState } from "react";

import "./styleguide.css";
import "./style.scss";

const ApiKeyPage = (props) => {
  const { handleSubmit, showLoader, apiKey, setApiKey } = props;
  // State to manage the error message
  const [errorMessage, setErrorMessage] = useState("");

  const handleInputChange = (e) => {
    setApiKey(e.target.value);

    // Clear the error message when the user starts typing again
    setErrorMessage("");
  };

  const handleFormSubmit = async (e) => {
    e.preventDefault();

    const result = await handleSubmit(); // Assuming handleSubmit returns a promise with validation result
    console.log("tessst", result);

    if (!result.valid) {
      // Assuming result contains a message for the error
      setErrorMessage(result.message || "Invalid API Key");
    }
  };
  return (
    <div className="cd-api-container">
      <div className="cd-logo">
        <img
          src={
            "https://cdn.cdsn.me/v1/storage/buckets/63e727f01c9cd2a8490f/files/6538b048bf70db3aafe9/view?project=63e727dfc53e6679e400"
          }
          alt="CodeDesign Brand Wordmark"
        />
      </div>
      <p className="cd-api-intro-text">
        You can find your site's API key by going to the{" "}
        <a
          href="https://dev.codedesign.ai"
          target="_blank"
          rel="noopener noreferrer"
        >
          CodeDesign Dashboard
        </a>
        , then your site's settings
      </p>
      <div>
        <div className="cd-api-input-section">
          <input
            className="cd-api-input-field"
            placeholder="Your API Key..."
            value={apiKey}
            onChange={handleInputChange}
          />
          <div className="button-container" onClick={handleFormSubmit}>
            <button className="button-sync">
              {showLoader && <div className="css-spinner"></div>}
              {!showLoader
                ? "Activate API"
                : "Authenticating your API. Please wait..."}
            </button>
          </div>
          <div className="cd-error-message">{errorMessage}</div>
          <div className="info-text">
            To get the API key, head over to your project's settings, and then
            Integrations. If you need any help, do not hesitate to get in{" "}
            <a href="https://codedesign.ai/support" className="span">
              touch with us
            </a>
            .
          </div>
        </div>
      </div>
    </div>
  );
};

export default ApiKeyPage;
