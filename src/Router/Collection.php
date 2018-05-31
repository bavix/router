<?php

namespace Bavix\Router;

use Bavix\Iterator\Iterator;

class Collection extends Iterator
{

    /**
     * @param array $names
     * @return Collection
     */
    public function names(array $names): self
    {
        foreach ($names as $key => $name) {
            /**
             * @var Pattern $item
             */
            $item = $this->data[$key];
            $item->setName($name);
        }
        return $this;
    }

    /**
     * @param array $names
     * @return Collection
     */
    public function only(array $names): self
    {
        $this->data = \array_filter($this->data, function (string $needle) use ($names) {
            return \in_array($needle, $names, true);
        }, \ARRAY_FILTER_USE_KEY);

        return $this;
    }

    /**
     * @param array $names
     * @return Collection
     */
    public function except(array $names): self
    {
        return $this->only(\array_diff(\array_keys($this->data), $names));
    }

}
