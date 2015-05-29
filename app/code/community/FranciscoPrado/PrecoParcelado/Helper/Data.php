<?php

class FranciscoPrado_PrecoParcelado_Helper_Data extends Mage_Core_Helper_Abstract {

    const XML_PATH_ACTIVE = 'sales/franciscoprado_precoparcelado/active';
    const XML_PATH_SHOW_TABLE = 'sales/franciscoprado_precoparcelado/show_table';
    const XML_PATH_TABLE_TITLE = 'sales/franciscoprado_precoparcelado/table_title';
    const XML_PATH_SHOW_PRICE_IN_PARCELS = 'sales/franciscoprado_precoparcelado/show_price_in_parcels';
    const XML_PATH_MIN_PARCEL_VALUE = 'sales/franciscoprado_precoparcelado/min_quote_value';
    const XML_PATH_MAX_NUMBER_MONTHS = 'sales/franciscoprado_precoparcelado/max_number_months';
    const XML_PATH_INTEREST_VALUE = 'sales/franciscoprado_precoparcelado/interest_value';
    const XML_PATH_TEXT_PATTERN = 'sales/franciscoprado_precoparcelado/text_pattern';
    const XML_PATH_TABLE_TEXT_PATTERN = 'sales/franciscoprado_precoparcelado/text_table_pattern';
    const XML_PATH_ADD_JQUERY = 'sales/franciscoprado_precoparcelado/add_jquery';

    public function isModuleEnabled($moduleName = null) {
        if ((int) Mage::getStoreConfig(self::XML_PATH_ACTIVE, Mage::app()->getStore()) != 1) {
            return false;
        }
        return parent::isModuleEnabled($moduleName);
    }

    public function showTable($store = null) {
        return Mage::getStoreConfig(self::XML_PATH_SHOW_TABLE, $store);
    }

    public function getTableTitle($store = null) {
        return Mage::getStoreConfig(self::XML_PATH_TABLE_TITLE, $store);
    }

    public function showPriceInParcels($store = null) {
        return Mage::getStoreConfig(self::XML_PATH_SHOW_PRICE_IN_PARCELS, $store);
    }

    public function getMinParcelValue($store = null) {
        return (int) Mage::getStoreConfig(self::XML_PATH_MIN_PARCEL_VALUE, $store);
    }

    public function getMaxNumberMonths($store = null) {
        return (int) Mage::getStoreConfig(self::XML_PATH_MAX_NUMBER_MONTHS, $store);
    }

    public function getInterest($store = null) {
        return (float) Mage::getStoreConfig(self::XML_PATH_INTEREST_VALUE, $store);
    }

    public function getText($store = null) {
        return Mage::getStoreConfig(self::XML_PATH_TEXT_PATTERN, $store);
    }

    public function getTableText($store = null) {
        return Mage::getStoreConfig(self::XML_PATH_TABLE_TEXT_PATTERN, $store);
    }

    public function addJquery($store = null) {
        return Mage::getStoreConfig(self::XML_PATH_ADD_JQUERY, $store);
    }

    public function getParcelsValue($value, $interest, $parcel) {
        $interest = bcdiv($interest, 100, 15);
        $E = 1.0;
        $cont = 1.0;

        for ($i = 1; $i <= $parcel; $i++) {
            $cont = bcmul($cont, bcadd($interest, 1, 15), 15);
            $E = bcadd($E, $cont, 15);
        }
        
        $E = bcsub($E, $cont, 15);

        $value = bcmul($value, $cont, 15);
        return bcdiv($value, $E, 15);
    }

    public function getPrice($value) {
        if ($this->isModuleEnabled() && $this->showPriceInParcels()) {

            if ($value > $this->getMinParcelValue()) {
                $finalText = '';

                for ($i = 2; $i <= $this->getMaxNumberMonths(); $i++) {
                    $parcel = $this->getParcelsValue($value, $this->getInterest(), $i);

                    if ($parcel >= $this->getMinParcelValue()) {
                        $price = Mage::helper('core')->currency($parcel, true, false);
                        $replaceText = str_replace('{parcelas}', $i, $this->getText());
                        $finalText = str_replace('{preco}', $price, $replaceText);
                    }
                }

                $returnHtml = sprintf('<span class="precoparcelado-parcels">%s</span>', $finalText);

                return $returnHtml;
            }
        }

        return null;
    }

}
