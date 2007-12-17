=== Plugin Name ===
Contributors: pfefferle
Donate link: http://notizblog.org
Tags: hCard Commenting, hCard, OpenID, Auth, Commenting, Comments, Microformats
Requires at least: 2.2.x
Tested up to: 2.3.1
Stable tag: 0.5

This Plugin allows your users to easily fill out your comment forms using an hCard.

== Description ==

This Plugin allows your users to easily fill out your comment forms using hCard. I got this
Idea from SignUp pages like [bragster.com](https://www.bragster.com/signup) or 
[getsatisfaction.com](http://getsatisfaction.com/people/new).

== Installation ==

* Upload the `wp-hcard-commenting` to your **plugin** folder

* It should work for the most themes (like K2 or Kubrik) without changes

* For all others, simply add `<?php hcard_commenting_link() ?>` where you want
the link to be displayed

Thats it

== Frequently Asked Questions ==

== Which Microformats parser du you use? ==

I use the [hKit](http://allinthehead.com/hkit/) parser from Drew McLellan and the hKit
service from [microformatic.com](http://tools.microformatic.com/help/xhtml/hkit/).

== Why do you also use the service ==

hKit needs PHP5, if you run PHP4.3 you can't run the hKit parser natively.

== What are the next steps? ==

I would like to integrate the service to the public and admin register page.

== Screenshots ==

You can find a demo here: [notizBlog.org](http://notizblog.org).