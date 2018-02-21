<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Password\Manager;

use InvalidArgumentException;

/**
 * @package ActiveCollab\Authentication\Password
 */
class PasswordManager implements PasswordManagerInterface
{
    const OPENSSL_CRYPT_METHOD = 'aes-256-cbc';

    /**
     * @var string
     */
    private $global_salt;

    /**
     * @param string $global_salt
     */
    public function __construct($global_salt = '')
    {
        $this->global_salt = (string) $global_salt;
    }

    /**
     * {@inheritdoc}
     */
    public function verify($password, $hash, $hashed_with)
    {
        switch ($hashed_with) {
            case self::HASHED_WITH_PHP:
                return password_verify($this->global_salt . $password, $hash);
            case self::HASHED_WITH_PBKDF2:
            case self::HASHED_WITH_SHA1:
                return $this->hash($password, $hashed_with) === $hash;
            default:
                throw new InvalidArgumentException("Hashing mechanism '$hashed_with' is not supported");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hash($password, $hash_with = self::HASHED_WITH_PHP)
    {
        switch ($hash_with) {
            case self::HASHED_WITH_PHP:
                return password_hash($this->global_salt . $password, PASSWORD_DEFAULT);
            case self::HASHED_WITH_PBKDF2:
                return base64_encode($this->pbkdf2($password, $this->global_salt, 1000, 40));
            case self::HASHED_WITH_SHA1:
                return sha1($this->global_salt . $password);
            default:
                throw new InvalidArgumentException("Hashing mechanism '$hash_with' is not supported");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function needsRehash($hash, $hashed_with)
    {
        if ($hashed_with === self::HASHED_WITH_PHP) {
            return password_needs_rehash($hash, PASSWORD_DEFAULT);
        } else {
            return true;
        }
    }

    /**
     * PBKDF2 Implementation (described in RFC 2898).
     *
     * Source: http://www.itnewb.com/tutorial/Encrypting-Passwords-with-PHP-for-Storage-Using-the-RSA-PBKDF2-Standard
     *
     * @param  string $p  password
     * @param  string $s  salt
     * @param  int    $c  iteration count (use 1000 or higher)
     * @param  int    $kl derived key length
     * @param  string $a  hash algorithm
     * @return string derived key
     */
    private function pbkdf2($p, $s, $c, $kl, $a = 'sha256')
    {
        $hl = strlen(hash($a, null, true)); // Hash length
        $kb = ceil($kl / $hl);              // Key blocks to compute
        $dk = '';                           // Derived key

        // Create key
        for ($block = 1; $block <= $kb; ++$block) {

            // Initial hash for this block
            $ib = $b = hash_hmac($a, $s . pack('N', $block), $p, true);

            // Perform block iterations
            for ($i = 1; $i < $c; ++$i) {
                // XOR each iterate

                $ib ^= ($b = hash_hmac($a, $b, $p, true));
            }

            $dk .= $ib; // Append iterated block
        }

        // Return derived key of correct length

        return substr($dk, 0, $kl);
    }

    /**
     * @param $password
     * @return string
     */
    public function encryptPassword($password)
    {
        $password_hash = !empty(getenv('PASSWORD_CRYPT_HASH'))
            ? getenv('PASSWORD_CRYPT_HASH')
            : 'its_hashed';

        $iv_size = openssl_cipher_iv_length(self::OPENSSL_CRYPT_METHOD);
        $iv = openssl_random_pseudo_bytes($iv_size);

        return base64_encode(
            openssl_encrypt(
                $password,
                self::OPENSSL_CRYPT_METHOD,
                $password_hash,
                OPENSSL_RAW_DATA,
                $iv
            )
        ) . ':' . base64_encode($iv);
    }

    /**
     * @param $password
     * @return string
     */
    public function decryptPassword($password)
    {
        $password_hash = !empty(getenv('PASSWORD_CRYPT_HASH'))
            ? getenv('PASSWORD_CRYPT_HASH')
            : 'its_hashed';

        if (empty($password)) {
            throw new InvalidArgumentException('Password argument is required');
        }

        $separated_data = explode(':', $password);

        if (count($separated_data) != 2) {
            throw new InvalidArgumentException('Encryption is not valid');
        }

        return openssl_decrypt(
            base64_decode($separated_data[0], true),
            self::OPENSSL_CRYPT_METHOD,
            $password_hash,
            OPENSSL_RAW_DATA,
            base64_decode($separated_data[1], true)
        );
    }
}
