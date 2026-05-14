/**
 * Feature flags for frontend functionality
 * 
 * To enable scroll snap:
 * - Set SCROLL_SNAP_ENABLED to true
 * - Smooth scroll behavior will continue working from CSS
 * - Pages will snap-scroll on wheel events
 */
export const FEATURES = {
  // Enables custom scroll snap behavior on wheel events (fullscreen sections snap)
  SCROLL_SNAP_ENABLED: false,

  // Enables scroll-triggered fade/slide-in animations for elements with [data-ani] attribute.
  // Supported values: top | bottom | left | right | rotate | scale | img | preserveTop | hidden | skew
  SCROLL_FADE: true,
};

export default FEATURES;
