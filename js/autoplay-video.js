(() => {
  const callback = (entries, observer) => {
    entries.forEach(entry => {
      // Each entry describes an intersection change for one observed
      // target element:
      //   entry.boundingClientRect
      //   entry.intersectionRatio
      //   entry.intersectionRect
      //   entry.isIntersecting
      //   entry.rootBounds
      //   entry.target
      //   entry.time
      const video = entry.target;
      if (entry.isIntersecting) {
        video.setAttribute('muted', '');
        video.setAttribute('autoplay', '');
        try {
          video.play();
        } catch (e) {
          // noop
        }
      } else {
        video.removeAttribute('muted', '');
        video.removeAttribute('autoplay');
        try {
          video.pause();
        } catch (e) {
          // noop
        }
      }
    });
  };
  const options = {
    root: null,
    rootMargin: '100px 0px 1000px 0px',
    threshold: 0.01,
  };
  const observer = window.IntersectionObserver ? new IntersectionObserver(callback, options) : {
    observe: (video) => {
      video.setAttribute('autoplay', '');
    }
  };
  const videos = document.querySelectorAll('video[muted]:not([controls]):not([poster]');
  let i = 0;

  for (; i < videos.length; i++) {
    observer.observe(videos[i]);
  }
})();
