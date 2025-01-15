<?php

class LCB_Translations_Model_System_Config_Source_DeepL
{
    /**
    * @var array
    */
    public const ENDPOINTS = [
        'Free' => 'https://api-free.deepl.com',
        'Pro' => 'https://api.deepl.com',
    ];

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();

        foreach (self::ENDPOINTS as $mode => $endpoint) {
            $options[] = array(
                'label' => $mode,
                'value' => $endpoint
            );
        }

        return $options;
    }
}
