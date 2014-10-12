<?php

namespace vova07\base;

use Yii;
use yii\base\BootstrapInterface;

/**
 * Base bootstrap class.
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        Yii::$app->set('base',
            [
                'class' => 'vova07\base\components\Base'
            ]
        );
    }
}
