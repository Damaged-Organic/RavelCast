<?php
// AppBundle/Entity/Traits/DataPackageTrait.php
namespace AppBundle\Entity\Traits;

trait DataPackageTrait
{
    public function flattenSaltArrayResult(array $saltArrayResult)
    {
        $flatArray = [];

        foreach( $saltArrayResult as $value ) {
            $flatArray[] = $value['saltAlpha'];
            $flatArray[] = $value['saltBeta'];
        }

        return $flatArray;
    }
}
