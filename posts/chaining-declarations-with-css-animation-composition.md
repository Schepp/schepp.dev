---
title: Chaining Declarations via Animation Composition
description: How to chain CSS declarations non-destructively
image: /img/rainbow-ponton.jpeg
date: 2023-06-11
tags:
  - css
layout: layouts/post.njk
---
I just returned from this year's [CSS Day](https://cssday.nl/) in Amsterdam, and it was fantastic! Year after year, PPK and Krijn organize one of the best conferences you can wish for: The program is mind-blowing with the best of speakers coming from all over the world, and at the same time they cater so well for everyone attending <3. This is also true for their other conference series: [Performance.now()](https://perfnow.nl/). If you haven't been to any of their conferences, maybe it's about time!

What also happened was that Nils and I ran the second installment of an [in-person CSS Café meetup](https://www.meetup.com/css-cafe/events/292044743/) as a side event the day after CSS Day. It's a place for people to relax and come down after three intense days, with a few high-quality talks sprinkled in to help against going cold turkey on CSS. And what a lovely day that was!

<script async src="https://cpwebassets.codepen.io/assets/embed/ei.js"></script>

## Individual Transforms: the Good, the Bad, and the Ugly

This was the title of the first talk given by [Amit Sheen](https://amitsh.com/). His talk was about the [individual transforms properties](https://web.dev/css-individual-transform-properties/) that got recently added to the web platform, showing us what kinds of problems they help solve, but also pointing out where they do have their shortcomings.

### The Good

Individual transforms, which have landed in all browsers, allow us to manipulate the different kinds of transforms individually, allowing us to layer them and keep our CSS code more DRY.

So instead of writing such repetitive code:

```css
.element {
  transform: translateY(50px);
}

.element:hover {
  /* translateY needs to be repeated, 
  otherwise, it's going to be lost */
  transform: translateY(50px) rotate(-45deg);
}
```

we can now do this:

```css
.element {
  translate: 0 50px;
}

.element:hover {
  rotate: -45deg;
}
```

No need to carry over the translate when all we want is to change the rotation.

With animations, it's even more useful when you need each transform to change at a different pace:

```css
@keyframes anim {
  0% { transform: translateX(0%) }
  5% { transform: translateX(5%) rotate(90deg) scale(1.2) }
  10% { transform: translateX(10%) rotate(180deg) scale(1.2)}
  90% { transform: translateX(90%) rotate(180deg) scale(1.2)}
  95% { transform: translateX(95%) rotate(270deg) scale(1.2)}
  100% { transform: translateX(100%) rotate(360deg) }
}
```

With individual transforms, this gets a lot DRYer - and easier, too:

```css
@keyframes anim {
  0% { translate: 0 0 }
  100% { translate: 100% 0 }

  0%, 100% { scale: 1 }
  5%, 95% { scale: 1.2 }

  0% { rotate: 0deg }
  10%, 90% { rotate: 180deg }
  100% { rotate: 360deg }
}
```

That way you can also layer multiple keyframe animations on top of one another, each animating a different individual property:

```css
@keyframes move {
  0% { translate: 0% 0; }
  100% { translate: 100% 0; }
}

@keyframes scale {
  0%, 100% { scale: 1; }
  5%, 95% { scale: 1.2; }
}

@keyframes rotate {
  0% { rotate: 0deg; }
  10%, 90% { rotate: 180deg; }
  100% { rotate: 360deg; }
}

.target {
  animation: move 2s, scale 2s, rotate 2s;
  animation-fill-mode: forwards;
}
```

### The Bad

The big caveat of individual transforms is that the order in which they get processed and applied is predetermined: 

* `translate` always comes first, 
* followed by `rotate`, 
* then `scale` 
* and finally the classic `transform`. 

That can be a problem as the order in which transforms get applied can lead to different visual outcomes.

Take the example from the beginning, but let's flip things around this time and have us add a `translate` to an existing `rotate`:

```css
.element {
  transform: rotate(-45deg);
}

.element:hover {
  transform: rotate(-45deg) translateY(50px);
}
```

While it's possible to do the following, the visual outcome will not be the same anymore:

```css
.element {
  rotate: -45deg;
}

.element:hover {
  translate: 0 50px;
}
```

The reason is that an individual rotate will always be applied *after* an individual translate. So in the `:hover`-state, instead of the element first being rotated and then moved along the rotation axis, the element will first be moved and only then rotated.

<p class="codepen" data-height="500" data-default-tab="css,result" data-slug-hash="BaGNwGB" data-user="Schepp" style="height: 500px; box-sizing: border-box; display: flex; align-items: center; justify-content: center; border: 2px solid; margin: 1em 0; padding: 1em;">
  <span>See the Pen <a href="https://codepen.io/Schepp/pen/BaGNwGB">
  Untitled</a> by Christian Schaefer (<a href="https://codepen.io/Schepp">@Schepp</a>)
  on <a href="https://codepen.io">CodePen</a>.</span>
</p>

### Solutions?

One thing I was very surprised to learn from Amit is that when you set individual transforms *and* the classic transform at the same time the latter one would not clear out the individual declarations but queue itself up after them. So a solution can be to put our translate into the classic transform as it will then come in last:

```css
.element {
  rotate: -45deg;
}

.element:hover {
  transform: translateX(50px);
}
```

<p class="codepen" data-height="500" data-default-tab="css,result" data-slug-hash="GRwJMwa" data-user="Schepp" style="height: 500px; box-sizing: border-box; display: flex; align-items: center; justify-content: center; border: 2px solid; margin: 1em 0; padding: 1em;">
  <span>See the Pen <a href="https://codepen.io/Schepp/pen/GRwJMwa">
  Individual Transforms Caveat</a> by Christian Schaefer (<a href="https://codepen.io/Schepp">@Schepp</a>)
  on <a href="https://codepen.io">CodePen</a>.</span>
</p>

Amit's suggestion was another one, though. He made the case for using Custom Properties, like so:

```css
.element {
  transform: rotate(var(--rotate, -45deg) 
             translate(var(--translate, 0));
}

.element:hover {
  --translate: 0 50px;
}
```

While I think, this works well for the given example, where it falls short is when you want to add transforms to elements that have *not* been set up with Custom Properties. This could be a component from another team or a third party. And it also only works for the amount + type + order of transforms that the element has been set up with.

### Enters Animation Composition!

An unorthodox way of solving this is by making use of, or rather misuse, [CSS Animation Composition](https://developer.chrome.com/articles/css-animation-composition/) to chain transforms after existing ones. [`animation-composition`](https://developer.mozilla.org/en-US/docs/Web/CSS/animation-composition) is a new animation property, that allows you to determine if the effects of an animation should wipe out what may already be defined on an element or if its effects get added on top of what is already there. 

What you can do now is define an animation, which carries your additional transforms, which is paused and composited on top of the existing values. This is how this would look like, applied to our example:

```css
@keyframes translate {
  0% { transform: translateY(50px) }
}

.element2 {
  transform: rotate(-45deg);
}

.element2:hover {
  animation: translate 1ms paused;
  animation-composition: add;
}
```

<p class="codepen" data-height="500" data-default-tab="css,result" data-slug-hash="KKrpXYN" data-user="Schepp" style="height: 500px; box-sizing: border-box; display: flex; align-items: center; justify-content: center; border: 2px solid; margin: 1em 0; padding: 1em;">
  <span>See the Pen <a href="https://codepen.io/Schepp/pen/KKrpXYN">
  Combining individual and classic transforms</a> by Christian Schaefer (<a href="https://codepen.io/Schepp">@Schepp</a>)
  on <a href="https://codepen.io">CodePen</a>.</span>
</p>

Animation Composition can also fill other gaps in CSS, for example when you want to add a background image via shorthand to an element while preserving any background color that might already be assigned to it. 

> We often run into situations where background-color has already been set for an element or component, and in a separate class we want to also add a background image to it.  
> 
> In order to do this without overriding the color, we have to use the background-image property specifically.  
>   
> Then we need to size, position, no-repeat it and that's 3 more longhand properties.  
>   
> Add two bg images and we're managing parallel arrays in every longhand property above to avoid overriding the background-color set elsewhere.  
> 
> It gets pretty tedious very quickly.

[Jane Ori at the W3C CSS Working Group Issues](https://github.com/w3c/csswg-drafts/issues/8726)

This is what a solution via Animation Composition might look like:

```css
.element {
  background-color: yellow;
}

@keyframes background {
  0% {
    background: no-repeat center/cover url(bg.png);
  }
}

.element {
  animation: background 1ms paused;
  animation-composition: add;
}
```
<p class="codepen" data-height="500" data-default-tab="css,result" data-slug-hash="qBQdVVw" data-user="Schepp" style="height: 500px; box-sizing: border-box; display: flex; align-items: center; justify-content: center; border: 2px solid; margin: 1em 0; padding: 1em;">
  <span>See the Pen <a href="https://codepen.io/Schepp/pen/qBQdVVw">
  CSS Animation Composition for Composing Backgrounds</a> by Christian Schaefer (<a href="https://codepen.io/Schepp">@Schepp</a>)
  on <a href="https://codepen.io">CodePen</a>.</span>
</p>

This would also work for composing complex shorthands like `font` or other layered properties like [`fill`](https://drafts.fxtf.org/fill-stroke-3/#fill-layering).

## But is it available in all browsers?

As of writing this article, CSS Animation Composition is available in all browsers, except for Firefox, where the feature has still to be enabled via the `layout.css.animation-composition.enabled` flag. Hopefully, this will change soon!
