<?php

namespace vova07\base\behaviors;

use Module;
use yii\base\Behavior;
use yii\helpers\HtmlPurifier;

/**
 * Class PurifierBehavior
 * HTMLPurifier behavior.
 *
 * Usage:
 * ```
 * ...
 * 'purifierBehavior' => [
 *     'class' => PurifierBehavior::className(),
 *     'attributes' => [
 *         self::EVENT_BEFORE_VALIDATE => [
 *             'snippet',
 *             'content' => [
 *                 'HTML.AllowedElements' => '',
 *                 'AutoFormat.RemoveEmpty' => true
 *             ]
 *         ]
 *     ],
 *     'textAttributes' => [
 *         self::EVENT_BEFORE_VALIDATE => ['title', 'alias']
 *     ]
 * ]
 * ...
 * ```
 *
 * @property array $attributes Attributes array with settings
 * @property array $textAttributes Text attributes array with settings
 * @property array $purifierOptions Purifier settings
 */
class PurifierBehavior extends Behavior
{
    /**
     * @var array Attributes array
     */
    public $attributes = [];

    /**
     * @var array Text attributes array
     */
    public $textAttributes = [];

    /**
     * @var array Purifier settings
     */
    public $purifierSettings = [
        'AutoFormat.RemoveEmpty' => true,
        'AutoFormat.RemoveEmpty.RemoveNbsp' => true,
        'AutoFormat.Linkify' => true,
        'HTML.Nofollow' => true
    ];

    /**
     * @inheritdoc
     */
    public function attach($owner)
    {
        parent::attach($owner);

        if (!is_array($this->attributes) || empty($this->attributes)) {
            throw new InvalidParamException('Invalid or empty attributes array.');
        }
        if (!empty($this->attributes) && !is_array($this->attributes)) {
            throw new InvalidParamException('Invalid or text attributes array.');
        }
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        $events = [];

        foreach ($this->attributes as $event => $attributes) {
            $events[$event] = 'purify';
        }
        foreach ($this->textAttributes as $event => $attributes) {
            $events[$event] = 'textPurify';
        }

        return $events;
    }

    /**
     * Purify attributes
     *
     * @param Event $event Current event
     */
    public function purify($event)
    {
        $attributes = isset($this->attributes[$event->name]) ? (array)$this->attributes[$event->name] : [];
        if (!empty($attributes)) {
            $purifier = new HtmlPurifier;
            foreach ($attributes as $attribute => $config) {
                if (is_array($config)) {
                    $settings = $config;
                } else {
                    $attribute = $config;
                    $settings = $this->purifierSettings;
                }
                $this->owner->$attribute = $purifier->process($this->owner->$attribute, $settings);
            }
        }
    }

    /**
     * Purify text attributes
     *
     * @param Event $event Current event
     */
    public function textPurify($event)
    {
        $attributes = isset($this->textAttributes[$event->name]) ? (array)$this->textAttributes[$event->name] : [];
        if (!empty($attributes)) {
            $purifier = new HtmlPurifier;
            $settings = [
                'HTML.AllowedElements' => '',
                'AutoFormat.RemoveEmpty' => true,
                'AutoFormat.RemoveEmpty.RemoveNbsp' => true,
            ];
            foreach ($attributes as $attribute) {
                $this->owner->$attribute = $purifier->process($this->owner->$attribute, $settings);
            }
        }
    }
}
