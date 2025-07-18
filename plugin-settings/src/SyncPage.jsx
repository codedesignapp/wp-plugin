import React from "react";

import "./styleguide.css";
import "./style.scss";

const SyncPage = (props) => {
  const rawData = window.wpData && window.wpData;

  // Safe JSON parsing with fallbacks for corrupt data
  let jsonData = null;
  let pagesCount = 0;
  let lastPublished = "N/A";

  try {
    if (rawData && rawData.projectData) {
      jsonData = JSON.parse(rawData.projectData);

      // Safely get pages count
      if (
        jsonData &&
        jsonData.blueprint &&
        typeof jsonData.blueprint === "object"
      ) {
        pagesCount =
          Object.keys(jsonData.blueprint).filter(
            (item) => !item?.startsWith("linked"),
          )?.length || 0;
      }

      // Safely get last published date
      if (jsonData && jsonData.timestamps && jsonData.timestamps.updatedAt) {
        lastPublished = formatPrettyDate(jsonData.timestamps.updatedAt);
      }
    }
  } catch (error) {
    console.error("Error parsing project data:", error);
    console.log("Raw project data:", rawData?.projectData);
    // Use fallback values - component will still render
    jsonData = { blueprint: {}, timestamps: {} };
    pagesCount = 0;
    lastPublished = "Data unavailable";
  }

  const { handleDisconnect, showLoader } = props;

  // Debug log to check if handleDisconnect is properly passed
  console.log("SyncPage handleDisconnect:", handleDisconnect);
  console.log("SyncPage showLoader:", showLoader);

  // Wrapper function to debug clicks
  const handleDisconnectClick = () => {
    console.log("Disconnect span clicked!");
    if (handleDisconnect) {
      handleDisconnect();
    } else {
      console.error("handleDisconnect function not available!");
    }
  };

  // State to manage the API Key input field

  function formatPrettyDate(dateString) {
    const options = {
      year: "numeric",
      month: "long",
      day: "numeric",
      hour: "2-digit",
      minute: "2-digit",
      second: "2-digit",
    };
    const date = new Date(dateString);
    return date.toLocaleString("en-US", options);
  }

  return (
    <div className="cd-sync-container">
      <div className="cd-sync-header-container">
        <div className="cd-sync-header-h1">Pages are now in sync!</div>
        <div className="cd-sync-header-p">
          All your CodeDesign.ai pages are now synced to WordPress.
        </div>
      </div>

      <div className="cd-sync-animation-container">
        <img
          alt="CodeDesign Logo"
          src="https://cdn.cdsn.me/v1/storage/buckets/63e727f01c9cd2a8490f/files/6538845b8254f97b3eca/view?project=63e727dfc53e6679e400"
        />
        <video autoPlay loop muted>
          <source
            src="https://cdn.cdsn.me/v1/storage/buckets/63e727f01c9cd2a8490f/files/653884fe3107284184a3/view?project=63e727dfc53e6679e400"
            type="video/webm"
          />
        </video>
        <img
          alt="WordPress Logo"
          src="https://cdn.cdsn.me/v1/storage/buckets/63e727f01c9cd2a8490f/files/65388463d17b49210d47/view?project=63e727dfc53e6679e400"
        />
      </div>
      <div className="cd-sync-footer-container">
        <div className="cd-sync-footer-last-sync-and-info">
          <div className="cd-sync-footer-last-sync">
            Last Sync: {lastPublished}
          </div>
        </div>
        <div className="cd-sync-footer-additional-info">{pagesCount} Pages</div>
        <div className="cd-sync-footer-info-text">
          {lastPublished === "Data unavailable" ? (
            <span
              style={{
                color: "#e74c3c",
                marginBottom: "8px",
                display: "block",
              }}
            >
              ⚠️ Project data appears to be corrupted. Try disconnecting and
              reconnecting your API key, or republish from CodeDesign.
            </span>
          ) : null}
          <span>
            To publish a newer version, head over to the project and press the
            Publish button. Please contact{" "}
            <span className="span">CodeDeign Support</span> for any help with
            the plugin.
          </span>
          <span
            className="cd-sync-footer-disconnect"
            onClick={handleDisconnectClick}
          >
            {" "}
            {showLoader ? "Disconnecting..." : "Disconnect now"}
          </span>
        </div>
      </div>
    </div>
  );
};

export default SyncPage;
