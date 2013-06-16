<?php

namespace Cpliakas\Password;

use Phpass\Hash\Adapter\Pbkdf2;
Use Phpass\Hash;

/**
 * Contains helper methods for password hashing and comparison. When
 * instantiated, this class models the hashed version of the password.
 */
class Password
{
    /**
     * The hashed password.
     *
     * @var string
     */
    protected $_hashedPassword;

    /**
     * The default binary logarithm value used in password stretching.
     *
     * @var int
     */
    protected static $_defaultIterationCount = 12;

    /**
     * Sets the default binary logarithm value used in password stretching.
     *
     * @param int $count
     *   Binary logarithm value used in password stretching.
     */
    public static function setDefaultIterationCount($count)
    {
        self::$_defaultIterationCount = $count;
    }

    /**
     * Returns a Hash instance.
     *
     * @param int|null $iteration_count
     *   Binary logarithm value used in password stretching, pass null to use
     *   the default value.
     *
     * @return Hash
     */
    public static function getHashInstance($iteration_count = null)
    {
        if (null === $iteration_count) {
            $iteration_count = self::$_defaultIterationCount;
        }

        $adapter = new Pbkdf2(array(
            'iterationcountlog2' => $iteration_count,
        ));

        return new Hash($adapter);
    }

    /**
     * Constructs a Password object.
     *
     * @param string $hashed
     *   The hashed password.
     */
    public function __construct($hashed)
    {
        $this->_hashedPassword = $hashed;
    }

    /**
     * Returns a hashed value of a raw password.
     *
     * @param string $password
     *   The raw password being hashed.
     * @param int|null $iteration_count
     *   Binary logarithm value used in password stretching, pass null to use
     *   the default value.
     *
     * @return Password
     */
    public static function hash($password, $iteration_count = null)
    {
        $hash = self::getHashInstance($iteration_count);
        return new static($hash->hashPassword($password));
    }

    /**
     * Matches a raw value against it's hashed version.
     *
     * @param string $password
     *   The raw password being matched.
     * @param int|null $iteration_count
     *   Binary logarithm value used in password stretching, pass null to use
     *   the default value.
     *
     * @return bool
     */
    public function match($password, $iteration_count = null)
    {
        $hash = self::getHashInstance($iteration_count);
        return $hash->checkPassword($password, $this->_hashedPassword);
    }

    /**
     * Returns the hashed password.
     */
    public function __toString()
    {
        return $this->_hashedPassword;
    }
}
