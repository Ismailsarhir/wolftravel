import gsap from "gsap";
import ScrollTrigger from "gsap/ScrollTrigger";
import SplitText from "gsap/SplitText";
import CustomEase from "gsap/CustomEase";

gsap.registerPlugin(CustomEase, SplitText, ScrollTrigger);

CustomEase.create("hop", "0.9, 0, 0.1, 1");
CustomEase.create("glide", "0.8, 0, 0.2, 1");

// Module-level: runs as soon as the script is parsed, before the browser
// has a chance to restore scroll position from session history.
history.scrollRestoration = "manual";
window.scrollTo({ top: 0, left: 0, behavior: "instant" });

// iOS Safari bfcache: navigating back serves the page from cache,
// skipping DOMContentLoaded entirely. Force scroll-to-top on restore.
window.addEventListener("pageshow", (e) => {
  if (e.persisted) window.scrollTo({ top: 0, left: 0, behavior: "instant" });
});

document.addEventListener("DOMContentLoaded", () => {
  if (!document.querySelector(".hero-img")) return;

  document.documentElement.style.overflow = "hidden";
  gsap.set(".header", { autoAlpha: 0, y: -24 });

  document.fonts.ready.then(() => {
    // Match the CSS breakpoint (global.css: max-width: 767px → --w: 375)
    const isMobile = window.innerWidth < 768;
    const { innerWidth } = window;
    const introImages = gsap.utils.toArray(".intro-img");

    // Mobile: 3 images (skip outer 2) — 5 images at 20% scale + 40px gaps
    // would need 535px on a 375px screen and break the layout.
    // Desktop: all 5 images, original layout.
    const activeImages = isMobile ? introImages.slice(1, 4) : introImages;

    if (isMobile) {
      gsap.set([introImages[0], introImages[4]], { autoAlpha: 0 });
    }

    const scale     = isMobile ? 0.28 : 0.2;
    const gap       = isMobile ? 10   : 40;
    const rotations = isMobile ? [-10, 0, 10] : [-15, 5, -7.5, 10, -2.5];
    const delay     = isMobile ? 0.8  : 1;

    const scaledW = innerWidth * scale;
    const totalW  = activeImages.length * scaledW + (activeImages.length - 1) * gap;
    const startX  = (innerWidth - totalW) / 2;
    const offStartX = startX - innerWidth * 1.3;

    const heroSplit = SplitText.create(".hero-header h1", { type: "lines", mask: "lines" });
    gsap.set(heroSplit.lines, { y: "100%" });

    const centeredXValues = activeImages.map((img, i) => {
      const offset = i * (scaledW + gap) + scaledW / 2 - innerWidth / 2;
      gsap.set(img, { scale, x: offStartX + offset, rotation: rotations[i], borderRadius: "2.5rem" });
      return startX + offset;
    });


    const tl = gsap.timeline({ delay });

    tl.to(".preloader", { scaleX: 1, duration: 1.5, ease: "glide" })
      .set(".preloader", { transformOrigin: "right" })
      .to(".preloader", { scaleX: 0, duration: 1.25, ease: "hop" })
      .to(".preloader-overlay", {
        clipPath: "polygon(0% 0%, 100% 0%, 100% 0%, 0% 0%)",
        duration: 1,
        ease: "hop",
        onComplete: () => {
          document.documentElement.style.overflow = "";
          ScrollTrigger.refresh();
          window.dispatchEvent(new Event("preloader-complete"));
        },
      }, "<0.75");

    activeImages.forEach((img, i) => {
      tl.to(img, { x: centeredXValues[i], duration: 1.5, ease: "glide" }, "<0.025");
    });

    if (isMobile) {
      tl.to(activeImages[0], { x: "-110vw", duration: 1.5, ease: "glide" }, "spread")
        .to(activeImages[2], { x: "110vw",  duration: 1.5, ease: "glide" }, "spread");
    } else {
      tl.to(".intro-img:nth-child(1), .intro-img:nth-child(2)", { x: "-100vw", duration: 1.5, ease: "glide" }, "spread")
        .to(".intro-img:nth-child(4), .intro-img:nth-child(5)", { x: "100vw",  duration: 1.5, ease: "glide" }, "spread");
    }

    tl.to(".hero-img", { scale: 1, x: 0, rotation: 0, borderRadius: 0, duration: 1.5, ease: "glide" }, "<")
      .to(heroSplit.lines, { y: "0%", duration: 1, stagger: 0.1, ease: "power3.out" })
      .to(".header", { autoAlpha: 1, y: 0, duration: 0.8, ease: "power3.out", clearProps: "transform" }, "<0.3")
  });
});
