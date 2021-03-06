<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: netborg
 * Date: 10.12.17
 * Time: 14:40
 */

namespace NetborgTeam\P24;

use Illuminate\Contracts\Support\Arrayable;

class ArrayOfRefund implements Arrayable
{
    /**
     * @var array
     */
    protected $data = [];


    /**
     * Adds SingleRefund to array.
     * If another object with provided sessionId already exists,
     * will be replaced by this instance.
     *
     * @param  SingleRefund  $refund
     * @return ArrayOfRefund
     */
    public function add(SingleRefund $refund): self
    {
        if ($this->has($refund)) {
            $this->remove($refund);
        }

        $this->data[] = $refund;
        return $this;
    }

    /**
     * Creates and adds SingleRefund to an array based on values provided.
     *
     * @param  string        $sessionId
     * @param  int           $orderId
     * @param  int           $amount
     * @return ArrayOfRefund
     */
    public function addByKeys(string $sessionId, int $orderId, int $amount): self
    {
        $this->add(new SingleRefund([
            'sessionId' => $sessionId,
            'orderId' => (int) $orderId,
            'amount' => strpos(str_replace(',', '.', $amount), '.')
                ? (int) floor($amount * 100)
                : (int) $amount,
        ]));

        return $this;
    }

    /**
     * Removes refund from array by provided instance or sessionId.
     *
     * @param string|SingleRefund $subject
     */
    public function remove($subject)
    {
        if ($subject instanceof SingleRefund) {
            $sessionId = (string) $subject->sessionId;
        } else {
            $sessionId = (string) $subject;
        }

        foreach ($this->data as $i => $refund) {
            if ((string) $refund->sessionId === $sessionId) {
                unset($this->data[$i]);
            }
        }
    }

    /**
     * Searches and returns SingleRefund by its index in array or sessionId.
     *
     * @param  int|string        $subject
     * @return SingleRefund|null
     */
    public function get($subject): ?SingleRefund
    {
        if (is_numeric($subject) || is_integer($subject)) {
            if (isset($this->data[(int) $subject])) {
                return $this->data[(int) $subject];
            }
        } elseif (is_string($subject)) {
            foreach ($this->data as $i => $refund) {
                if ((string) $refund->sessionId === $subject) {
                    return $refund;
                }
            }
        }

        return null;
    }

    /**
     * Returns `true` if searched refund exists in array or `false` otherwise.
     *
     * @param  SingleRefund|string $subject
     * @return bool
     */
    public function has($subject): bool
    {
        if ($subject instanceof SingleRefund) {
            $sessionId = (string) $subject->sessionId;
        } else {
            $sessionId = (string) $subject;
        }

        foreach ($this->data as $i => $refund) {
            if ((string) $refund->sessionId === $sessionId) {
                return true;
            }
        }

        return false;
    }


    /**
     * Returns an array of refunds.
     *
     * @return array
     */
    public function toArray(): array
    {
        $array = [];
        foreach ($this->data as $refund) {
            $array[] = $refund->toArray();
        }
        return $array;
    }
}
