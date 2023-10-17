---
title: Restore your Follows from Twitter/X in BlueSky
description: How to easily re-build your Twitter/X feed in BlueSky
image: /img/blue-sky.jpeg
date: 2023-10-17
tags:
  - twitter
  - x
  - bluesky
layout: layouts/post.njk
---
As we all know X, née Twitter, is turning more and more into a shitshow. Time to move on to other platforms! Sadly, the people I used to follow flock into all sorts of directions: be it LinkedIn, Mastodon, BlueSky or all of them. Since BlueSky is currently invite only and I'm not the kind of person chasing after invites, I had no presence there yet. But last week at Smashing Conf Antwerp I met Claudia and she granted me with an invite. Thanks a bunch! So here I am: [@derschepp.bsky.social](https://bsky.app/profile/derschepp.bsky.social)

## Reinstating your Twitter/X followings on BlueSky

While thanks to the [Movetodon](https://www.movetodon.org/) project it was easy to re-follow everyone on Mastodon that I followed on Twitter/X - back in the day when the Twitter API still worked, nowadays there's only [Fedifinder](https://fedifinder.glitch.me/) and the Chrome plugin [Sky Follower Bridge](https://chrome.google.com/webstore/detail/sky-follower-bridge/behhbpbpmailcnfbjagknjngnfdojpko) left to reconnect with your followings on BlueSky. The problem with both: you need to click a button for every single person you want to re-follow. There is no "follow them all" button.  But when you follow almost 5.000 people on Twitter/X, that's just not feasible! So I did what a front-end developer does, and wrote myself a script to execute in the browser console which works together with the Sky Follower Bridge plugin and automates the process of following the people dug up by it.

## Give me the Code

First make sure to install [Sky Follower Bridge](https://chrome.google.com/webstore/detail/sky-follower-bridge/behhbpbpmailcnfbjagknjngnfdojpko) and to bring it to work by running it on your Twitter/X "[Following](https://twitter.com/following)" page and then feeding it with a BlueSky app password, which you can generate [here](https://bsky.app/settings/app-passwords). Once the first "Follow on BlueSky" buttons appear, open the browser devtools, run this script and give it some time to work itself through all your followings:

```js
const wait = (milliseconds) => new Promise((resolve) => {
  window.setTimeout(resolve, milliseconds);
});

const clickMoreButton = () => new Promise((resolve) => {
  const lookForButton = () => {
    const loadMoreButton = document.querySelector('.bsky-reload-btn');

    if (!loadMoreButton) {
      window.setTimeout(lookForButton, 200);
      return;
    }

    loadMoreButton.scrollIntoView();
    loadMoreButton.click();

    resolve(loadMoreButton);
  };

  lookForButton();
});

const followAllResults = () => {
  const followButtons = Array.from(document.querySelectorAll('.action-button:not(.action-button__being)'));

  return followButtons.reduce(async (acc, button) => {
    button.click();

    await wait(200);

    return true;
  }, Promise.resolve());
};

const run = async () => {
  await followAllResults();
  await clickMoreButton();
  await wait(2000);

  return run();
};

await run();
```
