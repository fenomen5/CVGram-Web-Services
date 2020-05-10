<?php

namespace AppBundle\Service\Validator;

/**
 * Service class for validating fields of user account
 * Class Account
 * @package AppBundle\Repository
 */
class AccountValidator
{

    public function validate($accountValues)
    {
        $errors = [];
        $errors['name'] = $this->validateString($accountValues['name'],true, 4);
        $errors['email'] = $this->validateString($accountValues['email'], true, 6);
        $errors['phone'] = $this->validateString($accountValues['phone'], true,8);
        $errors['user_type'] = $this->validateInt($accountValues['user_type']);
        $errors['password'] = $this->validatePassword($accountValues['password']);
        $errors['passwords'] = $this->validatePasswords(
            $accountValues['user_type'],
            $accountValues['repeat_password']
        );

        foreach ($errors as $key => $value) {
            if (empty($value)) {
                unset($errors[$key]);
            }
        }
        return $errors;
    }

    public function saveAccount()
    {

    }

    /**
     * String validation
     * @param $string
     * @param $checkEmpty
     * @param $minLength
     * @param $maxLength
     */
    protected function  validateString($string, $checkEmpty = true, $minLength = 1, $maxLength = 50)
    {
        if ($checkEmpty && empty($string)) {
            return 'is empty';
        }

        if (mb_strlen($string) < $minLength or mb_strlen($string) > $maxLength) {
            return "length not in range between $minLength and $maxLength";
        }
    }

    /**
     * Email validation
     * @param $string
     * @param $checkEmpty
     * @param $minLength
     * @param $maxLength
     */
    protected function validateEmail($string, $checkEmpty = true, $minLength = 1, $maxLength = 50)
    {
        if ($checkEmpty && empty($string)) {
            return 'is empty';
        }

        if (mb_strlen($string) < $minLength or mb_strlen($string) > $maxLength) {
            return "length not in range between $minLength and $maxLength";
        }
    }

    /**
     * Phone validation
     * @param $string
     * @param $checkEmpty
     * @param $minLength
     * @param $maxLength
     */
    protected function validatePhone($string, $checkEmpty = true, $minLength = 1, $maxLength = 16)
    {
        if ($checkEmpty && empty($string)) {
            return 'is empty';
        }

        if (mb_strlen($string) < $minLength or mb_strlen($string) > $maxLength) {
            return "length not in range between $minLength and $maxLength";
        }
    }


    /**
     * Validate value for int type
     * @param $string
     */
    protected function  validateInt($value)
    {
        if (!is_numeric($value)) {
            return 'value is not numeric';
        }
    }

    /**
     * Password validation
     * @param $string
     * @param $checkEmpty
     * @param int $minLength
     * @param int $maxLength
     * @return string
     */
    protected function  validatePassword($string, $checkEmpty = true, $minLength = 8, $maxLength = 50)
    {
        if ($checkEmpty && empty($string)) {
            return 'is empty';
        }

        if (mb_strlen($string) < $minLength or mb_strlen($string) > $maxLength) {
            return "length not in range between $minLength and $maxLength";
        }

        if (!preg_match('/[^a-zA-Z\d]/', $string)) {
            return "value does not contain special characters";
        }
    }

    /**
     * Passwords validation
     * @param $pass1
     * @param $pass2
     * @return bool|string
     */
    protected function validatePasswords($pass1, $pass2)
    {
        if (empty($pass1) || empty($pass2)) {
            return 'passwords mismatch';
        }
    }
}
