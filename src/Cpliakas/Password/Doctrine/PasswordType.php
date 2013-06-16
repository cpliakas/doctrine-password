<?php

namespace Cpliakas\Password\Doctrine;

use Cpliakas\Password\Password;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Field type mapping for the Doctrine Database Abstraction Layer (DBAL).
 *
 * Password fields are stored as a string in the database and converted back to
 * the Password object when querying.
 */
class PasswordType extends Type
{
    /**
     * @var string
     */
    const NAME = 'password';

    /**
     * {@inheritdoc}
     *
     * @param array                                     $fieldDeclaration
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * {@inheritdoc}
     *
     * @param string|null                               $value
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return new Password($value);
    }

    /**
     * {@inheritdoc}
     *
     * @param Uuid|null                                 $value
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return Password::hash($value);
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }
}
