---
title: Effects for the Web!
description: How to use the new filter-properties for CSS3 and combine them with methods implemented by most modern browsers.
image: /img/Mike-Matas-One-Week-In-Japan.jpg
date: 2011-12-27
tags:
  - css
  - svg
layout: layouts/post.njk
---
In the late nineties CSS 2.1 brought us a basic set of good-enough tools to finally get table-free layouts en route. Then came CSS3 which started off by providing us with more creative tools to carve out the details. Today we finally have embeddeable fonts, rounded corners, gradients, semitransparent elements and colors, box and text shadows and so on and so forth.

Yet, comparing our toolset with that of an image editor like Photoshop we still discover a lot to desire. In Photoshop, for example, we have possibilities to desaturate parts of an image, or to sharpen or blur them. How might we need that for the web? Well, desaturating or blurring an area of a web page might be a means of directing your visitor’s attention to where you want it to be. Or it might help your visitor to concentrate better on an area, which you left untouched. Such a case might be magnifying pictures of a gallery:

![A cover-flow like image gallery](/img/Mike-Matas-One-Week-In-Japan.jpg)

A sharpen effect on the other hand might be useful when you scale down images in the browser, since without counter-measures you lose a lot of image details.

![Scaled down images, one with a sharpen filter](/img/sharpen.jpg)

That said, it would be nice if we had such effects and could apply them the same way we do apply opacity to tranparentize a whole area. Alas, we don’t.

## The status quo

Instead, to replicate a desaturation/grey scaling we are currently forced to cycle “by hand” through the color values of every affected element with JavaScript and set them to a corresponding grey value. When we meet (background-)images or videos it gets even more complicated: Then we need to switch to [HTML5 canvas to manipulate their color-values pixel by pixel](http://spyrestudios.com/html5-canvas-image-effects-black-white/) and swap out the original ones. But even if we get help from libraries like [CamanJS](http://camanjs.com/examples) or [Hoverizr](http://www.iliasiovis.com/hoverizr/), it remains a profound messy affair.

A blur effect can also be simulated, this time by using [text-shadows](http://webstandard.kulando.de/post/2011/12/09/css3-text-shadow-erzeugt-blur-effekt-tag-10-im-css3-adventskalender-2011) and/or [box-shadows](http://tympanus.net/codrops/2011/12/14/item-blur-effect-with-css3-and-jquery/) that use the same color/background-color as the to be blurred text/box. The thing is, the illusion doesn’t work with multicolored boxes and, as before, not with images or video. This requires again some HTML5 canvas action. Sigh.

![Item blur effect with CSS3 and jQuery](/img/Item-Blur-Effect-with-CSS3-and-jQuery-Google-Chrome_2011-12-18_11-37-58.png)

Speaking of HTML5 canvas: A very brutal approach to that blurring topic is being made by a library called [blurry.js](https://github.com/pmura/blurry.js), which extracts all the content, recreates it in a canvas (partially via Cufón), blurs it there and injects the whole thing back to where the original content was.

Finally, for sharpening elements there is no CSS-based trick at all, which leaves us alone with HTML5 Canvas.

## SVG to the rescue

Interestingly SVG knows a huge palette of so-called [filters](http://electricbeach.org/?p=950) since ages, e.g. color blending, brightness/contrast adjustments, lighting, displacement mapping, gaussian and motion blur, clouds, noise, sharpen et cetera.

Now, what we might try, since SVG is more and more widely supported, is to not put our content into HTML, but instead to put it into an SVG which we then inline or embed into out HTML “frame”. Inside that SVG we are then able to [apply any effect we like to our contents](\"http://dev.opera.com/articles/view/how-to-do-photoshop-like-effects-in-svg/\"). The drawback is that a HTMLer won’t be too keen on switching to SVG markup like the following:

```svg
<text font-family="Arial"
      font-weight="900"
      font-size="40"
      x="20" y="55%">SVG Example</text>
```

Something way cooler is an SVG thing called ForeignObject/xlink, [which allows us to embed foreign objects or areas of foreign markup inside an SVG](http://ajaxian.com/archives/foreignobject-hey-youve-got-html-in-my-svg). Think of it like inlining SVG into an HTML5 document, but the other way ’round. Once you have embedded your stuff, you can apply filters to it like you can for any other area of your SVG. [You might embed and filter a full HTML page from A to Z](http://dev.opera.com/articles/view/applying-color-tints-to-web-pages-with-s/), or you restrict yourself to [just embedding and filtering a single bitmap image](http://www.flother.com/examples/canvas-blur/v4/blur.svg). Of course, you’d need to put the holding SVG into an HTML page again, which makes up for some Inception-like braintwister (HTML with embedded SVG with embedded HTML). Browser support of the ForeignObject is [quite good](http://caniuse.com/#feat=svg-html). Remains left IE, who will follow suite in version 10 both with SVG filters and ForeignObject.

## SVG Filters in HTML via CSS

Firefox since version 3.5 goes even further. He allows you to reach out to a filter that resides inside an SVG from out your HTML document’s stylesheet and have that filter then applied to any HTML element. We could for example define a feGaussianBlur-filter in an SVG with a radius of 2px and assign it the id `gaussian_blur`:

```svg
<?xml version="1.0" standalone="no"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN"
"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
<svg width="1" height="1" version="1.1"
xmlns="http://www.w3.org/2000/svg"
xmlns:xlink="http://www.w3.org/1999/xlink">
  <defs>
    <filter id="gaussian_blur">
      <feGaussianBlur in="SourceGraphic" stdDeviation="2" />
    </filter>
  </defs>
</svg>
```

Then we could reference that filter by the SVG’s file name and the filter’s id in our styles and have it applied to every image:

```css
img {
  filter: url(blur.svg#gaussian_blur);
}
```

![Blurriness via SVG filter referenced from CSS ](/img/Blurriness-via-CSS-Filter-SVG-Mozilla-Firefox_2011-12-18_21-18-59.png)

You find a live-demo [here](http://www.der-schepp.de/code-files/blur.html). Alas, as I said, this just works with Firefox.

Thankfully this is not the end of the story. IE also knows filters, although not SVG-based, but instead based on early incarnations of the Windows graphics library DirectX. Amongst those filters are not only the well known alpha oder gradient filters that we use to fix bugs or to replicate CSS3 features. There are also filters that are quite similar to many of the interesting SVG filters:

```css
/* blur by 2 pixels */
filter: progid:DXImageTransform.Microsoft.Blur(pixelradius=2);
/* 13 pixel motion blur rotated to an angle of 310° */
filter: progid:DXImageTransform.Microsoft.MotionBlur(strength=13, direction=310);
/* grey scale / desaturate */
filter: gray;
/* Röntgen effect (inverted grey scale) */
filter: xray;
/* light cone */
filter: light();
/* emboss */
filter: progid:DXImageTransform.Microsoft.emboss();
```

You find a full list of those visual filters [here](http://msdn.microsoft.com/en-us/library/ms673539(v=VS.85).aspx).

So, with the help of conditional comments we can now already serve certain effects to two browser families, which together make up a considerable amount of the browser market:

```html
<!DOCTYPE HTML>
<!--[if lte IE 9]> <html class="ie" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="en"> <!--<![endif]-->
<head>
<meta charset="utf-8">
<title>Blur via CSS</title>
<style>
img {
  filter: url(blur.svg#gaussian_blur);
}
.ie img {
  margin: -2px;
  filter: progid:DXImageTransform.Microsoft.blur(pixelradius=2);
  zoom: 1;
}
</style>
</head>
<body>
  <img src="stadt.jpg"
   alt="Some rights reserved by zigazou76"
   width="500" height="333">
</body>
</html>
```

`margin: -2px` counters the increased dimensions of the filtered element in IE. `zoom: 1` is needed for most filters to work in IE6/7. Sad fact: IE10 won’t support filters anymore :(

[Here you find the extended example](http://www.der-schepp.de/code-files/blur2.html)

As people liked Firefox’s idea to open up SVG filters for use with HTML, those people formed a new task force in the W3C called W3C FX Task Force whose purpose it got to standardize and even simplify usage of SVG filters for use in all browsers. Because Firefox already pioneered filter effects the first draft didn’t take too long to appear under the label of [W3C Filter Effects 1.0](https://dvcs.w3.org/hg/FXTF/raw-file/tip/filters/index.html). The mechanics proposed in that draft work almost like in Firefox, but with the addition of offering shortcuts to some of the most interesting filters. Filters that have a shortcut defined work without the help of an external SVG. They are sort of permanently hard wired inside browser engine. Those are the filters with shortcuts:

*   grayscale
*   sepia
*   saturate
*   hue-rotate
*   invert
*   opacity
*   brightness
*   contrast
*   blur
*   drop-shadow

Another advantage of the shortcut filters is that they are animateable via CSS transitions or animations:

```css
.foo {
  filter: blur(2px);
  transition: filter 1s ease-in-out;
}

.foo:hover {
  filter: blur(0);
}
```

This would be much harder and less efficient to do within an SVG.

Finally the filter effects are planned to be extended with [OpenGL/WebGL vertex and fragment shaders](http://www.adobe.com/devnet/html5/articles/css-shaders.html). Vertex shaders will let you span a 2D mesh over an element and then let you distort the object by moving all the crossing points of the mesh following a mathematical formula of your choice. Fragment shaders on the other hand will allow you to do such mathematical processing on the colors of every pixel of that element. And all of it, on top, hardware accelerated on your graphics card.

![Adobe's cinematic effects for the web](/img/CSS-shaders-Cinematic-effects-for-the-web-Adobe-Developer-Connection-Google_2011-12-18_19-31-01.png)

Conveniently last week saw the first implementation of those filters in the [WebKit-Nightlies](http://nightly.webkit.org/), from were they quickly spawned over to [Chrome Canary](http://tools.google.com/dlpage/chromesxs). That means that within a 3 months timeframe CSS filter effects will be available in your everyday Chrome browser and probably not too much later also in Safari. And this means that we are not too far away from being able to serve some flavor of CSS filters to 90% – 95% of all browsers on the market!

Returning to our code example, all that we need to add for WebKit now is a simple `-webkit-filter: blur(2px);` which for once we need to put _after_ the unprefixed `filter`-property. The reason is that once WebKit will support an unprefixed `filter`-property that Firefox-specific syntax in the last line would kill our CSS transition, which we do apply in order to fade nicely from blurred to unblurred and back via `-webkit-transition: -webkit-filter 1s ease-in-out`:

```html
<!DOCTYPE HTML>
<!--[if lte IE 9]> <html class="ie"> <![endif]-->
<!--[if gt IE 9]><!--> <html> <!--<![endif]-->
<head>
<meta charset="utf-8">
<title>Blur via CSS</title>
<style>
img {
  filter: url(blur.svg#gaussian_blur);
  -webkit-filter: blur(2px);
  -webkit-transition: -webkit-filter 1s ease-in-out;
}
img:hover {
  filter: none;
  -webkit-filter: blur(0);
}
.ie img {
  margin: -2px;
  filter: progid:DXImageTransform.Microsoft.blur(pixelradius=2);
  zoom: 1;
}
.ie img:hover {
  margin: 0;
  filter: none;
}
</style>
</head>
<body>
  <img src="stadt.jpg"
   alt="Some rights reserved by zigazou76"
   width="500" height="333">
</body>
</html>
```

[And here is the final example](http://www.der-schepp.de/code-files/blur3.html).

And that’s it, filter effects for everyone! Let 2012 be the year of the CSS filters and [have fun playing with’em](http://davidwalsh.name/dw-content/css-filters.php).

_This blog post was originally published at [drublic.de/blog/effects-for-the-web/](http://drublic.de/blog/effects-for-the-web/)_
