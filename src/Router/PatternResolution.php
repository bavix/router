<?php

namespace Bavix\Router;

interface PatternResolution
{

    /**
     * @param array $methods
     *
     * @return Pattern
     */
    public function methods(array $methods): Pattern;

    /**
     * @return Pattern
     */
    public function any(): Pattern;

    /**
     * @return Pattern
     */
    public function get(): Pattern;

    /**
     * @return Pattern
     */
    public function post(): Pattern;

    /**
     * @return Pattern
     */
    public function put(): Pattern;

    /**
     * @return Pattern
     */
    public function patch(): Pattern;

    /**
     * @return Pattern
     */
    public function delete(): Pattern;

}
