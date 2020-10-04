<?php

/**
 * Php Sql
 *
 * MIT License
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @package   bronos\php-sql
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace BronOS\PhpSql\Repository\Cache;


use BronOS\PhpSql\Exception\CacheStorageException;

/**
 * Cache storage interface.
 *
 * @package   bronos\php-sql
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
class ApcuCacheStorage implements CacheStorageInterface
{
    private string $prefix;

    /**
     * ApcuCacheStorage constructor.
     * @param string $prefix
     */
    public function __construct(string $prefix = '')
    {
        $this->prefix = $prefix;
    }

    /**
     * Loads data from storage by key.
     *
     * @param string $key Storage key.
     *
     * @return array
     *
     * @throws CacheStorageException
     */
    public function load(string $key): array
    {
        if (!$this->isExists($key)) {
            throw new CacheStorageException("Cache key [$key] does not exists in the storage.");
        }

        return apcu_fetch($this->extendKey($key));
    }

    /**
     * Stores array under the key in storage.
     *
     * @param string $key   Storage key.
     * @param array  $value Value to be stored.
     *
     * @return bool
     *
     * @throws CacheStorageException
     */
    public function save(string $key, array $value): bool
    {
        $res =  apcu_store($this->extendKey($key), $value);

        if ($res === false) {
            throw new CacheStorageException("Cannot save cache.");
        }

        return $res;
    }

    /**
     * Removes value under the key from storage.
     *
     * @param string $key
     *
     * @return bool
     *
     * @throws CacheStorageException
     */
    public function invalidate(string $key): bool
    {
        $res = apcu_delete($this->extendKey($key));

        if ($res === false) {
            throw new CacheStorageException("Cannot invalidate cache.");
        }

        return $res;
    }

    /**
     * Check whether the key is exists in the storage.
     *
     * @param string $key Key to be checked.
     *
     * @return bool
     */
    public function isExists(string $key): bool
    {
        return apcu_exists($this->extendKey($key));
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private function extendKey(string $key): string
    {
        return $this->prefix . $key;
    }
}