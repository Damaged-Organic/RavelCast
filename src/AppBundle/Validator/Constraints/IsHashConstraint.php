<?php
// AppBundle/Validator/Constraints/IsHashConstraint.php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsHashConstraint extends Constraint
{
    public $message = "stashed_data_package.hash.valid";
}