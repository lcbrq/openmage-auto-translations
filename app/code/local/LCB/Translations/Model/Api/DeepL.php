<?php

use GuzzleHttp\Client;

/**
 * @author Tomasz Silpion Gregorczyk <tom@lcbrq.com>
 */
class LCB_Translations_Model_Api_DeepL
{
    /**
     * @var string
     */
    public const XPATH_AUTH_KEY = 'lcb_translations/deepl/auth_key';

    /**
     * @var string
     */
    public const XPATH_ENDPOINT = 'lcb_translations/deepl/mode';

    /**
     * @param string $html
     * @param string $langFrom
     * @param string $langTo
     * @return string
     */
    public function translate(string $html, string $langFrom, string $langTo)
    {
        $translation = '';
        $params = [
            'auth_key' => $this->getAuthKey(),
            'source_lang' => $langFrom,
            'target_lang' => $langTo,
            'text' => $html,
            'tag_handling' => 'html',
        ];

        $client = new Client(['base_uri' => $this->getEndpoint()]);
        $response = $client->request('POST', 'v2/translate', ['form_params' => $params]);
        $result = $response->getBody()->getContents();

        if ($result) {
            $data = json_decode($result, true);
            if (!empty($data['translations'])) {
                foreach ($data['translations'] as $translationData) {
                    $translation = $translationData['text'];
                    break;
                }
            }
        }

        return $translation;
    }

    /**
     * @return string
     */
    public function getAuthKey(): string
    {
        return  Mage::getModel('core/encryption')->decrypt(Mage::getStoreConfig(self::XPATH_AUTH_KEY));
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return Mage::getStoreConfig(self::XPATH_ENDPOINT);
    }
}
