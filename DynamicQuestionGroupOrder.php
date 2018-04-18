<?php

use \LimeSurvey\PluginManager\PluginManager;
use \LimeSurvey\PluginManager\PluginBase;

use \LimeSurvey\Helper\QuestionGroupHelper;

class DynamicQuestionGroupOrder extends PluginBase {

    const SESSION_KEY_OVERRIDE_GROUP_ORDER = 'override_group_order';

    static protected $name = 'Dynamic Question Group Order';
    static protected $description = 'Allows to change the question group order per survey run';
    
    protected $settings = array();
    
    public function __construct(PluginManager $manager, $id) {
        parent::__construct($manager, $id);
        $this->subscribe('beforeSurveyPage');
        $this->subscribe('afterGroupListLoaded');
    }
    
    public function beforeSurveyPage() {
        $aSurveySession = Yii::app()->session->get('survey_' . $this->event->get('surveyId'));
        var_dump("session", $aSurveySession);
        $aGroupOrder = array();
        if (($groupOrderFieldName = array_search('grouporder', array_column($fieldmap, 'title', 'fieldname'))) !== false) {
            if (isset($aSurveySession['startingValues']) && isset($aSurveySession['startingValues'][$groupOrderFieldName])) {
                $aGroupOrder = array_map('intval', explode(',', $aSurveySession['startingValues'][$groupOrderFieldName]));
            }
        }

        if(count($aGroupOrder) == count($aSurveySession['grouplist'])) {
            $aSurveySession[self::SESSION_KEY_OVERRIDE_GROUP_ORDER] = $aGroupOrder;
        }
    }

    public function afterGroupListLoaded() {
        $aGroupList = $this->event->get(QuestionGroupHelper::EVENT_KEY_QUESTION_GROUP_LIST);
        var_dump($aGroupList);
    }
    
}
