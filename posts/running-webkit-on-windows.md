---
title: Running WebKit on Windows
description: How to test your site on WebKit (Safari) without buying a Mac.
image: https://schepp.dev/img/webkit.png
date: 2021-01-21
tags:
  - devtools
layout: layouts/post.njk
---
Safari 5.1, back in 2010, was the last WebKit browser that somebody released for the Windows platform. Since then debugging things in WebKit came down to either buying a whole Mac or using a remote Safari in [Browserstack](https://www.browserstack.com/).

Funnily enough, the WebKit team kept pumping out nightly builds for Windows together with those for the other platforms. It's just that Apple didn't want to invest anymore time in providing a browser UI around it. But it is still perfectly possible to get WebKit on Windows to start, with almost all the engine features of the newest Safari.

There are two ways to do so: a manual and an automated one.

Alexander Skachkov was kind enough to describe all required steps of the manual way [in a blog post three years ago](https://medium.com/@alSkachkov/how-to-load-the-latest-webkit-on-windows-962a9219c1e1). The only update from my side would be to not install Cygwin and Apple iTunes, as both are monsters, but instead to download the so-called "WebkitForWindows [WebKitRequirements](https://github.com/WebKitForWindows/WebKitRequirements/releases)" as ZIP and to put the content of their `bin64` folder into the one with the same name of your WebKit-Cairo folder. Afterwards you run `MiniBrowser.exe` and should be greeted with a spartan browser window.

But you probably don't wanna jump through the hoops of manual installation, especially not each and everytime you need to upgrade WebKit. The automated way is to use "[Playwright](https://playwright.dev/)". Playwright is basically what Puppeteer is for Chrome, but for Chrome, Firefox and Safari at the same time. All you need to get going is [npm](https://www.npmjs.com/get-npm). Then you run it like this:

```
npx playwright wk https://webkit.org/
```

The `wk` parameter indicating that you wanna use WebKit. Then you are greeted with this window:

![A pretty spartan WebKit window, running on Windows, showing the WebKit project page](/img/playwright-webkit-on-windows.jpg)

Giving it a spin on [html5test.com](https://html5test.com) tells me that it trails Safari by quite a bit (492 vs 416 points). But the areas affected are basically due to proprietary things or the missing browser chrome and OS integration around it:

* color input
* fullscreen support
* Web Notifications
* gamepad support
* Pointer Lock API
* Web Audio API
* Speech Recognition
* Speech Synthesis
* video and audio codecs
* video streaming
* video DRM
* JPEG image **export**
* WebRTC
* Webcam support
* Web Payments API
* Objectstore blob support
* Web SQL

Looking though the list, only the missing video codec support is a bummer. The rest it pretty much irrelevant for the average site.

Have fun!
