export default function DonationCard({
  logo,
  logoAlt,
  logoWide = false,
  title,
  description,
  buttons,
  onButtonClick,
}) {
  return (
    <div className="donation-card">
      <div className={`donation-card__logo${logoWide ? " donation-card__logo--wide" : ""}`}>
        <img src={logo} alt={logoAlt} />
      </div>
      <div className="donation-card__body">
        <h4 className="donation-card__title">{title}</h4>
        <p className="donation-card__desc" dangerouslySetInnerHTML={{ __html: description }} />
      </div>
      {buttons && buttons.length > 0 ? (
        <div className="donation-card__btns">
          {buttons.map((button, idx) => {
            const isLink = button.href && !button.onClick;
            const Element = isLink ? "a" : "button";
            const props = isLink
              ? {
                  href: button.href,
                  target: "_blank",
                  rel: "noopener noreferrer",
                }
              : {
                  type: "button",
                  onClick: button.onClick,
                };

            return (
              <Element
                key={idx}
                className="btn-basic btn-basic--white"
                {...props}
              >
                {button.label}
              </Element>
            );
          })}
        </div>
      ) : null}
    </div>
  );
}
