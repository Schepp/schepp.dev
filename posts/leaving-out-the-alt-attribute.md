---
title: Leaving out the `alt` Attribute
description: If there is one thing everybody knows about accessibility, then it is the fact that you should always have an `alt` attribute on your <img> element. And that leaving it out also results in a HTML validation error. Interestingly, the latter is only partially true.
image: https://schepp.dev/img/braille.jpg
date: 2021-01-16
tags:
  - html
  - a11y
layout: layouts/post.njk
---
![An annotated Braille alphabet (A-Z) on a dark blue background](/img/braille.jpg)

As it seems I'm currently looking at my Twitter timeline pretty often. Because this time it was [a tweet](https://twitter.com/simevidas/status/1350239918031855618) from [Šime Vidas](https://webplatform.news/) that caught my attention:

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">Do I need alt text if there’s a visible image description?<br><br>&lt;figure&gt;<br> &lt;img src=&quot;x.jpg&quot; width=&quot;900&quot; height=&quot;600&quot; alt=&quot;&quot;&gt;<br> &lt;figcaption&gt;Caption serves as alt text&lt;/figcaption&gt;<br>&lt;/figure&gt;</p>&mdash; Šime Vidas (@simevidas) <a href="https://twitter.com/simevidas/status/1350239918031855618?ref_src=twsrc%5Etfw">January 16, 2021</a></blockquote> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

<noscript>
    <img src="/img/twitter-sime-vidas-alt-attribute.png" alt="Screenshot of Šime Vidas' tweet">
</noscript>

Basically, what Šime is asking is if `alt` could not be left out if the corresponding image is being described through other technical means, i.e. a `<figcaption>`.

As most commentators correctly answered: you cannot leave it out. This is due to the different use cases between `alt` and `<figcaption>`:

* `alt` is literally meant as an **alt**ernative representation of the visual "data". Therefore it should be a textual reproduction of the image's appearance. It would describe its format, colors, what shapes and things can be spotted and where, as well as text that is visible and in what type of font it is presented. Imagine one of those [new AIs which is able to draw an image from a text you provide](https://www.dpreview.com/news/2488474679/researchers-teach-an-ai-to-generate-logical-images-based-on-text-captions). Ideally your text is written in such a way that the image generated by the AI closely matches the original one.

* `<figcaption>` on the other hand is meant as an accompanying **caption or legend**. Similarly to [a `<table>`'s `<caption>` element](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/caption), it could provide a headline-like description of the given `<figure>`. For example: "Usage of jQuery vs. React in %". Or in the case of a photograph, it could offer additional info like artist and copyright.

So we need both, as `alt` and `<figcaption>` are not interchangeable.

One more thing to spot in Šime's code example is that he leaves the `alt` attribute an empty string. This is only allowed when the corresponding image [doesn't convey any information for the user, or is not meant for the user to see at all](https://www.w3.org/TR/2011/WD-html5-author-20110809/the-img-element.html#an-image-not-intended-for-the-user):

> Generally authors should avoid using img elements for purposes other than showing images.
 If an img element is being used for purposes other than showing an image, e.g. as part of a service to count page views, then the alt attribute must be the empty string.
 In such cases, the width and height attributes should both be set to zero.

Would the above be true, then the image would never be located inside a ´<figure>` element to begin with.

So in the end, does it all boil down to the fact we already know, which is that the `alt` attribute is a mandatory things? Not quite!

## Comes in the HTML Spec

When you write your HTML the way Šime was suggesting 👇

```html
<figure>
  <img src="./image.jpg">
  <figcaption>Description</figcaption>
</figure>
```

you will find that it will surprisingly be [perfectly valid HTML](https://html5.validator.nu/?doc=http%3A%2F%2Fschepp.github.io%2Fimagery-on-the-web%2Fdemos%2Ffigcaption.html&showsource=yes)! The same holds true if the image has a `title` attribut instead of `alt`:

```html
<img src="./image.jpg" title="Description">
```

This again is [perfectly valid HTML](https://html5.validator.nu/?doc=http%3A%2F%2Fschepp.github.io%2Fimagery-on-the-web%2Fdemos%2Ftitle.html&showsource=yes)! Weird. But why? Is this a glitch in the validator? Well, not at all. The reason for allowing these constellations is explained in the [HTML Spec](https://www.w3.org/TR/2011/WD-html5-author-20110809/the-img-element.html#unknown-images):

> In some unfortunate cases, there might be no alternative text available at all, either because the image is obtained in some automated fashion without any associated alternative text (e.g. a Webcam), or because the page is being generated by a script using user-provided images where the user did not provide suitable or usable alternative text (e.g. photograph sharing sites), or because the author does not himself know what the images represent (e.g. a blind photographer sharing an image on his blog).

So basically when an image *is* meant for the user to be seen and it *does* convey information, but when at the same time it is technically impossible to offer, or generate, a textual alternative describing what can be seen, *then* it is allowed to leave away `alt` altogether, as an empty `alt=""` would mark the image as purely presentational - which it is not. One example mentioned above is a live webcam feed, another one would be a CAPTCHA. In both cases it is impossible at the time of authoring the HTML to say what later will be shown in the image. At the same time both types of images are important for the user and nothing to skip over. So dropping the `alt` attribute in these cases makes sense.

So in the end things turn out again more nuanced than we initially thought (and maybe hoped for). But since omitting the `alt` attribute in the described cases is optional whereas all the other cases absolutely require it to be present, the easiest strategy is to keep sticking them to every image element, and you are good.

_Please have a look and subscribe to Šime's [Web Platform News](https://webplatform.news/) ❤_

_The cover image of this post is [Braille](https://www.shutterstock.com/de/image-illustration/braille-visually-impaired-writing-system-symbol-1858649119) by [80's Child](https://www.shutterstock.com/de/g/80s+Child)_