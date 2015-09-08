<?php

/**
 * @copyright Copyright &copy; Thành Nguyễn
 * @package yii2-widgets
 * @subpackage yii2-widget-social
 * @version 1.0.0
 */

namespace kun391\socials;

use Yii;
use yii\helpers\Html;

/**
 * Example(s):
 * ```php
 * echo FollowerSocial::widget(['social' => FollowerSocial::FOLLOWER_FACEBOOK, 'people' => '233971640113963']);
 *
 * @author Thành Nguyễn <nguyentruongthanh.dn@gmail.com>
 * @since 1.0
 */
class FollowerSocial extends \yii\base\widget
{
    CONST FOLLOWER_FACEBOOK = 'facebook';
    CONST FOLLOWER_GOOGLE = 'google';
    CONST FOLLOWER_YOUTUBE = 'youtube';

    /**
     * The target to get followers Ex: Google, Facebook, etc.
     *
     * @var string
     */
    public $social = self::FOLLOWER_GOOGLE;

    /**
     * The api key for request to server
     *
     * @var string
     */
    public $apiKey = 'AIzaSyCbqnMP7p6MylSCC2yc9TBAETwaILHmogc';

    public $fbAppId = '615985471754714';
    public $fbSecret = '39c01895042b54e758d566ebbfe56a1a';

    /**
     * ID/Username of target to get followers
     *
     * @var string
     */
    public $people = '115274693861518025504';

    /**
     * Url of server
     *
     * @var string
     */
    public $urlAPI = '';

    /**
     * Attribute followers on results to get number of followers
     *
     * @var string
     */
    private $attribute = 'circledByCount';

    /**
     * Initializes the widget
     */
    public function init()
    {
        parent::init();
        if ($this->social == self::FOLLOWER_GOOGLE) {
            $this->urlAPI = "https://www.googleapis.com/plus/v1/people/{$this->people}?key={$this->apiKey}";
        } elseif ($this->social == self::FOLLOWER_FACEBOOK) {
            $this->urlAPI = "https://graph.facebook.com/{$this->people}?access_token={$this->fbAppId}|{$this->fbSecret}";
            $this->attribute = 'likes';
        } elseif ($this->social == self::FOLLOWER_YOUTUBE) {
            $this->urlAPI = "https://www.googleapis.com/youtube/v3/channels?part=statistics&id={$this->people}&key={$this->apiKey}";
            $this->attribute = "subscriberCount";
        }
    }

    /**
     * Method run the widget
     *
     * @return string
     */
    public function run()
    {
        if (!empty($this->urlAPI)) {
            $followers = $this->getResponse($this->urlAPI, false);
        }
        if ($this->social == self::FOLLOWER_YOUTUBE) {
            $followers = $followers->items[0]->statistics;
        }
        return isset($followers) ? Html::encode((int) $followers->{$this->attribute}) : Html::encode(0);
    }


    /**
     * Method get response
     *
     * @param  string  $json_url url to request
     * @param  boolean $use_curl use method to get response by file_get_contents or CURL
     *
     * @return array
     */
    private function getResponse($json_url = '', $use_curl = false)
    {
        if($use_curl){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $json_url);
            $json_data = curl_exec($ch);
            curl_close($ch);
            return json_decode($json_data);
        }else{
            $json_data = @file_get_contents($json_url);
            if($json_data == true){
                return json_decode($json_data);
            }else{ return null;}
        }
    }
}
