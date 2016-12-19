<?php
/**
 * @package    twitterfeed
 * @date       Thu Aug 18 2016 17:02:02
 * @version    2.1.9
 * @author     Askupa Software <hello@askupasoftware.com>
 * @link       http://products.askupasoftware.com/twitter-feed/
 * @copyright  2016 Askupa Software
 */

namespace TwitterFeed\Debug;

/**
 * Debug class
 * 
 * This class has only one instance (singleton design pattern).
 * Used as a database for debugging purposes. Any class can
 * send a message to this class, and add that message to the 
 * list of debug messages.
 */
class Debugger 
{    
    private $debug_data;
    private static $instance = null;
    
    /**
     * Return an instance of this class
     * 
     * @return TweetsParser The only instance of this class
     */
    public static function get_instance() 
    {

        // If the single instance hasn't been set, set it now.
        if ( null == self::$instance ) {
            self::$instance = new self;
        }

        return self::$instance;
    }
    
    /**
     * Append a message to the debug data
     * 
     * @param    type    $message    The message to add to the queue
     */
    public function append($message) 
    {
        $callers = debug_backtrace();
        $data['function'] = $callers[1]['function'];
        $data['message'] = $message;
        $data['class'] = $callers[1]['class'];
        $this->debug_data[] = $data;
    }
    
    /**
     * Get the debug data array
     * 
     * @return array The debug messaged array
     */
    public function get_data() 
    {
        return $this->debug_data;
    }
    
    /**
     * Display the data in HTML formatting
     */
    public function render() 
    {
        $template = new \Amarkal\Template\Template( dirname(__FILE__).'/template.phtml', array('data' => $this->debug_data));
        echo $template->render();
    }
}
