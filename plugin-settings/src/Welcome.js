import React from "react";
import logo from "./CodeDesignLogo2.svg"
import "./styleguide.css";
import "./style2.scss";
const Welcome = () => {
  return (
    <div className="cd-welcome-container">
      <div className="cd-banner-container">
        <img className="cd-banner-image" src={logo}></img>
      </div>
      <div className="cd-top-bar">
        <div className="cd-top-header">AI Website pages with CodeDesign</div>
        <p className="cd-top-text">Connect CodeDesign Pages</p>
      </div>
      {/* cards section1 */}
      <div className="cd-cards-container">
        {/* card 1  */}
        <div className="cd-card-wrapper">
          <h2 className="cd-card-number">01</h2>
          <h2 className="cd-card-title">Install Wordpress Plugin</h2>
          <p className="cd-card-description">
            You can find your site's API key by going to then your site's
            settings
          </p>
        </div>
        {/* card 2  */}
        <div className="cd-card-wrapper">
          <h2 className="cd-card-number">02</h2>
          <h2 className="cd-card-title">Copy & Paste the API key</h2>
          <p className="cd-card-description">
            You can find your site's API key by going to then your site's
            settings
          </p>
        </div>
      {/* card  3 */}
      <div className="cd-card-wrapper">
          <h2 className="cd-card-number">03</h2>
          <h2 className="cd-card-title">Sync CodeDesign Pages</h2>
          <p className="cd-card-description">
          You can find your site's API key by going to then your site's settings
          </p>
        </div>


      </div>
    </div>
  );
};

export default Welcome;
