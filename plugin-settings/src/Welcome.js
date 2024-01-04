import React from "react";

// import thumbnail from "./video.svg";
import "./styleguide.css";
import "./style2.scss";

const Welcome = () => {
  // const [isHovered, setIsHovered] = useState(false);
  const handleThumbnailClick = (type) => {
    let url;
    switch (type) {
      case "support":
        url = "https://codedesign.ai/support";
        break;

      case "video":
        url = "https://www.youtube.com/watch?v=oJkF0eZamUc";
        break;

      default:
        url = "https://codedesign.ai/support";
        break;
    }
    window.open(url);
  };

  return (
    <div className="cd-welcome-container">
      <div className="cd-banner-container">
        <img
          className="cd-banner-image"
          src="https://assets.codedesign.app/v1/storage/buckets/63e727f01c9cd2a8490f/files/65964b817eeb5caca27f/view?project=63e727dfc53e6679e400"
          alt="Banner"
        ></img>
      </div>
      <div className="cd-top-bar">
        <div className="cd-top-header">
          Build AI Website pages with CodeDesign!
        </div>
        <p className="cd-top-text">
          Design, build and publish your website - with assistance from AI in
          minutes! Once you've prepared the design, you can sync your website to
          WordPress in #3 easy steps:
        </p>
      </div>

      {/* cards section1 */}
      <div className="cd-cards-container">
        {/* card 1  */}
        <div className="cd-card-wrapper">
          <h2 className="cd-card-number">01</h2>
          <div className="cd-card-wrap">
            <h2 className="cd-card-title">Install Wordpress Plugin</h2>
            <p className="cd-card-description">
              Download the{" "}
              <a href="https://cdn.cdsn.me/wordpress">WordPress plugin</a> if
              you haven't already. Next, head over to the integrations tab
              inside your CodeDesign project.
            </p>
          </div>
          <div className="cd-image-wrapper image1">
            <img
              className="cd-card-img "
              src="https://assets.codedesign.app/v1/storage/buckets/63e727f01c9cd2a8490f/files/6596512ba1f6890cc184/view?project=63e727dfc53e6679e400"
              alt="Card 1"
            ></img>
          </div>
        </div>

        {/* card 2  */}
        <div className="cd-card-wrapper">
          <h2 className="cd-card-number">02</h2>
          <div className="cd-card-wrap">
            <h2 className="cd-card-title">Copy & Paste the API key</h2>
            <p className="cd-card-description">
              Copy the API key and paste it inside the plugin. That's it, your
              page should be now live!
            </p>
          </div>
          <div className="cd-image-wrapper image2">
            <img
              className="cd-card-img"
              src="https://assets.codedesign.app/v1/storage/buckets/63e727f01c9cd2a8490f/files/6596513897b2605c9762/view?project=63e727dfc53e6679e400"
              alt="card 2 "
            ></img>
          </div>
        </div>

        {/* card  3 */}
        <div className="cd-card-wrapper">
          <h2 className="cd-card-number">03</h2>
          <div
            style={{
              display: "flex",
              flexDirection: "column",
              gap: "16px",
              alignItems: "center",
            }}
          >
            <div className="cd-image-wrapper">
              <img
                style={{ width: "240px", height: "50px" }}
                className="cd-card-img"
                src="https://assets.codedesign.app/v1/storage/buckets/63e727f01c9cd2a8490f/files/659651436e642d81c821/view?project=63e727dfc53e6679e400"
                alt="card 3 "
              ></img>
            </div>

            <div className="cd-card-wrap">
              <h2 className="cd-card-title">Sync CodeDesign Pages</h2>
              <p className="cd-card-description">
                Sync the changes to WordPress by heading over to CodeDesign.ai's
                builder canvas, and hitting the Publish Now button. Easy!
              </p>
            </div>
          </div>
        </div>
      </div>

      {/* card 4  */}
      <div className="cd-cards-container-2">
        <div className="cd-wrapper-outer">
          <div className="cd-card-wrapper4">
            <h2 className="cd-card-title">Learn How to use plugin ~ 2min</h2>
            <div className="cd-thumbnail-wrapper">
              <div className="cd-thumbnail-overlay">
                <button
                  className="cd-thumbnail-btn"
                  onClick={() => handleThumbnailClick("video")}
                >
                  Watch Video
                </button>
              </div>
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
                  We're happy to assist you with anything!
                </p>
              </div>
              <button
                className="cd-box1-btn"
                onClick={() => handleThumbnailClick("support")}
              >
                Request Help in Chat
              </button>
            </div>

            <div className="cd-card-box2">
              <p className="cd-box2-text">
                If you get a blank page or get an error while connecting
                (Invalid API key), try hitting the Publish button on
                CodeDesign.ai's builder. Don't hesitate to get in touch with
                <span>
                  {" "}
                  <a href="https://codedesign.ai/support">
                    CodeDeign Chat Support{" "}
                  </a>
                </span>{" "}
                for any help with the plugin.
              </p>
            </div>
          </div>
          <div className="image-wrap">
            <img
              className="cd-card5-img"
              src="https://assets.codedesign.app/v1/storage/buckets/63e727f01c9cd2a8490f/files/65965154b726b635afdf/view?project=63e727dfc53e6679e400"
              alt="card 5 "
            ></img>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Welcome;
