<?php
namespace RateCard\Model;

class Discount
{
    public $id;
    public $platformId;
    public $conditionalAmount;
    public $discountPercentage;

    public function exchangeArray($data)
    {
        $this->id = ( !empty($data['id']) ) ? $data['id'] : null;
        $this->platformId = ( !empty($data['platform_id']) ) ? $data['platform_id'] : null;
        $this->conditionalAmount = ( !empty($data['conditional_amount']) ) ? $data['conditional_amount'] : null;
        $this->discountPercentage = ( !empty($data['discount_percentage']) ) ? $data['discount_percentage'] : null;
    }

        public function getArrayCopy() {

            return get_object_vars($this);
        }
}