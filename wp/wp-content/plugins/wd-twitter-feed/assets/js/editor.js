(function($) {
    
    var config = {
        slug:       'twitterfeed_button',
        title:      'Add a Twitter Feed Widget',
        icon:       'fa fa-twitter',
        text:       null,
        width:      600,
        height:     450,
        max_cols:   3,
        popups:     {
            statictweets: {
                img:        'static-icon.gif',
                label:      'Static Tweets',
                title:      'Insert Static Tweets',
                template:   '[statictweets skin="<% skin %>" resource="<% resource %>" user="<% user %>" list="<% list %>" query="<% query %>" id="<% id %>" count="<% count %>" retweets="<% retweets %>" replies="<% replies %>" ajax="<% ajax %>" show="<% show %>"/]'
            },
            scrollingtweets: {
                img:        'scrolling-icon.gif',
                label:      'Scrolling Tweets',
                title:      'Insert Scrolling Tweets',
                template:   '[scrollingtweets scroll_time="<% scroll_time %>" skin="<% skin %>" resource="<% resource %>" user="<% user %>" list="<% list %>" query="<% query %>" id="<% id %>" count="<% count %>" retweets="<% retweets %>" replies="<% replies %>"/]'
                ,disabled: true 
            },
            slidingtweets: {
                img:        'sliding-icon.gif',
                label:      'Sliding Tweets',
                title:      'Insert Sliding Tweets',
                template:   '[slidingtweets slide_dir="<% slide_dir %>" slide_duration="<% slide_duration %>" skin="<% skin %>" resource="<% resource %>" user="<% user %>" list="<% list %>" query="<% query %>" id="<% id %>" count="<% count %>" retweets="<% retweets %>" replies="<% replies %>" show="<% show %>"/]'
                ,disabled: true 
            }
        }
    };
    
    // Add the button to the editor
    tinymce.PluginManager.add( config.slug, function( editor, url ) {

        var dimensions = calcDimensions( config.popups.length, config.max_cols ),
            img_url = url.replace('/assets/js','/assets/img/');
        
        editor.addButton( config.slug, { 
            text: config.text, 
            icon: config.icon, 
            title: config.title, 
            onclick: function() {
                
                // Open a window showing all icon boxes
                editor.windowManager.open( {
                    title: config.title,
                    width: dimensions.width,
                    height: dimensions.height,
                    body: [{
                        type: 'container',
                        html: genHTML( config.popups, config.max_cols )
                    }],
                    buttons: [] // Hide footer
                });
                 
                 // Click event for each icon box
                $('.afw-editor-popup-icon').click(function(){
                    
                    var id      = $(this).attr('data-id'),
                        title   = config.popups[id].title,
                        template= config.popups[id].template,
                        action  = config.slug + '_' + id,
                        width   = config.width,
                        height  = config.height,
                        disabled= $(this).hasClass('disabled'),
                        url     = ajaxurl + '?action=' + action;
                    
                    // Disabled button, do nothing
                    if( disabled )
                    {
                        return;
                    }
                    
                    // Close the previous dialog
                    editor.windowManager.close(); 
                    
                    // Open a new ajax popup form window
                    Amarkal.Editor.Form.open( editor, {
                        title: title,
                        url: url,
                        width: width,
                        height: height,
                        template: template,
                        on_insert: on_insert
                    });
                });
            } 
        });
        
        /**
         * Generate the HTML for the icon boxes popup window.
         * 
         * @param {Object} buttons
         * @param {number} maxCols
         * @returns {String} The generated HTML.
         */
        function genHTML( buttons, maxCols )
        {
            var html = '', i = 0;
            for( var btn in buttons )
            {
                if( buttons.hasOwnProperty(btn) )
                {
                    html += '<div class="afw-editor-popup-icon'+(buttons[btn].disabled ? ' disabled' : '')+'" title="'+buttons[btn].label+'" data-id="'+btn+'">';
                    html += '<img src="'+img_url+buttons[btn].img+'"/>';
                    html += '<h3>'+buttons[btn].label+'</h3>';
                    html += '</div>';

                    if( (i+1) % maxCols === 0 )
                    {
                        html += '<br />';
                    }
                    i++;
                }
            }

            return html;
        }

        /**
         * Calculate the dimensions for the icon boxes popup window.
         * 
         * @param {number} boxCount
         * @param {number} maxCols
         * @returns {object} The calculated width and height of the window.
         */
        function calcDimensions( boxCount, maxCols )
        {
            var boxWidth    = 107,
                cols        = Math.min( maxCols, boxCount ),
                rows        = Math.ceil( boxCount / maxCols );
            return { width: cols*boxWidth+25, height: rows*boxWidth+25 };
        }
        
        /**
         * Create a floating toolbar for the code blocks in the visual editor.
         * This toolbar is shown once the element is clicked.
         */
        new Amarkal.Editor.FloatingToolbar( editor, {
            buttons: [
                {
                    slug: 'twitterfeed_edit',
                    icon: 'dashicon dashicons-edit',
                    tooltip: 'Edit',
                    onclick: function() { editTweets( editor.selection.getNode() ) }
                },
                {
                    slug: 'twitterfeed_remove',
                    icon: 'dashicon dashicons-no',
                    tooltip: 'Remove',
                    onclick: function() { removeTweets( editor.selection.getNode() ) }
                }
            ],
            selector: '.twitterfeed-placeholder'
        });
        
        //replace from shortcode to an placeholder image
//        editor.on('BeforeSetcontent', function(event) {
//            event.content = replaceShortcodes( event.content );
//        });
//
//        //replace from placeholder image to shortcode
//        editor.on('GetContent', function(event) {
//            event.content = restoreShortcodes(event.content);
//        });
//        
//        // helper functions
//        // @see http://generatewp.com/take-shortcodes-ultimate-level/
//        function getAttr(s, n) 
//        {
//            n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
//            return n ?  window.decodeURIComponent(n[1]) : '';
//        };
//
//        function html( type, data ) 
//        {
//            sh_data = window.encodeURIComponent( data );
//            var html = '<p><div contentEditable="false" class="twitterfeed-placeholder mceItem" data-type="'+type+'" data-sh-attr="' + sh_data + '">';
//            html += '<ul><li><strong>Type:</strong> '+type+'</li><li><strong>Resource:</strong> '+getAttr( data, 'resource' )+'</li><li><strong>Skin:</strong> '+getAttr( data, 'skin' )+'</li></ul>';
//            html += config.popups[type].label;
//            html += '</div></p>';
//            return html;
//        }
//
//        function replaceShortcodes( content ) 
//        {
//            return content.replace( /\[(statictweets|scrollingtweets|slidingtweets)([^\]]*)\/\]/g, function( all, type, attr ) {
//                return html( type, attr );
//            });
//        }
//
//        function restoreShortcodes( content ) 
//        {
//            var html = $.parseHTML( content );
//            var output = '';
//
//            $(html).each(function() { 
//                if( $(this).is('.twitterfeed-placeholder') )
//                {
//                    var type = $(this).attr('data-type');
//                    var data = window.decodeURIComponent($(this).attr('data-sh-attr'));
//                    output += '<p>[' + type + data + '/]</p>';
//                }
//                else
//                {
//                    output += this.outerHTML;
//                }
//            });
//            return output;
//        }
//        
//        /**
//         * Edit a code block by a given node.
//         * 
//         * @param {type} node The code block's HTML node
//         */
//        function editTweets( node )
//        {
//            if( !$(node).is('.twitterfeed-placeholder') )
//            {
//                node = $(node).parents('.twitterfeed-placeholder')[0];
//            }
//            
//            var atts = window.decodeURIComponent($(node).attr('data-sh-attr'));
//            var type = $(node).attr('data-type');
//            var popup = config.popups[type];
//
//            // Open a new ajax popup form window
//            Amarkal.Editor.Form.open( editor, {
//                title:  popup.title,
//                url:    ajaxurl + '?action=' + config.slug + '_' + type,
//                width:  config.width,
//                height: config.height,
//                template: popup.template,
//                on_insert: on_insert,
//                values: {
//                    resource: getAttr( atts, 'resource' ),
//                    user: getAttr( atts, 'user' ),
//                    list: getAttr( atts, 'list' ),
//                    query: getAttr( atts, 'query' ),
//                    count: getAttr( atts, 'count' ),
//                    retweets: getAttr( atts, 'retweets' ),
//                    replies: getAttr( atts, 'replies' ),
//                    skin: getAttr( atts, 'skin' ),
//                    show: getAttr( atts, 'show' ),
//                    slide_dir: getAttr( atts, 'slide_dir' ),
//                    slide_duration: getAttr( atts, 'slide_duration' ),
//                    scroll_time: getAttr( atts, 'scroll_time' )
//                }
//            });
//            return;
//        }
//        
        /**
         * Remove a code block from the tinyMCE editor.
         * 
         * @param {type} node
         */
        function removeTweets( node ) 
        {
            if( !$(node).is('.twitterfeed-placeholder') )
            {
                node = $(node).parents('.twitterfeed-placeholder')[0];
            }
            
            if ( node ) {
                if ( node.nextSibling ) {
                    console.log('next');
                    editor.selection.select( node.nextSibling );
                    console.log(node.nextSibling);
                } else if ( node.previousSibling ) {
                    console.log('prev');
                    editor.selection.select( node.previousSibling );
                } else {
                    console.log('parent');
                    editor.selection.select( node.parentNode );
                }
                
                editor.selection.collapse( true );
                editor.dom.remove( node );
            } else {
                editor.dom.remove( node );
            }

            editor.nodeChanged();
            editor.undoManager.add();
        }
        
        function on_insert( editor, values ) {
            var args = editor.windowManager.getParams(),
                node = editor.selection.getNode(),
                template = args.template;
            
            editor.insertContent( Amarkal.Editor.Form.parseTemplate( template, values ) );
            removeTweets( node );
        }
    });
})(jQuery);