---
title: Fixing Smooth Scrolling & Page Search (updated!)
description: Native smooth scrolling is one more example of standards paving the cow path by declaring a wide-spread practice officially a thing&#58; being able to smoothly scroll the viewport to another part of a page without the user losing their orientation. As good at it is, though, it also has an undesired side effect on the browser's built-in page search. This posts shows what the problem is and how to solve it.
image: /img/ballet.jpg
date: 2021-01-06
tags:
  - css
layout: layouts/post.njk
---
Yesterday, as I was browsing my Twitter timeline, a tweet from Chris Coyier popped up, in which he mentioned feedback he got for one of his [CSS Tricks](https://css-tricks.com/) experiments. It went as follows:

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">Anecdotal thing: when I had this on <a href="https://twitter.com/css?ref_src=twsrc%5Etfw">@CSS</a>, I had SO MANY reports of people annoyed that when they did &quot;find on page&quot; and ⬆️⬇️ through the results, the smooth scrolling was slow and annoying. Unfortunately, you can&#39;t control the speed or when it happens. <a href="https://t.co/HAio46bYQt">https://t.co/HAio46bYQt</a></p>&mdash; Chris Coyier (@chriscoyier) <a href="https://twitter.com/chriscoyier/status/1346513455516426242?ref_src=twsrc%5Etfw">January 5, 2021</a></blockquote> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

<noscript>
    <img src="/img/twitter-chris-coyer-smooth-scrolling-page-search.png" alt="Screenshot of Chris Coyier's tweet">
</noscript>

Apparently Chris had once switched out JavaScript-based smooth anchor scrolling in favor of a more modern, purely CSS-based solution. All you need to do is slap a `scroll-behavior: smooth` on `html` and all of a sudden scrolls to different places on the page happen smoothly. And that's great!

## Page Search

Sadly, as Chris points out in his tweet, native smooth scrolling negatively affects the UX of page search when cycling through its search results:

<video width="1128" height="718" autoplay muted loop>
  <source src="/img/smooth-scroll-page-search.mp4" type="video/mp4">
</video>

Smooth scrolling is consequently applied to everything. Always. Even when cycling through the browser's page search results. At least that's the case for Chromium. So for the page search it would be desirable for the browser to make an exception to that rule and to deactivate smooth scrolling.

Until the Chromium team [fixes it](https://bugs.chromium.org/p/chromium/issues/detail?id=866694), here is a trick how to solve the problem on your own with a little bit of extra CSS --and HTML--.

## The Solution

First you need to move your assignment of `scroll-behavior` from `html` selector to `html:focus-within`. This will ensure that smooth scrolling is only active while the focus is within the page. Sadly Chrome and Firefox, upon clicking an on-page anchor link, both first assign and then remove focus from the document. Therefore you need to flank the above with two (identical) time-limited animations that force smooth scrolling onto the document for a certain period of time after the click.

```css
@keyframes smoothscroll1 {
  from, to { scroll-behavior: smooth; }
}

@keyframes smoothscroll2 {
  from, to { scroll-behavior: smooth; }
}

html {
  animation: smoothscroll1 1s;
}

html:focus-within {
  animation-name: smoothscroll2;
  scroll-behavior: smooth;
}
```

[Link to Codepen](https://codepen.io/Schepp/pen/wvzNLJz)

Now everytime the user interacts with the surrounding browser interface, as is the case with the page search, smooth scrolling will be disabled and jumping to the results will be instantaneous.

And that's it. Problem solved!

**2021/01/17 Update:** My initial solution [broke smooth scrolling on a perfectly working Firefox](https://twitter.com/chrlsbr/status/1351284407794073606) (due to this long standing [bug](https://bugzilla.mozilla.org/show_bug.cgi?id=308064)). So I reworked the code. The CSS got more complex, but on the winning side you don't need to add `tabindex`es to your HTML any more.

_Thanks go out to [Matthias Ott](https://matthiasott.com/) and [Stefan Judis](https://www.stefanjudis.com/) for pushing me to publish this post ❤_

_The cover image of this post is [Graceful Ballet Dancer](https://www.shutterstock.com/de/image-photo/graceful-ballet-dancer-classic-ballerina-dancing-1412088299) by [Master1305](https://www.shutterstock.com/de/g/Master1305)_
