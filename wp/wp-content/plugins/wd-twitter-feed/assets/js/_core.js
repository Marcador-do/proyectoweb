TwitterFeed.settings = {
    popUpWidth:  700,
    popUpHeight: 345
};

TwitterFeed.init = function() 
{
    // Bind onclick tweet actions
    $('body').on('click', '.atf-web-intent', function(e) {
        e.preventDefault(); // Prevent the link from being opened
        newwindow = window.open(
            this.getAttribute("href"),
            this.getAttribute("title"),
            'height=' + TwitterFeed.settings.popUpHeight + ',width=' + TwitterFeed.settings.popUpWidth
        );

        // Focus
        if(window.focus) { newwindow.focus(); }

        // Centralize the popup window
        newwindow.moveTo((screen.width-TwitterFeed.settings.popUpWidth)/2,(screen.height-TwitterFeed.settings.popUpHeight)/2);
        return false;
    });

    // Show hide media
    $('body').on('click', '.atf-show-media-button', function(e) {
        e.preventDefault();
        var tweet = $(this).parent();
        tweet.find('.atf-media-wrapper').toggle(300, function(){
            var el = tweet.find('.atf-show-media-button > span');
            var text = el.text() === 'Show' ? 'Hide' : 'Show';
            el.text(text);
        });
    });
    
    // Load more tweets
    $('.atf-load-more').click(function(e){
        var $spinner = $(this).find('.fa-refresh'),
            $data = $(this).find('[name="atf-data"]'),
            $tweets = $(this).parent().find('.atf-inner-wrapper'),
            ajaxurl = $(this).find('[name="ajaxurl"]').val(),
            data = JSON.parse($data.val());
        
        // Prevent additional requests until this one is complete
        if($spinner.hasClass('fa-spin')) return;

        $spinner.addClass('fa-spin');
        $.post(ajaxurl,data,function(res){
            if(res.success) {
                $spinner.removeClass('fa-spin');
                data.position = res.data.pos;
                $data.val(JSON.stringify(data));
                $tweets.append(res.data.tweets);
            }
        });
    });
    
    // Make tweet clickable
    $('body').on('click', '.atf-tweet-wrapper', function(e){
        if( !$(e.target).closest('a').length )
            window.open($(this).attr('data-tweet-url'));
    });

    // Hide debug window onclick
    $('#twitter-feed-debug-window .close-button').click(function() {
        $('#twitter-feed-debug-window').hide();
    });
};
