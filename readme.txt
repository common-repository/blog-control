=== Blog Control ===
Tags: post, blog, link
Requires at least: 2.1
Tested up to: 2.3.2
Stable tag: 1.1.2

Blog Control helps to avoid the common operations blog's owners do when adopting
a new template: add scripts from mybloglog, google analytics, adsense or other
ad network. The plugin adds some automatic link modification (target, nofollow, ...)
and bots-blocking meta tag on specific pages. Read more in the description.

== Description ==

Blog Control helps to avoid the common operations blog's owners do when adopting
a new template: add scripts from mybloglog, google analytics, adsense or other
ad network. The plugin adds some automatic link modification (target, nofollow, ...)
and bots-blocking meta tag on specific pages. Read more in the description.

*Aggressive cache*. If you want to try out an aggressive cache system, blog control
have an implementation of a cache for wordpress pages. It's experimental, not to
be used on high traffic volume blogs, but on those have low database resources.
Read the installation istructions.

*Head and footer code*. Blog Control is a plugin that helps in controlling some behaviour of your blog.
It lets you to add code in the footer and head section of a template, eg. to add
code from services like the google sitemaps, mygloglog tracking script, analytics 
script without modify the template. So your configurations are template-change-proof.

*Post trasformation*. With this plugin you can add an html block before, after and after the "more
break" in a post. I use this to add my adsense code without changing the template pages.
Those html blocks accept some "tag" like [title] or [title_enconded] that are replaced
with the post title (plain or url econded). Tipical usage is a link like this:

`http://digg.com/submit?url=[link_encoded]&amp;title=[title_encoded]`

*Related posts*. The plugin has a simple related posts function based on a full text
index of mysql, like the standard related post implementation of other plugins.
The related posts can be added after a posts using the tag [related] in the "after
post" html block code. The output is controlled by some parameters which let to
set how the relates posts list is displayed.

*Links*. The plugin have many other options: a way to track the outbound links (if you
use google analytics), add the "nofollow" in the post links, force a new windows
for all the links outside your domain.

*HTML compression*. You can set the plugin to compress the html produced: you can gain from 10 to 30%.

*Bots blocking*. You can force a meta tag in categories and search pages to block the bots (useful to avoid
the content duplication issue).

== Installation ==

1. Put the folder "blog-control" into [wordpress_dir]/wp-content/plugins/
2. Go into the WordPress admin interface and activate the plugin
3. Optional: go to the options page and configure the plugin

== The agressive cache ==

This cache is simple and very efficient. Wordpress, when a blog page is called, 
lets someone to intercept the call and do "something" before all. No database access
will be made before this call neither a connection is activated.

For who, like me, have a very slow database, servin the page at this step is very
efficient. But there are some drawbacks:

1. no plugin are initialized neither loaded
2. no statistics can be collected
3. no dynamic code in the page will be executed (after the first call)

So, if you have a simple blog like mine, try to see if this cache can be useful.
Tha cache is expertimental, this is why no automatic installation has ben provided.

=Agressive cache installation (experimental)=

1. if you already have the blog control plugin, deactivate and reactivate it
2. deactivate the wp cache plugin if installed
3. remove or rename the link "wp-content/advanced-cache.php"
4. copy/upload the file "advanced-cache.php" in the folder "wp-content" from the blog control folder
5. edit the "wp-config.php" and add the line `define("WP_CACHE", true");` (if you use the wp cache plugin this code is already there)
6. now the aggressive cache *is active*! In the blog control configuration page activate the cache option otherwise when you add a post it won't show on home page
7. you're alone now...
8. you're alone now...

=Remove the agressive cache=

1. edit the "wp-config.php" and remove the line `define("WP_CACHE", true");`
2. delete the "wp-content/advanced-cache.php"
3. deactivate the cache option on the blog control configuration page
4. delete the "wp-content/bc-cache" directory
 