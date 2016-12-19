TwitterFeed.ToolTip = function( wrapper )
{
    this.wrapper    = $(wrapper);
    this.text       = this.wrapper.attr('title');
    this.el         = this.createTooltip(this.text);
    this.delay      = 300;
    this.arrowWidth = 5;
    this.top        = 0; // Calculated after the element is created
    this.timer;
    
    this.init();
};

TwitterFeed.ToolTip.prototype.init = function()
{
    var self = this;
    
    this.wrapper.css({position:'relative'});
    
    this.wrapper.hover(
        function(){self.mouseover()},
        function(){self.mouseout()}
    );
};

TwitterFeed.ToolTip.prototype.createTooltip = function( text )
{
    var el    = $('<div></div>'),
        inner = $('<div></div>');
    
    
    el.addClass('atf-tooltip');
    inner.addClass('atf-tooltip-inner');
    inner.html(text);
    el.append(inner);
    
    return el;
};

TwitterFeed.ToolTip.prototype.mouseover = function()
{
    clearTimeout(this.timer);
    this.wrapper.append(this.el);

    if( this.top === 0 )
    {
        this.top = -(this.el.outerHeight() + this.arrowWidth);
    }
    
    this.el.addClass('visible');
    this.el.css({
        top: this.top
    });
};

TwitterFeed.ToolTip.prototype.mouseout = function()
{
    var self = this;
    this.timer = setTimeout(function() 
    {
        self.el.removeClass('visible');
        self.el.remove();
    }, this.delay);
};

// Set tooltips
$('.atf-web-intent').each(function(){
    new TwitterFeed.ToolTip( this );
});