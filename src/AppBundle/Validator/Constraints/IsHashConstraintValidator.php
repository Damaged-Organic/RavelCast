<?php
// AppBundle/Validator/Constraints/IsHashConstraintValidator.php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint,
    Symfony\Component\Validator\ConstraintValidator;

class IsHashConstraintValidator extends ConstraintValidator
{
    const HASH_PATTERN = "#\\$2y\\$(0[4-9]|[12][0-9]|3[01])\\$[.\\/A-Za-z0-9]{53}#";

    public function validate($value, Constraint $constraint)
    {
        if( !preg_match(self::HASH_PATTERN, $value) )
            $this->context->buildViolation($constraint->message)
                ->addViolation();
    }
}
