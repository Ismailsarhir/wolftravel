/**
 * Animated Lines - Line by Line Scroll Animation with GSAP SplitText
 *
 * @package TransfertMarrakech
 */

import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { SplitText } from 'gsap/SplitText';

gsap.registerPlugin(SplitText, ScrollTrigger);

document.addEventListener('DOMContentLoaded', () => {
  const animatedLines = document.querySelectorAll('.animated-lines');

  if (animatedLines.length === 0) {
    return;
  }

  gsap.set(animatedLines, { opacity: 1 });

  const splitTextIntoLines = (element) => {
    if (element.querySelector('.line')) {
      return;
    }

    SplitText.create(element, {
      type: 'words,lines',
      linesClass: 'line',
      onSplit: (instance) => {
        instance.lines.forEach((line) => {
          const lineWrapper = document.createElement('div');
          lineWrapper.classList.add('line-wrapper');
          lineWrapper.style.overflow = 'hidden';
          line.parentNode.insertBefore(lineWrapper, line);
          lineWrapper.appendChild(line);
        });
      }
    });
  };

  const animateLines = (element) => {
    const lines = element.querySelectorAll('.line');

    if (lines.length === 0) {
      return;
    }

    gsap.fromTo(lines, {
      y: '145%'
    }, {
      y: '0',
      stagger: 0.15,
      duration: 0.9,
      ease: 'circ.out',
      scrollTrigger: {
        trigger: element,
        start: 'top 75%',
        toggleActions: 'play none none none',
        once: true,
      }
    });
  };

  const init = () => {
    document.fonts.ready.then(() => {
      animatedLines.forEach((el) => {
        splitTextIntoLines(el);
        animateLines(el);
      });
    });
  };

  // On pages with a hero preloader, delay until overflow is restored and
  // layout is stable — otherwise autoSplit would re-split against stale targets
  if (document.querySelector('.hero-img')) {
    window.addEventListener('preloader-complete', init, { once: true });
  } else {
    init();
  }
});
