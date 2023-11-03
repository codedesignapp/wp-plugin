import React from "react";

import "./styleguide.css";
import "./style.scss";

const SyncPage = (props) => {
  const { handleDisconnect, showLoader } = props;
  // State to manage the API Key input field

  return (
    <div className="cd-sync-container">
      <div className="cd-sync-header-container">
        <div className="cd-sync-header-h1">Synchronizing your Pages..</div>
        <div className="cd-sync-header-p">
          We're currently synchronizing your Pages across Wordpress and
          CodeDesign. This may take a few minutes
        </div>
      </div>

      <div className="cd-sync-animation-container">
        <img src="https://cdn.cdsn.me/v1/storage/buckets/63e727f01c9cd2a8490f/files/6538845b8254f97b3eca/view?project=63e727dfc53e6679e400" />
        <video autoPlay loop muted>
          <source
            src="https://cdn.cdsn.me/v1/storage/buckets/63e727f01c9cd2a8490f/files/653884fe3107284184a3/view?project=63e727dfc53e6679e400"
            type="video/webm"
          />
        </video>
        <img src="https://cdn.cdsn.me/v1/storage/buckets/63e727f01c9cd2a8490f/files/65388463d17b49210d47/view?project=63e727dfc53e6679e400" />
      </div>
      <div className="cd-sync-footer-container">
        <div className="cd-sync-footer-last-sync-and-info">
          <div className="cd-sync-footer-last-sync">
            Last Sync: 9:71pm 12/10/2023
          </div>
        </div>
        <div className="cd-sync-footer-additional-info">
          12 Pages - 3 Linked Components - 8 Functions
        </div>
        <div className="cd-sync-footer-info-text">
          <span>
            This version of the plugin is not compatible with CodeDesign pages.
            Please contact <span className="span">CodeDeign Chat Support</span>{" "}
            for any help with the plugin.
          </span>
          <span
            className="cd-sync-footer-disconnect"
            onClick={handleDisconnect}
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
