<?php

class VS7_PromoWidget_Model_Adminhtml_Observer
{
    public function saveBannerData($observer)
    {
        $post = Mage::app()->getRequest()->getPost('vs7_promowidget', -1);
        if ($post != '-1') {
            $rule = $observer->getRule();
            $ruleId = $rule->getId();
            if ($ruleId == null) {
                return $this;
            }
            $model = Mage::getModel('vs7_promowidget/banner')->load($ruleId, 'rule_id');
            $model->setRuleId($ruleId);
            $model->setName($post['name']);
            $model->setUrlKey($post['url_key']);
            $model->setText($post['text']);
            $model->setPosition((int)$post['position']);

            $imageName = 'vs7_promowidget_image';
            $image = false;

            if (isset($_FILES[$imageName]['name']) && !empty($_FILES[$imageName]['name'])) {
                try {
                    $uploader = new Varien_File_Uploader($imageName);
                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);
                    $uploader->save(Mage::helper('vs7_promowidget')->getImagePath(), $_FILES[$imageName]['name']);
                    $image = $uploader->getUploadedFileName();
                } catch (Exception $e) {

                }
            } else if (isset($post[$imageName]['delete']) && !empty($post[$imageName]['delete']) && $model->getImage() != null) {
                $filePath = Mage::helper('vs7_promowidget')->getImagePath($model->getData($imageName));
                unlink($filePath);
                $image = null;
            }

            if ($image !== false) {
                $model->setImage($image);
            }

            $model->save();
        }
        return $this;
    }
}