/**
 * Scrolling Tweets
 * 
 * @param {type} el The element to be converted to a scroller
 */
TwitterFeed.ScrollingTweets = function( el )
{
    this.el = el;
    this.slides = $(el).find('.atf-tweet-wrapper');
    this.slide_count = this.slides.length;
    this.slide_duration = parseInt($(el).attr('data-scroll-time')) * 1000;
    
    // Hide slides
    $(this.slides).each(function(i,e){
        $(this).hide();
    });
    
    // Show the first slide
    this.showSlide(0);
    this.scrollText(0, 1000);
    
    // Play the slide show from slide 1
    this.play(1);
};

/**
 * Show Slide
 * 
 * Show the slide from the slides array with
 * the given index 'slide_num' and hide the previous
 * slide.
 * 
 * @param {int}        slide_num    The number of the
 *                                slide to show
 */
TwitterFeed.ScrollingTweets.prototype.showSlide = function(slide_num) 
{    
    // Hide the previous slide and show the requested one
    $(this.slides[this.current_slide]).hide();
    $(this.slides[slide_num]).fadeIn(200);
    
    // Update current slide number
    this.current_slide = slide_num;
    
};

/**
 * Scroll Text
 * 
 * If the width of the textnode is greater than the
 * width of its wrapper, this will scroll the text
 * from right to left.
 * 
 * @param {int}        slide_num        The number of the
 *                                    slide to show
 * @param {int}        scroll_delay    The delay to 
 *                                    start scrolling 
 *                                    in milliseconds
 */
TwitterFeed.ScrollingTweets.prototype.scrollText = function(slide_num, scroll_delay) 
{
    // Variables
    var child       = $(this.slides[slide_num]).find('p');
    var child_width = $(child).width();
    var wrapper     = $(this.slides[slide_num]).find('.atf-tweet-text');
    var wrapper_width = $(wrapper).width();
    var diff        = child_width - wrapper_width;
    
    // Zero the absolute position of the text
    $(child).css('left',0);
    
    // The text size exceeds the wrapper size
    if(diff > 0) {
        $(child).delay(scroll_delay).animate({ 
            left: "-=" + diff,
        }, 
        // Use the difference to set the time, so that 
        // the scrolling time is the same regardless 
        // of the width of the text node 
        diff*10 );
    }
};


/**
 * Play
 * 
 * Play the slideshow and scroll the text
 * of each slide.
 * 
 * @param {int}        slide_num        The number of the
 *                                    slide to start playing
 *                                    from.
 */
TwitterFeed.ScrollingTweets.prototype.play = function(slide_num) 
{
    var scroller = this;
    var i = slide_num;
    
    // Make sure the slide is within the array boundaries
    if(i >= this.slide_count)
        i = 0;
    
    interval = setInterval(function(){
        scroller.showSlide(i);
        scroller.scrollText(i++, 1000);
        if(i === scroller.slide_count)
            i = 0;
    }, this.slide_duration);
};

$('.atf-scrolling-tweets').each(function(){
    new TwitterFeed.ScrollingTweets( this );
});