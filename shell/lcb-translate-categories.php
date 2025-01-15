<?php

require_once 'abstract.php';

/**
 * Truncate old data from database
 *
 * @author Tomasz Gregorczyk <t.gregorczyk@grupatopex.com>
 */
class LCB_Translate_Categories_Shell extends Mage_Shell_Abstract
{
    /**
     * Run script
     *
     */
    public function run()
    {
        $storeFromId = (int) $this->getArg('store_from');
        $storeToId = (int) $this->getArg('store_to');

        if (!$storeFromId || !$storeToId) {
            return print($this->usageHelp());
        }

        $storeFromLanguage =  substr(Mage::getStoreConfig('general/locale/code', $storeFromId), 0, 2);
        $storeToLanguage =  substr(Mage::getStoreConfig('general/locale/code', $storeToId), 0, 2);

        if ($storeFromLanguage === $storeToLanguage) {
            $this->output(
                sprintf('Store ID %s language is same as store ID %s language (%s %s)', $storeFromId, $storeToId, $storeFromLanguage, $storeToLanguage)
            );
        }

        $categories = Mage::getModel('catalog/category')->getCollection();

        if ($parentId = (int) $this->getArg('parent_id')) {
            $categories->addFieldToFilter('parent_id', $parentId);
        }

        foreach ($categories as $category) {
            $categoryId = $category->getId();
            $storeFromCategory = Mage::getModel('catalog/category')->setStoreId($storeFromId)->load($categoryId);
            $storeToCategory = Mage::getModel('catalog/category')->setStoreId($storeToId)->load($categoryId);

            $storeFromCategoryName = $storeFromCategory->getName();
            $storeToCategoryName = $storeToCategory->getName();

            if ($storeFromCategoryName && $storeToCategoryName && $storeFromCategoryName === $storeToCategoryName) {
                $this->output(sprintf('Translating %s from %s to %s', $storeFromCategoryName, $storeFromLanguage, $storeToLanguage));
                $storeToTranslatedCategoryName = $this->getModel()->translate($storeFromCategoryName, $storeFromLanguage, $storeToLanguage);
                if ($storeToTranslatedCategoryName && $storeFromCategoryName !== $storeToTranslatedCategoryName) {
                    $this->output(sprintf('Translated from %s to %s', $storeFromCategoryName, $storeToTranslatedCategoryName));
                    $storeToCategory->setName($storeToTranslatedCategoryName)->save();
                }
            }
        }
    }

    /**
     * @param string $string
     * @return void
     */
    private function output($string)
    {
        echo "$string\n";
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return Mage::getModel('lcb_translations/api_deepL');
    }

    /**
     * Retrieve Usage Help Message
     */
    public function usageHelp()
    {
        return <<<USAGE
        
   Usage:  php lcb-translate-categories.php [options]

  -h             Short alias for help
  -store_from    Store From ID
  -store_to      Store To ID
  -parent_id     Category Parent ID

USAGE;
    }
}

$shell = new LCB_Translate_Categories_Shell();
$shell->run();
