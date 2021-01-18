---
title: Gradient Multiline Padded Text
description: How to achieve multiline padded text with a gradient as background in CSS.
image: /img/gradient-multiline-padded-text.png
date: 2021-01-18
tags:
  - css
layout: layouts/post.njk
---
![Example for a gradient multiline padded text with white text on a blue gradient background](/img/gradient-multiline-padded-text.png)

For whatever reason a question from Dan Mall from 2018 popped up in my timeline where he was asking how to create multiline padded text with a consistent gradient background in CSS:

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">CSS superfriends! Have you seen examples of how to do multi-line padded text like this article on <a href="https://twitter.com/css?ref_src=twsrc%5Etfw">@css</a> (<a href="https://t.co/2j8p4jmaT4">https://t.co/2j8p4jmaT4</a>), but with a gradient that doesn&#39;t reset for each line? <a href="https://t.co/MVPdAjxt1W">pic.twitter.com/MVPdAjxt1W</a></p>&mdash; Dan Mall (@danmall) <a href="https://twitter.com/danmall/status/1069729595384049665?ref_src=twsrc%5Etfw">December 3, 2018</a></blockquote> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

<noscript>
    <img src="/img/twitter-dan-mall-gradient-multiline-padded text" alt="Screenshot of Dan Mall's tweet">
</noscript>

Here is my take:

<p class="codepen" data-height="265" data-theme-id="light" data-default-tab="result" data-user="Schepp" data-slug-hash="zYKyXPq" style="height: 265px; box-sizing: border-box; display: flex; align-items: center; justify-content: center; border: 2px solid; margin: 1em 0; padding: 1em;" data-pen-title="Gradient Multiline Padded Text">
  <span>See the Pen <a href="https://codepen.io/Schepp/pen/zYKyXPq">
  Gradient Multiline Padded Text</a> by Christian Schaefer (<a href="https://codepen.io/Schepp">@Schepp</a>)
  on <a href="https://codepen.io">CodePen</a>.</span>
</p>
<script async src="https://cpwebassets.codepen.io/assets/embed/ei.js"></script>

[Link to Codepen](https://codepen.io/Schepp/pen/WNbQByE?editors=1100)

The trick is to set `background-attachment` to `fixed`, so that it uses the viewport instead of the element (or rather inline fragments) as its reference. That keeps the background from resetting in each line and also from starting at different origins for each line.

The second relevant setting is `box-decoration-break: clone`, which repeats the padding on every line, instead of applying `padding-left` only in the first and `padding-right` only in the last line.

Try commenting out both properties in the Codepen to see the differences.

**Important caveat:** The background will only spread as far as the size of the viewport. If you need to style text in such a way, but below the fold, you're out of luck. So this only works well with the main headline.
