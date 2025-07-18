import React, { useState } from "react";

import "./styleguide.css";
import "./style.scss";

const ApiKeyPage = (props) => {
  const { handleSubmit, showLoader, apiKey, setApiKey } = props;
  // State to manage the error details
  const [error, setError] = useState(null);

  const handleInputChange = (e) => {
    setApiKey(e.target.value);

    // Clear the error message when the user starts typing again
    setError(null);
  };

  const handleFormSubmit = async (e) => {
    e.preventDefault();

    const result = await handleSubmit(); // Assuming handleSubmit returns a promise with validation result
    console.log("tessst", result);

    if (!result.valid) {
      // Set detailed error information
      setError({
        message: result.message || "Invalid API Key",
        suggestion: result.suggestion || "",
        error_type: result.error_type || "unknown_error",
        raw_error: result.raw_error || "",
      });
    } else {
      setError(null);
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
        Enter your CodeDesign Project API Key. Here's a{" "}
        <a
          href="https://www.youtube.com/watch?v=oJkF0eZamUc"
          rel="noreferrer"
          target="_blank"
        >
          short video{" "}
        </a>{" "}
        that will help you get started.
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
          {error && (
            <div className="cd-error-container">
              <div className="cd-error-message">{error.message}</div>
              {error.suggestion && (
                <div className="cd-error-suggestion">{error.suggestion}</div>
              )}
            </div>
          )}
          <div className="info-text">
            To get the API key, head over to your{" "}
            <strong>project's settings</strong>, and then{" "}
            <strong>Integrations</strong>. If you need any help, do not hesitate
            to get in{" "}
            <a
              href="https://codedesign.ai/support"
              rel="noreferrer"
              target="_blank"
              className="span"
            >
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
