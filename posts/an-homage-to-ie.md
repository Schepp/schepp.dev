---
title: The Trident Era Ends - an Homage to IE
description: 
image: /img/natalya-letunova-gF8aHM445P4-unsplash.jpg
date: 2019-12-28
tags:
  - ecosystem
layout: layouts/post.njk
---
![View from within the ruins of the Monument House of he Bulgarian Communist Party, built on Buzludzha Peak in central Bulgaria. Photo by Natalya Letunova on Unsplash](/img/natalya-letunova-gF8aHM445P4-unsplash.jpg)

When I was a child, I was always fascinated by stories about ancient civilizations. I literally devoured books about Atlantis, or the story of [Heinrich Schliemann](https://en.wikipedia.org/wiki/Heinrich_Schliemann)'s discovery of Troy, stories about the Greek, the Romans, the [Inca Empire](https://en.wikipedia.org/wiki/Inca_Empire) or [Ancient Egypt](https://en.wikipedia.org/wiki/Ancient_Egypt). And I was always fascinated by the extend of their capabilities in the fields of [astronomy](https://blogs.scientificamerican.com/observations/the-astronomical-genius-of-the-inca/), [math](https://en.wikipedia.org/wiki/Pythagoras) and [medicine](https://en.wikipedia.org/wiki/Ancient_Egyptian_medicine), their incredible achievements, like building those vast monuments, or their highly functional social systems. What's even more incredible is that most of this already happened way before Jesus Christ first set foot on our Earth! 

And yet, all these eras of highly developed civilizations on day came to an end. Some just died out quietly, some were outpaced by civilizations with better military capabilities. What then usually happened was that the capabilities of the defeated did not transition over to the now dominating group enriching their pool, but instead vanished.

## The Era of the Trident Engine

Starting in January 2020, Microsoft will roll out their new Chromium-based Edge browser to their millions of Windows 10 users. This will also mark the end of an era: the era of the Trident-Engine. When Microsoft created the Edge browser in 2015, what they really did was to fork Trident and to strip out plenty of legacy code paths like [ActiveX](https://en.wikipedia.org/wiki/ActiveX) (Microsoft's version of Java Applets) or emulation of older IE rendering engines. That both browsers still share most of their code get's apparent when you read posts like [these](https://blogs.windows.com/msedgedev/2017/04/19/modernizing-dom-tree-microsoft-edge/) on the Edge Blog or when you see bug reports that [similarly affect IE 11 as well as Edge 17](https://phabricator.wikimedia.org/T203564). [Most of the initial improvements came from Chakra](https://www.anandtech.com/show/8932/internet-explorer-project-spartan-shows-large-performance-gains), the JavaScript engine, and only a moderate few [from the rendering engine itself](http://html5test.com/compare/browser/ie-11/edge-12.html). Renaming the browser could be considered more of a marketing move, though, as the removal of features already started earlier when it was still called Internet Explorer.

Nowadays, when we get excited about the web platform, it is not because of a new Edge release but because of Google unveiling new ideas and APIs during Google I/O or the Chrome Dev Summit. A lot of these innovations are driven by other teams at Google working on Google frameworks like Angular and AMP, or on Google products like Gmail, Search, Drive, Maps, Google Docs, Analytics or in recent times: Lighthouse. In fact, a lot of what defines HTML5 can be rooted back to Google looking for a way to improve the web platform to better accommodate its ideas around web apps. Remember [Google Gears](https://en.wikipedia.org/wiki/Gears_(software))? Or later [Google Chrome Frame](https://en.wikipedia.org/wiki/Google_Chrome_Frame)? And you know what? That same kind of process also drove innovation in Internet Explorer back in the days. ActiveX capability was added to Internet Explorer 3.0, together with the `<object>` tag, to offer one more "compile target" for Microsoft's Java competitor. It was certainly not the IE team that came up with this idea. Or take what we know today as "AJAX": the idea of lazily fetching content in the background via JavaScript was born in the Exchange / Outlook Web Access team, a product that could be seen as a precursor to Gmail. [After pulling a few tricks inside Microsoft](https://web.archive.org/web/20060617163047/http://www.alexhopmann.com/xmlhttp.htm) they got it (silently) shipped with Internet Explorer 5.0 in 1999. It wasn't until 6 years later that [the term AJAX was coined](https://web.archive.org/web/20050222032831/http://adaptivepath.com/publications/essays/archives/000385.php) and its concepts widely known. 

> the real explanation of where the name XMLHTTP comes from- the thing is mostly about HTTP and doesn't have any specific tie to XML other than that was the easiest excuse for shipping it so I needed to cram XML into the name

Same goes for the [Drag-n-Drop API](https://www.quirksmode.org/blog/archives/2009/09/the_html5_drag.html) or [MHTML](https://en.wikipedia.org/wiki/MHTML). Back in the days, Microsoft was single-handedly pushing the web forward, [with around 1.000(!) people working on Internet Explorer with a 100 million Dollar budget to burn per year](https://en.wikipedia.org/wiki/Internet_Explorer_version_history#Microsoft_Internet_Explorer_5), with almost no-one left to compete. This was massive. 

Innovations in Internet Explorer being driven by other business units persisted until even 2012. At that time Windows 8 introduced the Windows Store and corresponding (universal) Windows Store Apps. Those apps could be written once and could then be run on Windows, Xbox and Windows Phone. Since Microsoft was late to the app store game, they had put the barrier for developing apps as low as possible, so they got the idea of allowing people to develop apps with web technologies. As a communication path to the underlying OS, they created a JavaScript library called "[WinJS](https://en.wikipedia.org/wiki/WinJS)" and Internet Explorer 10 was meant to be the runtime environment for those apps.

![Metro Design - Microsoft, Public Domain](/img/metro.png)

But to be able to model the Windows UI with web technologies, Microsoft had to add plenty of new capabilities to IE: CSS Grid, CSS Flexbox, CSS Scroll Snap Points and the Pointer Events API for touch and stylus interactions (the latter one was required as [Apple had filed a patent on the Touch API](https://books.google.de/books?id=vb4v9HNwWVgC&pg=PA569&lpg=PA569&dq=internet+explorer+pointer+events+patent&source=bl&ots=dlEPaUbP6_&sig=ACfU3U2I08YKVq1fPg5RTHcGC169SyOrEQ&hl=en&sa=X&ved=2ahUKEwj5l4zggtvmAhVPyqQKHS0dACUQ6AEwAXoECAoQAQ#v=onepage&q=internet%20explorer%20pointer%20events%20patent&f=false)).

<div class="video"><iframe src="https://channel9.msdn.com/Events/Build/2012/3-114R/player" width="960" height="540" allowFullScreen frameBorder="0" title="HTML5 & CSS3 latest features in action! (Repeat) - Microsoft Channel 9 Video"></iframe></div>

Coming back to my introductory part on ancient civilizations: For me it feels like Internet Explorer already had many of the things that we came to reinvent later and that we now celebrate as innovations. Although it goes without saying that our modern reinventions offer more features combined with a better developer experience, I came to wonder why we, as a community, only picked up very few of them. Most of the things mentioned above got picked up - for variing reasons. The following ones were not:

## The capabilities that got lost

### MHTML

[MHTML](https://en.wikipedia.org/wiki/MHTML) or "MIME encapsulation of aggregate HTML documents" was meant as a packaging format. It shares a lot of concepts with how email clients append attachments to an email. MHTML would take an HTML file and inline all of its resources like CSS, JavaScript files or images via base64 into extra sections. So it is basically data URIs on steroids. You could also see MHTML as the precursor of [Web Bundles](https://web.dev/web-bundles/). The format was supported from IE 5.0 onwards, as well as in Presto-based Opera. It was also supported by Outlook Express, where MHTML was hidden inside `.eml` email message files (do you remember these?). No other brother officially supported MHTML, but Chromium added the feature later behind a flag called `chrome://flags/#save-page-as-mhtml`. MHTML was proposed as an open standard but never took off.

### Page Transition Filters

Internet Explorer also had page transition filters which you would define as HTTP header or in form of a meta tag:

```html
<meta http-equiv="Page-Enter" 
       content="RevealTrans(Duration=0.600, Transition=6)">
```  

There was an extensive list of transition filters you could choose from by referencing them via number:

* 0 - Box in
* 1 - Box out
* 2 - Circle in
* 3 - Circle out
* 4 - Wipe up
* 5 - Wipe down
* 6 - Wipe right
* 7 - Wipe left
* 8 - Vertical blinds
* 9 - Horizontal blinds
* 10 - Checkerboard across
* 11 - Checkerboard down
* 12 - Random dissolve
* 13 - Split vertical in
* 14 - Split vertical out
* 15 - Split horizontal in
* 16 - Split horizontal out
* 17 - Strips left down
* 18 - Strips left up
* 19 - Strips right down
* 20 - Strips right up
* 21 - Random bars horizontal
* 22 - Random bars vertical
* 23 - Any random pattern 0-22

In addition to `Page-Enter` you could specify additional transitions for `Page-Exit`, `Site-Enter` and `Site-Exit`. Those soft transitions between pages is something that we see reappearing in the form of [Portals](https://web.dev/hands-on-portals/).

### Object Transition Filters

Similarly to how you could use filters to transition between pages, you could also transition between two states of the same DOM object. This is similar to Rich Harris' [ramjet](https://github.com/Rich-Harris/ramjet), only that it would not morph between two states, but instead blend over like in the movies. 

![ramjet allows you to smoothly morph between two objects](https://cloud.githubusercontent.com/assets/1162160/7279487/5d668dea-e8ea-11e4-9b0d-a9ba2f1165cc.gif)

What you could also do with those object transition filters is something similar to CSS Transitions or to an [animated CSS crossfade() function](https://schepp.github.io/imagery-on-the-web/#/4/13).

You would start off with applying a blend filter to the element (with a duration of 600ms):

```js
img.style.filter = 'blendTrans(duration=0.600)';
```

Then, before making any change to the object, you would freeze its current state:

```js
img.filters.blendTrans.apply();
````

Finally you would change the image source and trigger the transition:

```js
img.src = 'different-src.jpg';
img.filters.blendTrans.play();
```

<video width="1208" height="842" autoplay muted loop>
  <source src="/img/ie-object-transition-filter.mp4" type="video/mp4">
</video>

_The cover photo of this post is shot from within the ruins of the [Monument House of he Bulgarian Communist Party, built on Buzludzha Peak in central Bulgaria](https://en.wikipedia.org/wiki/Buzludzha) by [Natalya Letunova on Unsplash](https://unsplash.com/photos/gF8aHM445P4)_
