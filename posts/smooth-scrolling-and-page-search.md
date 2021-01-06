---
title: Fixing Smooth Scrolling & Page Search
description: Native smooth scrolling is one more example of standards paving the cow path by declaring a wide-spread practice officially a thing&#58; being able to smoothly scroll the viewport to another part of a page without the user losing their orientation. As good at it is, though, it also has an undesired side effect on the browser's built-in page search. This posts shows what the problem is and how to solve it.
image: https://schepp.dev/img/ballet.jpg
date: 2020-01-06
tags:
  - css
layout: layouts/post.njk
---
![A ballet dancer](/img/ballet-resized.jpg)

Yesterday, as I was browsing my Twitter timeline, a tweet from Chris Coyier popped up, in which he mentioned feedback he got for his newest redesign of [CSS Tricks](https://css-tricks.com/). It went as follows:

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">Anecdotal thing: when I had this on <a href="https://twitter.com/css?ref_src=twsrc%5Etfw">@CSS</a>, I had SO MANY reports of people annoyed that when they did &quot;find on page&quot; and ⬆️⬇️ through the results, the smooth scrolling was slow and annoying. Unfortunately, you can&#39;t control the speed or when it happens. <a href="https://t.co/HAio46bYQt">https://t.co/HAio46bYQt</a></p>&mdash; Chris Coyier (@chriscoyier) <a href="https://twitter.com/chriscoyier/status/1346513455516426242?ref_src=twsrc%5Etfw">January 5, 2021</a></blockquote> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

<noscript>
    <img src="/img/twitter-chris-coyer-smooth-scrolling-page-search.png" alt="Screenshot of Chris Coyier's tweet">
</noscript>

Apparently Chris had once switched out JavaScript-based smooth anchor scrolling in favor of a more modern, purely CSS-based solution. All you need to do is slap a `scroll-behavior: smooth` on `html` and all of a sudden scrolls to different places on the page happen smoothly. And that's great!

## Page Search

Sadly, as Chris points out in his tweet, native smooth scrolling it negatively affects the UX of page search when cycling through its search results:

<video width="300" height="520" autoplay muted loop>
  <source src="/img/smooth-scroll-page-search.mp4" type="video/mp4">
</video>

Having the browser scroll to every single result is very time consuming. So for the page search it would be desirable for the browser to make an exception and to deactivate smooth scrolling.

Until then here is a trick how to solve the problem with a little nit of extra CSS and HTML.

## The Solution

First you need to modify your assignment of `scroll-behavior` by replacing `html` with `html:focus-within`:

```css
html:focus-within {
  scroll-behavior: smooth;
}
```

Now everytime the user interacts with the surrounding browser interface, as is the case with the page search, smooth scrolling will be disabled and jumping to the results will be instantaneous.

A new problem arising now is that anchor links (`<a href="#chapter2">`) also lose their smooth scrolling ability. This happens because most of our link targets are not focusable. Usually link targets are `<div>`s or headlines with an `id` slapped on them. And those types of HTML elements traditionally cannot receive focus. Therefore, once the user clicks on such a link the focus gets lost and `html:focus-with` doesn't hold true for our document any longer. So to solve this problem, you need to teach your anchor targets to receive focus by adding a `tabindex="-1"`.

_Thanks go out to [Matthias Ott](https://matthiasott.com/) and [Stefan Judis](https://www.stefanjudis.com/) for pushing me to publish this post ❤_

_The cover image of this post is [Graceful Ballet Dancer](https://www.shutterstock.com/de/image-photo/graceful-ballet-dancer-classic-ballerina-dancing-1412088299) by [Master1305](https://www.shutterstock.com/de/g/Master1305)_
