import React, { useState } from "react";
import logo from "./CodeDesignLogo2.svg";
import card1_img from "./card1-img.svg";
import card2_img from "./card2-img.svg";
import card3_img from "./card3-img.svg";
import thumbnail from "./video.svg";
import card5_img from "./card5-img.svg";
import "./styleguide.css";
import "./style2.scss";

const Welcome = () => {
  const [isHovered, setIsHovered] = useState(false);
  const handleThumbnailClick = () => {
    window.open("https://www.youtube.com/watch?v=FzAE5aBVPgc&t=5s");
  };

  return (
    <div className="cd-welcome-container">
      <div className="cd-banner-container">
        <img className="cd-banner-image" src={logo} alt="Banner Image"></img>
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
          <div className="cd-card-wrap">
            <h2 className="cd-card-title">Install Wordpress Plugin</h2>
            <p className="cd-card-description">
              You can find your site's API key by going to then your site's
              settings
            </p>
          </div>

          <img
            src={card1_img}
            className="cd-card1-img"
            alt="Card 1"
          ></img>
        </div>

        {/* card 2  */}
        <div className="cd-card-wrapper">
          <h2 className="cd-card-number">02</h2>
          <div className="cd-card-wrap">
            <h2 className="cd-card-title">Copy & Paste the API key</h2>
            <p className="cd-card-description">
              You can find your site's API key by going to then your site's
              settings
            </p>
          </div>
          <img
            src={card2_img}
            className="cd-card2-img"
            alt="card 2 "
          ></img>
        </div>

        {/* card  3 */}
        <div className="cd-card-wrapper">
          <h2 className="cd-card-number">03</h2>
          <div>
            <img
              src={card3_img}
              className="cd-card3-img"
              alt="card 3 image"
            ></img>
           <div className="cd-card-wrap">
            <h2 className="cd-card-title">Sync CodeDesign Pages</h2>
            <p className="cd-card-description">
              You can find your site's API key by going to then your site's
              settings
            </p></div>
          </div>
        </div>
      </div>

      {/* card 4  */}
      <div className="cd-cards-container-2">
        <div className="cd-card-wrapper4">
          <h2 className="cd-card-title">Learn How to use plugin ~ 2min</h2>
          <div className="cd-thumbnail-wrapper">
            <div className="cd-thumbnail-overlay">
              <button
                className="cd-thumbnail-btn"
                onClick={handleThumbnailClick}
              >
                Watch Video
              </button>
            </div>
          </div>

          {/* If emebeeded video required */}
          {/* <iframe
           width= "343px"
           height="193px"
            src="https://www.youtube.com/embed/FzAE5aBVPgc"
            title="Build your website with CodeDesign.ai"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen
          ></iframe> */}
        </div>

        {/* Card 5  */}
        <div className="cd-card-wrapper5">
          <div className="cd-card-content">
            <div className="cd-card-box1">
              <div className="cd-box-wrapper">
                <h2 className="cd-box-title">Do you need Help?</h2>
                <p className="cd-box-text">
                  You can find your site's API key by going to then your site's
                  settings
                </p>
              </div>
              <button className="cd-box1-btn">Request Help in Chat</button>
            </div>

            <div className="cd-card-box2">
              <p className="cd-box2-text">
                This version of the plugin is not compatible with CodeDesign
                pages. Please contact <span>CodeDeign Chat Support</span> for any help with
                the plugin.
              </p>
            </div>
          </div>
          <img
            src={card5_img}
            className="cd-card5-img"
            alt="card 5 "
          ></img>
        </div>
      </div>
    </div>
  );
};

export default Welcome;
