<?php
/**
 * @package    twitterfeed
 * @date       Thu Aug 18 2016 17:02:02
 * @version    2.1.9
 * @author     Askupa Software <hello@askupasoftware.com>
 * @link       http://products.askupasoftware.com/twitter-feed/
 * @copyright  2016 Askupa Software
 */

namespace TwitterFeed\Resource;

/**
 * 
 */
class Resource_status 
{
    const URL_SCRIPT = 'application/rate_limit_status.json';
    
    private $api;
    private $args;
    
    public function __construct( $args ) 
    {
        $this->api = new \TwitterAPIExchange($this->get_tokens());
        $this->args = $args;
    }
    
    public function perform_request()
    {
        return json_decode(
            $this->api->setGetfield($this->build_argument_list($this->args))
                 ->buildOauth($this->get_url(), 'GET')
                 ->performRequest()
            );
    }
    
    public function get_url()
    {
        return TwitterResource::API_URL . self::URL_SCRIPT;
    }
    
    public function get_tokens()
    {
        if( !isset( $this->tokens ) )
        {
            global $twitterfeed_options;
            $this->tokens = array(
                'oauth_access_token'        => $twitterfeed_options['oauth_access_token'],
                'oauth_access_token_secret' => $twitterfeed_options['oauth_access_token_secret'],
                'consumer_key'              => $twitterfeed_options['consumer_key'],
                'consumer_secret'           => $twitterfeed_options['consumer_secret']
            );
        }
        return $this->tokens;
    }
    
    protected function build_argument_list( array $args = array() ) 
    {
        $arg_list = '';
        foreach($args as $key => $value)
        {
            $arg_list = add_query_arg( array( $key => $value ), $arg_list );
        }
        return esc_url_raw ( $arg_list );
    }
}
