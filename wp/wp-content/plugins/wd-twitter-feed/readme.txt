=== Twitter Feed ===
Contributors: Askupa Software, ykadosh
Tags: twitter, tweet, tweets, twitter feed, twitter timeline, twitter widget, social, social media, twitter shortcode, embed tweets,
Requires at least: 3.0
Tested up to: 4.6.0
Stable tag: 2.1.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A powerful Twitter integration system that allows you to display tweets using widgets and shortcodes.

== Description ==

**Twitter Feed** allows you to easily display tweets from Twitter in a wide range of ways, using *shortcodes* and *widgets*. 
**Twitter Feed** comes with fully responsive tweet skins. Using the caching engine, you can improve performance and decreasing load time by storing tweets locally. 
You can use the *Usage Tracker* tool to track your usage status and see if it is in accordance with Twitter's rate limits.

**Features**

* Uses Twitter API 1.1
* Shortcodes + shortcode editor
* Widget
* LTR and RTL support
* Flexible Caching system
* Tweet customization
* Tweet actions (Like, Reply, Retweet)
* Tweet media (photos, YouTube, Vine, GIFs)
* 100% responsive
* Easy API for developers
* Usage status tracking tool
* Very simple to setup & maintain
* Clean & simple plugin options page
* Illustrated step-by-step guide

**Useful Links**

* [Official Page](http://products.askupasoftware.com/twitter-feed/)
* [Documentation](http://products.askupasoftware.com/twitter-feed/documentation/)
* [Live Examples](http://products.askupasoftware.com/twitter-feed/examples/)
* [Shortcode Reference](http://products.askupasoftware.com/twitter-feed/shortcodes/)
* [API Reference](http://products.askupasoftware.com/twitter-feed/api/)
* [How to create a Twitter app](http://blog.askupasoftware.com/how-to-create-a-twitter-application/)

Follow us on [Twitter](http://www.twitter.com/askupasoftware) and on [Facebook](http://www.facebook.com/askupasoftware) to get the latest updates and version releases.

This is the limited version of the [commercial plugin](http://codecanyon.net/item/twitter-feed-social-plugin-for-wordpress/6665168?ref=Askupa) that features 8 different twitter resources (including lists and search), 5 different skins and 3 shortcodes and 3 widgets!

**Available Translations**

* English
* German
* Hebrew

== Installation ==

1. Download the plugin zip package and extract it.
1. Put the folder named "askupa-twitter-feed" under /wp-content/plugins/ directory
1. Goto the plugins page in your Wordpress admin panel and click "Activate"
1. Goto Plugins -> Twitter Feed and enter your oAuth credentials (you would need to create a new application at https://dev.twitter.com/apps)
1. You can implements tweets into your posts and pages using shortcodes or you can go to your widget area (Appearance -> widgets) and add *Twitter Feed* to your widget panel

== Frequently Asked Questions ==

= Where can I find the oAuth access tokens? =

Twitter uses oAuth access tokens to verify the user that is fetching the data. In order to get your own tokens, you would have to create a twitter app on https://dev.twitter.com/ after which you will be provided with the app’s oAuth credentials that are needed for Twitter Feed.

= Is it possible to use the plugin without using shortcodes? =

Since version 1.43, Twitter Feed comes with a PHP based API that allows you to fetch tweets anywhere without having to use shortcodes. For more information, visit the [API documentation](http://products.askupasoftware.com/twitter-feed/api/) page.

= Why do I need to enable caching? =

Twitter Feed makes a request to the Twitter API every time someone views a page in your blog. This creates a problem since Twitter imposes a limit of 180 requests per hour. exceeding the limit would prevent you from making new requests until the clock is reset.
The caching system allows you to fetch the data locally, which not only will prevent you from exceeding the rate limits, but is also much faster than making a request to Twitter.

= Where can I update the settings? / I can't find the plugin settings =

In order to update the settings of the widget, you'll need to login to your Wordpress admin panel and navigate to *Plugins -> Twitter Feed* where you will find a complete options page for the plugin.

= Is possible to show multiple hashtags? =

Since version 1.1, Twitter Feed supports making queries using the new Twitter search engine. Everything that is possible through the search engine is possible through Twitter Feed. For example, you can display all the tweets that contain the #jazz and the #guitar hashtags by specifying the search query `#jazz AND #guitar` in the query input field.

== Screenshots ==

1. Twitter Feed option page
2. Shortcode editor
3. Text editor (TinyMCE) button popup options
4. Widget
5. Simplistic-skin
6. Default skin (matches the theme style)
7. Usage status tracker

== Changelog ==

= 2.1.9 =
* (FIX) "Amarkal" is not defined issue in admin area

= 2.1.8 =
* (UPDATE) Bumbed up dependencies versions
* (FIX) jQuery UI dependency loading issue

= 2.1.7 =
* (NEW) Added support for tweet media in sliding tweets
* (NEW) Added German translation
* (FIX) Event binding issue with dynamically loaded tweets

= 2.1.6 =
* (NEW) Added an option to control the avatar size (Can be managed under Twitter Feed->Appearance)
* (FIX) Trailing white spaces in authentication tokens are now being trimmed to prevent the "Bad authentication data" error

= 2.1.5 =
* (NEW) Added support for loading more tweets via AJAX (Commercial Version)
* (NEW) Added support for single tweet embedding (Commercial Version)
* (NEW) Added support for animated GIFs
* (NEW) Added an API layer to easily render tweets using PHP. See [API documentation](http://products.askupasoftware.com/twitter-feed/api/) for more info
* (IMPROVE) Better error messages
* (IMPROVE) Retweets/likes count number format
* (FIX) Vine ratio issue
* (FIX) Automatically playing vines when they become visible in the page

= 2.1.4 =
* (NEW) Added a Hebrew translation
* (NEW) Added a .pot file for internationalization
* (FIX) Fixed an issue related to late static binding
* (UPDATE) Bumped up PHP required version to 5.4.4
* (UPDATE) Tested under WordPress 4.4.2

= 2.1.3 =
* (UPDATE) Bumped up dependencies versions
* (FIX) Tweet time is now relative to GMT instead of local time
* (FIX) Showing an error message when cURL is not installed
* (FIX) Showing an error message when Twitter returns 0 results

= 2.1.2 =
* (UPDATE) Twitter-API-Exchange version 1.0.4
* (UPDATE) Changed the 'favorites' start icon to heart (as has been done by Twitter)
* (FIX) Caching related bug
* (FIX) Bug that was causing the search resource to show retweets/replies when they are set to off
* (FIX) Bug that was causing widgets not to show retweet credits

= 2.1.1 =
* (FIX) Some CSS bugs
* (UPDATE) Tested under WordPress version 4.3.1

= 2.1.0 =
* (NEW) You can now set tweet media to automatically show when the page loads (located under Twitter Feed -> Appearance)
* (NEW) You can now choose to show the url's expanded version (located under Twitter Feed -> Appearance)
* (FIX) Hashtags with Latin characters are now being rendered correctly
* (UPDATE) Amarkal Framework v0.3.7

= 2.0.8 =
* (FIX) Bug that was causing the widget to break
* (UPDATE) Improved HTML Accessibility
* (UPDATE) Tested under WordPress 4.2.3

= 2.0.7 =
* (FIX) Bug that was causing search results not to be properly stored in cache
* (UPDATE) Widget code structure
* (UPDATE) Loading all media using https

= 2.0.6 =
* (FIX) Bug that was breaking the status tracker if Twitter could not authenticate the user
* (FIX) Issue related to embedded youtube videos
* (UPDATE) Bumped up FontAwesome version

= 2.0.5 =
* (FIX) Improved code structure and fixed PHP notices
* (FIX) Security vulnerability related to add_query_args()
* (FIX) RTL/LTR issue
* (UPDATE) Amarkal Framework v0.3.6

= 2.0.4 =
* (UPDATE) Amarkal Framework v0.3.5
* (UPDATE) Twitter API Exchange v5.3.10
* (NEW) Add an option the write custom CSS (under 'Appearance')
* (FIX) Tested under WP_DEBUG mode
* (FIX) Scrolling tweets showing the wrong tweet time
* (FIX) Bug that was causing retweet credits not to show

= 2.0.3 =
* (UPDATE) Amarkal Framework v0.3.4
* (FIX) Some CSS issues
* (FIX) Slider height is now adjusting automatically

= 2.0.2 =
* (UPDATE) Updated code documentation and improved formatting
* (UPDATE) Amarkal Framework v0.3.3
* (FIX) Some CSS issues
* (FIX) Widget assets not corresponding to settings
* (FIX) Issue involving multiple framework instances

= 2.0.1 =
* (FIX) Critical Amarkal framework update

= 2.0.0 =
* (NEW) Minified and concatenated CSS & JS, only 10kb total!
* (NEW) Better formatted, self contained JavaScript
* (NEW) Integrated into Amarkal Framework
* (NEW) options & widget control panels
* (NEW) Editor widget with easy-to-use box icons
* (NEW) Added usage status section to the admin panel (Thanks for the idea, Mark Rickan!)
* (FIX) Improved stability
* (FIX) Improved Cross-Browser compatibility
* (FIX) Bug report tool not getting through spam filter

= 1.4.4 =
* (FIX) Minor bug fixes
* (FIX) Minor CSS improvements
* (UPDATE) field names for oAuth tokens
* (UPDATE) Readme

= 1.4.3 =
* (NEW) Twitter Feed API
* (FIX) Separated view from controller
* (UPDATE) Minor CSS updates

= 1.4.2 =
* (FIX) for include/exclude retweets
* (FIX) for bug-report section
* (NEW) Added widget subtitle option
* (NEW) Added tooltips for tweet actions

= 1.4.1 =
* (FIX) Critical fix for Wordpress 3.9 and TinyMCE 4
* (NEW) editor button icon, with retina display resolution
* (FIX) Minor CSS fixes

= 1.4.0 =
* (NEW) Added support for embedded media in tweets. A tweet containing media will have a button that show the included media when clicked. Supported media types are (for now): Photos, Vine videos and YouTube videos
* (NEW) Improved CSS

= 1.3.0 =
* (FIX) namespace issue
* (FIX) Critical fix for a bug that was causing the caching system to crash
* (UPDATE) Removed unused files

= 1.2.0 =
* (FIX) Loading Font Awesome from a CDN instead of storing it locally
* (FIX) Removed the 20 tweets limit from the input fields
* (FIX) minor CSS issues
* (FIX) web-intent issue that was causing it to open twice
* (FIX) Reorganized file structure
* (FIX) Added PHP namespace implementation to prevent name collisions and improve modularity
* (UPDATE) Tested on more themes to improve CSS compatibility
* (UPDATE) Updated wrong UI instructions

= 1.1.0 =
* (NEW) Twitter lists. Feature any list from any user.
* (NEW) Twitter search. Feature tweets using the twitter search api.
* (NEW) Home timeline. Feature your home timeline, including your tweets, and the tweets of those you follow.
* (NEW) Mentions of me. Feature tweets containing a users's @screen_name for the authenticating user. 
* (NEW) Retweets of me. Feature the most recent tweets authored by the authenticating user that have been retweeted by others.
* (UPDATE) Cache design
* (UPDATE) Debug mode can now be turned on/off from the administration panel.
* (UPDATE) User manual to reflect the new features
* (FIX) Slider with a single slide issue

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 2.1.0 =
* New features and some bug fixes

= 2.0.8 =
* Minor bug fixes

= 2.0.7 =
* Minor bug fixes. Widget renamed - make sure to re-insert widget after updating

= 2.0.3 =
* Security update

= 2.0.3 =
* Minor bug and CSS fixes

= 2.0.2 =
* Widget assets not corresponding to settings

= 2.0.1 =
* Critical Amarkal framework update

= 2.0.0 =
* Brand new user interface, multiple bug fixes, improved CSS.

= 1.4.4 =
* Minor bug fixes

= 1.4.3 =
* New API

= 1.4.2 =
* Fix for include/exclude retweets

= 1.4.1 =
* Critical fix for WordPress 3.9

= 1.4.0 =
* Added tweet media support for photos, YouTube & Vine videos

= 1.3.0 =
* Critical bug fix