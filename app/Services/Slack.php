<?php namespace App\Services;
/**
 * @author: wanghui
 * @date: 2019/1/23 11:34 PM
 * @email:    hank.HuiWang@gmail.com
 */
use Maknz\Slack\Client;
use GuzzleHttp\Client as Guzzle;

class Slack {
    protected static $instance = null;

    /**
     * @var Client
     */
    protected $client;

    public function __construct()
    {
        $this->client = new Client(config('slack.endpoint'),[
            'channel' => config('slack.channel'),
            'username' => config('slack.username'),
            'icon' => config('slack.icon'),
            'link_names' => config('slack.link_names'),
            'unfurl_links' => config('slack.unfurl_links'),
            'unfurl_media' => config('slack.unfurl_media'),
            'allow_markdown' => config('slack.allow_markdown'),
            'markdown_in_attachments' => config('slack.markdown_in_attachments'),
        ], new Guzzle);
    }

    public static function instance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param $to
     * @return $this
     */
    public function to($to){
        $this->client->to($to);
        return $this;
    }

    /**
     * @param array $attachment
     * @return $this
     */
    public function attach(array $attachment) {
        $this->client->attach($attachment);
        return $this;
    }

    public function send($msg) {
        try {
            $this->client->send($msg);
            return $this;
        } catch (\Exception $e) {
            app('sentry')->captureException($e);
            return false;
        }
    }

}