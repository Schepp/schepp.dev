---
title: "Rethinking Find-in-Page Accessibility: Making Hidden Text Work for Everyone"
description: Discover how to enhance accessibility and improve find-in-page search functionality using hidden="until-found" for icon-only buttons and hidden text labels.
image: /img/sunny-room.jpg
titleInverted: true
date: 2025-02-16
tags:
  - accessibility
layout: layouts/post.njk
---
I regularly attend [UX Graz](https://www.meetup.com/uxgraz/), a meetup covering diverse UX-related topics, alternating between in-person and remote events. At one recent event, [a blind speaker demonstrated how they navigate websites](https://www.meetup.com/uxgraz/events/305424662/). It wasn't my first time seeing someone blind use assistive technology for web navigation, but this experience stood out. Instead of relying on semantic elements like landmarks, headings, lists, or links, the speaker primarily used the browser's **find-in-page** functionality to navigate.

This approach surprised me at first. As frontend developers, we've been taught to focus on semantic HTML to aid navigation for assistive technology users. But the more I thought about it, the more it made sense: find-in-page can be a much faster and more efficient way to locate content, especially when semantic structures are missing or are poorly implemented. However, this technique isn't foolproof. It falters when text is hidden using attributes like `aria-label`, `title`, or `alt`, or when visible text is styled with `font-size: 0` - a method I often use for icon-only buttons.

The event left a lasting impression and plenty to think about.

Fast forward to last week: my podcast co-host, [Vanessa](https://vannsl.io/), messaged me about our Slack community. She needed the invite link, knew it was on our homepage, and hit `Cmd` + `F` to search for "Slack." Yet, nothing came up.

The issue? Once again, the text label for the Slack link was hidden using `font-size: 0`, as shown in this snippet:

```html
<a href="https://draft.community" style="font-size: 0">
  <svg.../> Slack
</a>
```

![Icons representing subscription and feedback channels: RSS feed, podcast, Spotify, Patreon, email, Twitter, Mastodon, Butterfly (possibly representing an alternative platform), LinkedIn, and Slack, displayed in a horizontal row with the label 'Abo- & Feedback-Kanäle' above them.](/img/workingdraft-icon-only-links.png)

Vanessa asked:

> "Is it possible to solve this on the frontend so that the icon gets focused when you use Cmd+F to search for a specific term?"

This reminded me of the meetup speaker's navigation pattern. When I told her about it, this is what she replied:

> "Honestly, I use websites exactly the same way."

## The Solution: `hidden="until-found"`

I had an idea to fix this problem using a relatively new HTML feature: the [`hidden="until-found"`](https://developer.chrome.com/docs/css-ui/hidden-until-found) attribute. Added to the HTML standard in March 2022, it was shipped in Chrome 102 (May 2022).

The `hidden="until-found"` attribute works similarly to `display: none`, preventing the element from rendering or taking up space. However, unlike `display: none`, the content remains in the accessibility tree, can be found via find-in-page, and is also targetable via anchor links. When a matching search or anchor link is triggered, the `hidden` attribute is removed, and the element becomes visible.

Here's how I updated our icon-only buttons:

```html
<a href="https://draft.community">
  <svg.../>
  <span class="hidden-until-found" hidden="until-found">Slack</span>
</a>
````

Now, the text label is searchable. If matched during a search, it appears dynamically. For a more pleasing user experience, I added a tooltip-like rendering for the revealed text using CSS:

```css
.hidden-until-found:not([hidden="until-found"]) {
  position: absolute;
  transform: translateX(calc(-50% - (var(--icon-width) / 2)));
  margin-top: -0.25rem;
  padding: 0 0.2rem 0.05rem 0.2rem;
  background-color: var(--brand-color);
  color: #fff;
  border-radius: 0.1rem;
  font-size: 1rem;
  filter: drop-shadow(0 0 3px rgba(0, 0, 0, 0.5));
}

/* little pointer at the bottom */
.hidden-until-found::after {
  content: '';
  position: absolute;
  z-index: -1;
  left: 50%;
  bottom: -0.2rem;
  width: 1rem;
  height: 1rem;
  transform: translateX(-50%) rotate(45deg);
  background-color: inherit;
}
```

The result 😍:

![Same screenshot as before, but with one difference: the search term 'Slack' is highlighted in an orange box with a purple pointer, positioned above the Slack icon, demonstrating how the find-in-page functionality visually emphasizes searched text within the page.](/img/workingdraft-icon-link-search-term-highlighted.png)

## Browser Compatibility and Accessibility

Currently, `hidden="until-found"` is only supported in Chromium-based browsers. In Firefox and Safari, the element behaves like a standard `hidden` attribute, meaning it isn't accessible via assistive technology, searchable, or linkable. While the latter two are tolerable (since they match the previous behavior), losing assistive tech accessibility is a problem.

To address this, I added an `aria-label` to ensure assistive technology still recognizes the link:

```html
<a href="https://draft.community" aria-label="Slack">
  <svg.../>
  <span class="hidden-until-found" hidden="until-found">Slack</span>
</a>
```

For broader browser support, consider weighing in on [WebKit](https://bugs.webkit.org/show_bug.cgi?id=238266) and [Mozilla](https://bugzilla.mozilla.org/show_bug.cgi?id=1761043) bug trackers.

## Remaining Challenges

1.  **Persistent Text Visibility**: Once text is revealed via find-in-page, it remains visible even after closing the search or starting a new one. Unfortunately, there's no native way to hide it again. A potential workaround is to listen for the [`beforematch`](https://developer.mozilla.org/en-US/docs/Web/API/Element/beforematch_event) event and reapply `hidden="until-found"` after a delay.

2.  **Styling Search Matches**: It's currently impossible to style matched text directly. However, future CSS pseudo-elements like [`::search-text`](https://drafts.csswg.org/css-pseudo-4/#selectordef-search-text) and [`::search-text:current`](https://github.com/w3c/csswg-drafts/issues/10527) could allow fine-grained control over search result styling.

This episode taught me the importance of accommodating diverse navigation patterns and highlighted (once more) how evolving standards can improve accessibility for everyone. But while `hidden="until-found"` is a good step forward, there's still work to be done to make find-in-page truly universal.
