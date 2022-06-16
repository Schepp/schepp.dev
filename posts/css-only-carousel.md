---
title: A CSS-only Carousel
description: I've recently read about and discussed CSS Scroll Snapping so often that I felt like I should build a CSS-only carousel based on it.
image: https://schepp.dev/img/carousel.jpg
date: 2019-12-08
tags:
  - css
layout: layouts/post.njk
---
I've recently [read about](https://24ways.org/2019/beautiful-scrolling-experiences-without-libraries/) and [discussed](https://twitter.com/AndyDavies/status/1202862028412661760) CSS Scroll Snapping so often that I felt like I should build a CSS-only carousel based on it. There it is:

<iframe src="/demos/css-only-carousel/index.html" height="600" style="background-color: #eee">
</iframe>

[Link to Codepen](https://codepen.io/Schepp/pen/WNbQByE?editors=1100)

## Here are a few things to note:

Accessibility is in line with all other CSS-only experiments: it can only be considered mediocre in term of semantics and visual indicators. Don't do this in production.

The going forward and back is done via a combination of [CSS Scroll Snap](https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_Scroll_Snap) together with [`scroll-behavior: smooth`](https://developer.mozilla.org/en-US/docs/Web/CSS/scroll-behavior) and moving the focus through the slides via anchor links.

The nicest trick I pull off is the one for the auto-forward:

1. first I slowly offset the scroll snap points to the right, making the scroll area follow along due to being snapped to them.
2. after having scrolled the width of a whole slide, I deactivate the snapping. The scroll area is now untied from the scroll snap points.
3. Now I let the scroll snap points jump back to their initial positions without them "snap-dragging" the scroll area back with them
4. Then I re-engage the snapping which now lets the scroll area snap to a different snap point 🤯 Whatever... [look at the code](https://codepen.io/Schepp/pen/WNbQByE?editors=1100) 🙃

_The cover image of this post is [Carousel spinning in Sunset](https://www.shutterstock.com/de/image-photo/carousel-ride-spins-fast-air-sunset-721817491) by [bubutu](https://www.shutterstock.com/de/g/Wojciech+Kozielczyk)_
