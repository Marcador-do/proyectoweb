/**
 * Sliding Tweets
 * 
 * @param {Node} el The element to be converted to a slider
 */
TwitterFeed.SlidingTweets = function( el ) 
{
    this.el = el;
    this.slides = $(el).find('.atf-tweet-padder');
    this.slide_count = this.slides.length;
    this.slide_duration = parseInt($(el).attr('data-slide-duration')) * 1000;
    this.slide_direction = $(el).attr('data-slide-dir');
    this.slider_width = $(el).width();
    this.slider_height = $(el).height();
    
    this.init();
}

/**
 * Initiate the slider
 */
TwitterFeed.SlidingTweets.prototype.init = function() 
{
    // Hide slides
    $(this.slides).each(function(i,e){
        $(this).hide();
    });
    
    // Show the first slide
    this.show_slide(0);
    
    // Play the slide show from slide 1
    if(this.slide_count > 1)
        this.play(1);
    
    // Fix width/height on window resize
    var self = this;
    $(window).resize(function(){
        self.adjust_view();
    });
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
TwitterFeed.SlidingTweets.prototype.show_slide = function(slide_num) 
{
    // There is no slide visible currently
    if(typeof this.current_slide === 'undefined')
    {
        $(this.slides[slide_num]).show();
    }
    
    // Hide the previous slide and show the requested one
    else 
        this.transition(this.slides[this.current_slide], this.slides[slide_num]);
    
    // Update current slide number
    this.current_slide = slide_num;
    this.adjust_view();
};

/**
 * Fix the visual appearance of the current slide.
 */
TwitterFeed.SlidingTweets.prototype.adjust_view = function() 
{
    var slide = $(this.slides[this.current_slide]),
        height = slide.outerHeight(),
        width = slide.outerWidth();
    $(this.el).height( height );
    this.slider_width = width;
    this.slider_height = height;
};

/**
 * Transition
 * 
 * Animate a transition between two slides.
 * 
 * @param    {DOM element}    slide1 The current slide
 * @param    {DOM element}    slide2 The new slide
 */
TwitterFeed.SlidingTweets.prototype.transition = function(slide1, slide2)
{    
    // Vars
    var pool = new Array('up','down','left','right');
    var anim1, anim2, css2;
    var dir = this.slide_direction;
    var reset_position = {top:'',left:'',bottom:'',right:''};
    
    // Randomize direction
    if(dir === 'random') 
    {
        var index = Math.floor(Math.random() * 4)
        dir = pool[index];
    }
    
    // Set the animation properties according to the direction
    switch(dir) 
    {
        case 'up':
            anim1 = {top : '-' + this.slider_height};
            anim2 = {top : 0};
            css2 = {top : this.slider_height};
            break;
        case 'down':
            anim1 = {bottom : '-' + this.slider_height};
            anim2 = {bottom : 0};
            css2 = {bottom : this.slider_height};
            break;
        case 'left':
            anim1 = {left : '-' + this.slider_width};
            anim2 = {left : 0};
            css2 = {left : this.slider_width};
            break;
        case 'right':
            anim1 = {right : '-' + this.slider_width};
            anim2 = {right : 0};
            css2 = {right : this.slider_width};
            break;
    }
    
    // Animate current slide
    $(slide1).animate(
        anim1,
        1000,
        function() {
            $(this).hide().css(reset_position);
        }
    );
        
    // Animate new slide
    $(slide2)
        .css(css2)
        .show()
        .animate(anim2,1000,function(){
            $(this).css(reset_position);
        });
};

/**
 * Play
 * 
 * Play the slideshow and scroll the text
 * of each slide.
 * 
 * @param {int}   slide_num   The number of the
 *                            slide to start playing
 *                            from.
 */
TwitterFeed.SlidingTweets.prototype.play = function(slide_num) 
{
    var Slider = this;
    var i = slide_num;
    
    // Make sure the slide is within the array boundaries
    if(i >= this.slide_count)
        i = 0;
    
    interval = setInterval(function(){
        Slider.show_slide(i++);
        if(i === Slider.slide_count) i = 0;
    }, 
    this.slide_duration);
};

$('.atf-sliding-tweets').each(function(){
    new TwitterFeed.SlidingTweets( this );
});